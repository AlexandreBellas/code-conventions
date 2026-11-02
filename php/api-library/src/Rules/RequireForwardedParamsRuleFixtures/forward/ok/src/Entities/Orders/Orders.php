<?php

namespace Acme\ApiClient\Entities\Orders;

class Orders extends Entity
{
    public function get(GetParams|array|null $params = null): GetResponse
    {
        $response = $this->repository->index(
            new Request(endpoint: "orders", queryParams: $params)
        );

        return GetResponse::fromResponse($response);
    }
}
