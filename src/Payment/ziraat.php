<?php

namespace aghaeian\ziraat\Payment;

use Webkul\Payment\Payment\Payment;

class ziraat extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'ziraat';

    /**
    * Get redirect url.
    *
    * @var string
    */
    public function getRedirectUrl()
    {
        return route('ziraat.payment.checkout');        
    }
}
