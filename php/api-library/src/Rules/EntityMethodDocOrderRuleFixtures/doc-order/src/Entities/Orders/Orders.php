<?php

namespace Acme\ApiClient\Entities\Orders;

class Orders extends Entity
{
    /**
     * Fetches orders.
     *
     * @see https://docs.example.com/reference#/Orders/get_orders
     * @return GetResponse
     */
    public function get(): GetResponse
    {
        return GetResponse::fromResponse($response);
    }
}
