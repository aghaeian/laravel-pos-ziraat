<?php

namespace aghaeian\ziraat\Http\Controllers;

use ziraat\Options;

class ziraatConfig
{

    public function options()
    {
        $options = new Options();
	$public = core()->getConfigData('sales.payment_methods.ziraat.client_id');
        $public = core()->getConfigData('sales.payment_methods.ziraat.api_user');
        $secret = core()->getConfigData('sales.payment_methods.ziraat.store_key');
        $secret = core()->getConfigData('sales.payment_methods.ziraat.api_user_password');
        $options->setClientId($public);
	$options->setApiUser($public);
        $options->setStoreKey($secret);
        $options->setApiUserPassword($secret);
        
        $baseUrl = $this->environment();
        $options->setBaseUrl($baseUrl);
        
        return $options;
    }

    /**
     * Setting up and Returns ziraat SDK environment with İyzico Access credentials.
     * For demo purpose, we are using SandboxEnvironment. In production this will be
     * ProductionEnvironment.
     */
	public function environment()
    { 
        $ziraatMode = core()->getConfigData('sales.payment_methods.ziraat.sandbox');

        if ($ziraatMode) {			
            return "https://sanalpos2.ziraatbank.com.tr/fim/est3Dgate";
        }

        return "https://sanalpos2.ziraatbank.com.tr/fim/est3Dgate";
    }
}
