<?php

namespace aghaeian\ziraat\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\Sales\Repositories\InvoiceRepository;
use Illuminate\Http\Request;

use aghaeian\ziraat\Http\Controllers\ziraatConfig;
use ziraat\Model\Address;
use ziraat\Model\BasketItem;
use ziraat\Model\BasketItemType;
use ziraat\Model\Buyer;
use ziraat\Model\CheckoutFormInitialize;
use ziraat\Model\Locale;
use ziraat\Model\PaymentGroup;
use ziraat\Options;
use ziraat\Model\CheckoutForm;
use ziraat\Request\RetrieveCheckoutFormRequest;
use ziraat\Request\CreateCheckoutFormInitializeRequest;

class ziraatController extends Controller
{
    /**
     * OrderRepository $orderRepository
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;
    /**
     * InvoiceRepository $invoiceRepository
     *
     * @var \Webkul\Sales\Repositories\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(OrderRepository $orderRepository,  InvoiceRepository $invoiceRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function checkoutWithziraat(Request $request)
    {   
        $cart = Cart::getCart();
        $cartbillingAddress = $cart->billing_address;
		
        $checkoutToken = $request->session()->get('_token');
		
        /** @var ziraatApiClient $api */				
        # create request class
        $api = new CreateCheckoutFormInitializeRequest();
        $api->setLocale($request->getLocale());
        $api->setConversationId($checkoutToken);
        //$api->setPrice("1");
        //$api->setPaidPrice("1.2");
        $currency = $cart->cart_currency_code;
        if($currency == "TRY") $currency = "TL";
        $api->setCurrency(constant('ziraat\Model\Currency::' . $currency));
        $api->setBasketId($cart->id);
        $api->setPaymentGroup(PaymentGroup::PRODUCT);
        $api->setCallbackUrl(request()->getSchemeAndHttpHost() . "/ziraat-payment-callback/" . $checkoutToken);
        $api->setEnabledInstallments(array(3, 6, 9, 12));
		
        $buyer = new Buyer();
        if( $cartbillingAddress->customer_id ) {
		    $buyer->setId($cartbillingAddress->customer_id);
        } else {
            $buyer->setId($cart->id);
        }
        $buyer->setName($cartbillingAddress->first_name);
        $buyer->setSurname($cartbillingAddress->last_name);
        $buyer->setGsmNumber($cartbillingAddress->phone);
        $buyer->setEmail($cartbillingAddress->email);
        $buyer->setIdentityNumber("74300864791");
        //$buyer->setLastLoginDate(date('Y-m-d H:i:s'));
        //$buyer->setRegistrationDate(date('Y-m-d H:i:s'));
        $buyer->setRegistrationAddress($cartbillingAddress->address);
        $buyer->setIp($request->ip());
        $buyer->setCity($cartbillingAddress->city);
        $buyer->setCountry($cartbillingAddress->country);
        $buyer->setZipCode($cartbillingAddress->postcode);
        $api->setBuyer($buyer);

        $shippingAddress = new Address();
        $shippingAddress->setContactName($cartbillingAddress->first_name . ' ' . $cartbillingAddress->last_name);
        $shippingAddress->setCity($cartbillingAddress->city);
        $shippingAddress->setCountry($cartbillingAddress->country);
        $shippingAddress->setAddress($cartbillingAddress->address);
        $shippingAddress->setZipCode($cartbillingAddress->postcode);		
		/*
		if ( ! $cart->billing_address->use_for_shipping ) {
			$cartshippingAddress = $cart->shipping_address;
			$shippingAddress->setContactName($cartshippingAddress->first_name . ' ' . $cartshippingAddress->last_name);
			$shippingAddress->setCity($cartshippingAddress->city);
			$shippingAddress->setCountry($cartshippingAddress->country);
			$shippingAddress->setAddress($cartshippingAddress->address);
			$shippingAddress->setZipCode($cartshippingAddress->postcode);
		}*/		
        $api->setShippingAddress($shippingAddress);		
			
        $billingAddress = new Address();
        $billingAddress->setContactName($cartbillingAddress->name);
        $billingAddress->setCity($cartbillingAddress->city);
        $billingAddress->setCountry($cartbillingAddress->country);
        $billingAddress->setAddress($cartbillingAddress->address);
        $billingAddress->setZipCode($cartbillingAddress->postcode);
        $api->setBillingAddress($billingAddress);
				
        $basketItems = array();
        $amountTotal = 0;                        
        for ($i =0; $i < $cart->items_count; $i++) {
            $BasketItem = new BasketItem();
            $BasketItem->setId($cart->items[$i]->id);
            $BasketItem->setName($cart->items[$i]->name);
            $BasketItem->setCategory1($cart->items[$i]->type);
            $BasketItem->setCategory2('category 2');
            $BasketItem->setItemType(BasketItemType::VIRTUAL);                        
            $BasketItem->setPrice(number_format((float)$cart->items[$i]->base_total, 2, '.', ''));
            $basketItems[$i] = $BasketItem;            
        }
        
        $api->setBasketItems($basketItems);
        $api->setPrice(number_format((float)$cart->sub_total, 2, '.', ''));
        $api->setPaidPrice(number_format((float)$cart->grand_total, 2, '.', ''));
         
        # make request
        $checkoutFormInitialize = CheckoutFormInitialize::create($api, (new ziraatConfig)->options());

        if( $checkoutFormInitialize->getStatus() != "success" ) {
            session()->flash('error', $checkoutFormInitialize->geterrorCode() . ", message: " . $checkoutFormInitialize->geterrorMessage());
            return redirect()->route('shop.checkout.cart.index'); 
        } else {					
            //$request->session()->put('paymentcontent_msg', $checkoutFormInitialize->getCheckoutFormContent());
            $paymentcontent_msg = $checkoutFormInitialize->getCheckoutFormContent();        
            return view('ziraat::ziraat-payment-callback')->with(compact('paymentcontent_msg'));
        }
    }

    /**
     * payment callback
     */
	public function paymentCallback(Request $request)
    {   
        try {
            /**
             * @var ziraatApiClient $api
             */
            $api = new RetrieveCheckoutFormRequest();
            $api->setLocale($request->getLocale());
            $api->setConversationId($request->session()->get('_token'));
            $api->setToken($request->input('token'));
            
            # make request
            $checkoutForm = CheckoutForm::retrieve($api, (new ziraatConfig)->options());
            
            if(strtolower($checkoutForm->getPaymentStatus()) !== \ziraat\Model\Status::SUCCESS) {
                session()->flash('error', 'ziraat payment either cancelled or transaction failure.');
                return redirect()->route('shop.checkout.cart.index');                
            }
        } catch (SignatureVerificationError $e) {
                $success = false;
                $error = 'ziraat Error : ' . $e->getMessage();
        }

        $cart = Cart::getCart();
        $data = (new OrderResource($cart))->jsonSerialize(); // new class v2.2
        $order = $this->orderRepository->create($data);
        //$order = $this->orderRepository->create(Cart::prepareDataForOrder()); // removed for v2.2
        $this->orderRepository->update(['status' => 'processing'], $order->id);
        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }
        Cart::deActivateCart();
        session()->flash('order_id', $order->id);
        // Order and prepare invoice
        return redirect()->route('shop.checkout.onepage.success');
    }	
    
    /**
     * Prepares order's invoice data for creation.
     *
     * @return array
     */
    protected function prepareInvoiceData($order)
    {
        $invoiceData = ["order_id" => $order->id,];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}
