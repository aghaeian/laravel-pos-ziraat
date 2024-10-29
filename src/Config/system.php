<?php

return [
    [
        'key' => 'sales.payment_methods.ziraat',
        'name' => 'ziraat',
		'info' => 'ziraat::app.admin.system.info',
        'sort' => 5,
        'fields' => [
            [
                'name' => 'title',
                'title' => 'ziraat::app.admin.system.title',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'description',
                'title' => 'ziraat::app.admin.system.description',
                'type' => 'textarea',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'client_id',
                'title' => 'ziraat::app.admin.system.client-id',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name'          => 'api_user',
                'title'         => 'ziraat::app.admin.system.api-user',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],[
                'name' => 'store_key',
                'title' => 'ziraat::app.admin.system.store-key',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name'          => 'api_user_password',
                'title'         => 'ziraat::app.admin.system.api-user-password',
                'type'          => 'password',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],/* [
                'name'          => 'allowed_installments',
                'title'         => 'ziraat::app.admin.system.allowed-installments',
                'type'			=> 'text',
                'validation'	=> 'numeric',
				'default_value' => '0',
                'channel_based' => false,
                'locale_based'  => true,
				'default_value' => '1',
            ], */[
                'name' => 'sandbox',
                'title' => 'ziraat::app.admin.system.sandbox',
                'type' => 'boolean',
                'channel_based' => false,
                'locale_based'  => true
            ], [
                'name' => 'status',
                'title' => 'ziraat::app.admin.system.status',
                'type' => 'boolean',                
                'validation' => 'required',
                'channel_based' => false,
                'locale_based'  => true
            ], [
                'name' => 'sort',
                'title' => 'ziraat::app.admin.system.sort',
                'type' => 'select',
                'channel_based' => false,
                'locale_based'  => true,
				'options' => [
                    [
                        'title' => '1',
                        'value' => '1',
                    ], [
                        'title' => '2',
                        'value' => '2',
                    ], [
                        'title' => '3',
                        'value' => '3',
                    ], [
                        'title' => '4',
                        'value' => '4',
                    ], [
                        'title' => '5',
                        'value' => '5',
                    ],
			[
                        'title' => '6',
                        'value' => '6',
                    ],
			[
                        'title' => '7',
                        'value' => '7',
                    ],
                ],
            ],			
        ],
    ],
   
];
