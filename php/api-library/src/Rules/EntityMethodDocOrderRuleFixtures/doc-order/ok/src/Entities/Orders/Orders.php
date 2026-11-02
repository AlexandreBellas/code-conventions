<?php

namespace Acme\ApiClient\Entities\Orders;

class Orders extends Entity
{
    /**
     * Fetches orders.
     *
     * @return GetResponse
     * @throws AcmeApiException|AcmeInternalException
     * @see https://docs.example.com/reference#/Orders/get_orders
     */
    public function get(): GetResponse
    {
        return GetResponse::fromResponse($response);
    }
}
