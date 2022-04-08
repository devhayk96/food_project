<?php

namespace App\Entities;

use App\AbstractInitializable;
use App\AbstractSerializable;
use App\Exceptions\EntityException;
use App\Locale\PoshubLocale;
use Carbon\Carbon;

class UberEatsToken extends AbstractSerializable
{
    public ?string $accessToken = null;

    public ?string $expiresIn = null;

    public ?string $tokenType = null;

    public ?string $scope = null;

    public ?string $createdAt = null;

    public ?string $expiresAt = null;

    public bool $autoRenew = false;

    /**
     * @param  array                $values
     * @return AbstractSerializable
     * @throws EntityException
     */
    public static function make(array $values = []): AbstractSerializable
    {
        if (array_key_exists('accessToken', $values) &&
            array_key_exists('expiresIn', $values) &&
            array_key_exists('tokenType', $values) &&
            empty($values['scope']) === false &&
            in_array($values['scope'], UberEatsWallet::$scopes) === true &&
            array_key_exists('createdAt', $values) &&
            array_key_exists('expiresAt', $values) &&
            array_key_exists('autoRenew', $values)) {
            return new UberEatsToken($values);
        }
        throw new EntityException("Malformed uber eats token: " . serialize($values));
    }

    /**
     * @param  string               $scope
     * @param  bool                 $autoRenew
     * @return UberEatsToken
     */
    public static function makeFromEmpty(string $scope, bool $autoRenew): UberEatsToken
    {
        return new UberEatsToken([
            'accessToken' => '',
            'expiresIn' => '',
            'tokenType' => '',
            'scope' => $scope,
            'createdAt' => '',
            'expiresAt' => '',
            'autoRenew' => $autoRenew
        ]);
    }

    /**
     * @param  array                $response
     * @param  bool                 $autoRenew
     * @return UberEatsToken
     * @throws EntityException
     */
    public static function makeFromResponse(array $response, bool $autoRenew): UberEatsToken
    {
        if (empty($response['access_token']) ||
            empty($response['expires_in']) ||
            empty($response['token_type']) ||
            empty($response['scope'])) {
            throw new EntityException("Malformed uber eats response token: " . serialize($response));
        }

        $locale = new PoshubLocale();
        $now = $locale->getCarbonNowSystemTz();

        return new UberEatsToken([
            'accessToken' => $response['access_token'],
            'expiresIn' => $response['expires_in'],
            'tokenType' => $response['token_type'],
            'scope' => $response['scope'],
            'createdAt' => $now->format($locale->poshubSystemDtFormat),
            'expiresAt' => $now->addSeconds((int)$response['expires_in'])->format($locale->poshubSystemDtFormat),
            'autoRenew' => $autoRenew
        ]);
    }
}
