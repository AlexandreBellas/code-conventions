<?php

namespace Acme\ApiClient\Entities\Orders;

class Orders extends Entity
{
    /**
     * @throws AcmeApiException|AcmeInternalException
     */
    public function get(): GetResponse
    {
        $response = $this->repository->index(new Request(endpoint: "orders"));

        return GetResponse::fromResponse($response);
    }
}
