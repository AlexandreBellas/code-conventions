<?php

namespace Acme\ApiClient;

use Acme\ApiClient\Entities\Orders\Orders;

/**
 * @property Orders $orders
 */
class Client
{
    public function __get(string $name)
    {
        return match ($name) {
            'orders' => $this->getModule(Orders::class),
        };
    }
}
