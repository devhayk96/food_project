<?php

namespace App\Entities;

use App\AbstractSerializable;
use App\Models\Order;

/**
 * Class OrderInTransit
 *
 * This class describe an order entity in transit from when it's downloaded from a source (e.g. thuisbezorgd, uber eats)
 * to when become an Order or an OrderInError
 *
 * @package App\Entities
 */
class OrderInTransit extends AbstractSerializable
{
    public array $raw;

    public array $messages;

    public array $properties;

    public ?Order $order;

    public function addMessage(string $message) : self
    {
        $this->messages[] = $message;
        return $this;
    }

    static public function make(array $values = []): AbstractSerializable
    {
        return new OrderInTransit($values);
    }
}
