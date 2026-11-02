<?php

namespace Acme\ApiClient\Entities\Orders;

class Orders extends Entity { /** @return GetResponse */ public function get(): GetResponse { return GetResponse::fromResponse($response); } }
