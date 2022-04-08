<?php

namespace App\Entities;

class ThuisbezorgdCredentials extends AbstractOrderSourceCredentials
{
    public string $restaurantId;

    public string $username;

    public string $password;

    public string $apiKey;

    static public function make(array $values = []): AbstractOrderSourceCredentials
    {
        return new ThuisbezorgdCredentials($values);
    }

    public static function initBlank():ThuisbezorgdCredentials
    {
        $data = new ThuisbezorgdCredentials();
        $data->restaurantId = "";
        $data->username = "";
        $data->password = "";
        $data->apiKey = "";
        return $data;
    }
}
