<?php

namespace Acme\ApiClient\Entities\Orders;

class Orders extends Entity
{
    public function get(): GetResponse
    {
        $response = $this->repository->index(new Request(endpoint: "orders"));

        return GetResponse::fromResponse($response);
    }
}
