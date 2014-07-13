<?php

namespace Bh\Bundle\Services;

use Braintree_Configuration;
use Braintree_ClientToken;
use Braintree_Transaction;

class DonateService
{
    public function __construct($id, $public_key, $private_key)
    {
        Braintree_Configuration::environment('sandbox');
        Braintree_Configuration::merchantId($id);
        Braintree_Configuration::publicKey($public_key);
        Braintree_Configuration::privateKey($private_key);
    }

    public function getToken()
    {
        return Braintree_ClientToken::generate();
    }

    public function pay($nonce, $user, $amount)
    {
        $result = Braintree_Transaction::sale(array(
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
        ));
        $result = Braintree_Transaction::submitForSettlement($result->transaction->id);
        if ($result->success) {
            $points = (int) ($amount / 10);
            $user->setPoints($user->getPoints() + $points);
            return $points;
        }
        return print_r($result->errors, true);
    }
}

