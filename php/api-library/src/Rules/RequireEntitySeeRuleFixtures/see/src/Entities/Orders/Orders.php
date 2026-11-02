<?php

namespace Acme\ApiClient\Entities\Orders;

class Orders extends Entity
{
    public function get(): GetResponse
    {
        return GetResponse::fromResponse($response);
    }
}
