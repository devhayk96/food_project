<?php


namespace App\Entities;


class FoodyxCredentials extends AbstractOrderSourceCredentials
{
    public string $token;

    static public function make(array $values = []): AbstractOrderSourceCredentials
    {
        return new FoodyxCredentials($values);
    }
}
