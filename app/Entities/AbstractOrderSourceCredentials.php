<?php

namespace App\Entities;

use App\AbstractSerializable;
use App\SerializableInterface;

abstract class AbstractOrderSourceCredentials extends AbstractSerializable implements SerializableInterface
{
    abstract static public function make(array $values = []): AbstractOrderSourceCredentials;
}
