<?php

namespace App;

abstract class AbstractInitializable
{
    public function __construct(array $values = [])
    {
        if (empty($values) === false) {
            $this->init($values);
        }
    }

    public function init(array $values): AbstractInitializable
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
        return  $this;
    }
}
