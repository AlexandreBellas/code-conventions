<?php

namespace Acme\ApiClient\Repositories;

interface ApiRepository
{
    public function index(): void;
}

class ClientRepository implements ApiRepository
{
    /**
     * @inheritDoc
     */
    public function index(): void
    {
    }
}
