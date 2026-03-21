<?php

namespace App\Services;

class KkiapayService
{
    public function verify(string $transactionId): bool
    {
        $kkiapay = new \Kkiapay\Kkiapay(
            config('kkiapay.public_key'),
            config('kkiapay.private_key'),
            config('kkiapay.secret'),
            config('kkiapay.sandbox', true),
        );

        $result = $kkiapay->verifyTransaction($transactionId);

        return ($result->status ?? '') === 'SUCCESS';
    }

    public function refund(string $transactionId): bool
    {
        $kkiapay = new \Kkiapay\Kkiapay(
            config('kkiapay.public_key'),
            config('kkiapay.private_key'),
            config('kkiapay.secret'),
            config('kkiapay.sandbox', true),
        );

        try {
            $kkiapay->refundTransaction($transactionId);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}