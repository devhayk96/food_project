<?php


namespace App;


interface SerializableInterface
{
    /**
     * Return an array which can be used to rehydrate the Entity
     *
     * @return array
     */
    public function toArray() : array;

    /**
     * Serialize the toArray result.
     *
     * @return string
     */
    public function serialize() : string;
}
