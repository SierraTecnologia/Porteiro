<?php

namespace Porteiro\Contracts;

interface User
{
    public function role();

    public function hasRole($name);

    public function setRole($name);

    public function hasPermission($name);
}
