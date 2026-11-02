<?php

namespace Acme\ApiClient\Entities\Orders\Schema\Get;

readonly final class GetParams extends Query
{
    public function __construct(public ?int $pagina = null)
    {
        parent::__construct(toArray($this));
    }
}
