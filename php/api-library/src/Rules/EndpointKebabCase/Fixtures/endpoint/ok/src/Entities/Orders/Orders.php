<?php

namespace Acme\ApiClient\Entities\Orders;

class Orders extends Entity
{
    public function find(int $id): FindResponse
    {
        return new Request(endpoint: "orders/$id/tipos");
    }
}
