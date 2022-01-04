<?php

namespace Aloefflerj\FedTheDog\Test;

class UserClass
{
    public function __construct($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }
}