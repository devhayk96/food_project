<?php

namespace App\Entities;

use App\AbstractInitializable;
use App\AbstractSerializable;
use App\Exceptions\EntityException;

class UberEatsWallet extends AbstractSerializable
{
    static public array $scopes = [
        'eats.store',
        'eats.store.status.write',
        'eats.order',
        'eats.store.orders.read',
//        'eats.store.orders.restaurantdelivery.status',
//        'eats.store.status.read',
//        'eats.store.orders.cancel',
//        'eats.report',
//        'eats.pos_provisioning'
    ];

    protected array $wallet;

    public function __construct(array $values = [])
    {
        if (empty($values)) {
            foreach (self::$scopes as $scope) {
                $token = UberEatsToken::makeFromEmpty($scope, false);
                $this->setToken($scope, $token);
            }
        }
        parent::__construct($values);
    }

    static public function make(array $values = []): AbstractSerializable
    {
        return new UberEatsWallet($values);
    }

    /**
     * @param  array                 $values
     * @return AbstractInitializable
     * @throws EntityException
     */
    public function init(array $values = []): AbstractInitializable
    {
        return $this->setWalletFromArray($values);
    }

    /**
     * @param  array           $wallet
     * @return $this
     * @throws EntityException
     */
    public function setWalletFromArray(array $wallet): self
    {
        $usedScopes = self::$scopes;
        foreach ($wallet as $scope => $arrayToken) {
            /**
             * @var UberEatsToken $token
             */
            $token = UberEatsToken::make($arrayToken);
            $this->setToken($scope, $token);
            $key = array_search($scope, $usedScopes);
            unset($usedScopes[$key]);
        }
        if (!empty($usedScopes)) {
            throw new EntityException("UberEats Invalid Wallet");
        }
        return $this;
    }

    /**
     * @param  string        $scope
     * @param  UberEatsToken $token
     * @return $this
     * @throws EntityException
     */
    public function setToken(string $scope, UberEatsToken $token): self
    {
        self::checkScope($scope);
        $this->wallet[$scope] = $token;
        return $this;
    }

    /**
     * @param  string          $scope
     * @return UberEatsToken
     * @throws EntityException
     */
    public function getToken(string $scope): UberEatsToken
    {
        self::checkScope($scope);
        return $this->wallet[$scope];
    }

    /**
     * @param  string          $scope
     * @throws EntityException
     */
    public static function checkScope(string $scope)
    {
        if (in_array($scope, self::$scopes) === false) {
            throw new EntityException("UberEatsWallet::setToken Error - invalid scope");
        }
    }

    public function toArray(): array
    {
        $array = [];
        /**
         * @var UberEatsToken $token
         */
        foreach ($this->wallet as $scope => $token) {
            $array[$scope] = $token->toArray();
        }
        return $array;
    }

}
