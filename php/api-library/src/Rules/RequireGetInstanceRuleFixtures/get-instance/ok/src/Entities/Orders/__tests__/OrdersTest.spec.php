<?php

namespace Tests\Unit\Acme\ApiClient\Entities\Orders;

class OrdersTest extends TestCase { private function getInstance(ApiRepository $repository): Orders { return new Orders($repository); } }
