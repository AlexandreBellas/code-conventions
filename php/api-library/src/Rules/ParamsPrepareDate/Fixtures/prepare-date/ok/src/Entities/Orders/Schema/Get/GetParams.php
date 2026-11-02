<?php

namespace Acme\ApiClient\Entities\Orders\Schema\Get;

readonly final class GetParams extends Query
{
    public function __construct(\DateTimeInterface|string|null $createdFrom = null)
    {
        $this->createdFrom = $this->normalizeDate($createdFrom, true);
        parent::__construct(toArray($this));
    }
}
