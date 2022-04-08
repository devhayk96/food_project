<?php

namespace App\Entities;

use App\AbstractSerializable;

class UberEatsCredentials extends AbstractOrderSourceCredentials
{
    public string $restaurantId;

    public string $clientId;

    public string $clientSecret;

    public UberEatsWallet $wallet;

    static public function make(array $values = []): AbstractOrderSourceCredentials
    {
        return new UberEatsCredentials($values);
    }

    public static function initBlank():UberEatsCredentials
    {
        $data = new UberEatsCredentials();
        $data->restaurantId = "";
        $data->clientId = "";
        $data->clientSecret = "";
        $data->wallet = new UberEatsWallet();
        return $data;
    }

    public function init(array $values = []): AbstractSerializable
    {
        $values['wallet'] = isset($values['wallet'])
            ? new UberEatsWallet($values['wallet'])
            : new UberEatsWallet();

        return parent::init($values);
    }

    public function toArray(): array
    {
        return [
            'restaurantId' => $this->restaurantId,
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'wallet' => $this->wallet->toArray()
        ];
    }
}
