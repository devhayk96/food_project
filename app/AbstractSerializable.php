<?php

namespace App;

abstract class AbstractSerializable extends AbstractInitializable implements SerializableInterface
{
    abstract static public function make(array $values = []): AbstractSerializable;

    public function initFromJson(string $json): AbstractSerializable
    {
        $array = json_decode($json, true);
        return $this->init($array);
    }

    public function initFromSerialized(string $serialized): AbstractSerializable
    {
        $array = unserialize($serialized);
        return $this->init($array);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function serialize(): string
    {
        return serialize($this->toArray());
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
