<?php

namespace Acme\ApiClient\Entities\Orders;

/**
 * Orders resource.
 *
 * @see https://docs.example.com/reference#/Orders
 */
class Orders extends Entity
{
    /**
     * Fetches orders.
     *
     * @see https://docs.example.com/reference#/Orders/get_orders
     */
    public function get(): GetResponse
    {
        return GetResponse::fromResponse($response);
    }
}
