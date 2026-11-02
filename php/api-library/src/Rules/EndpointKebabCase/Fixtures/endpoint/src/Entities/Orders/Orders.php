<?php

namespace Acme\ApiClient\Entities\Orders;

class Orders extends Entity
{
    public function get(): GetResponse
    {
        return new Request(endpoint: "ordersTipos");
    }
}
