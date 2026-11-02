<?php

namespace Acme\ApiClient\Repositories;

interface ApiRepository
{
    public function index(): void;
}

class ClientRepository implements ApiRepository
{
    public function __construct()
    {
    }

    public function index(): void
    {
    }

    private function buildResponseBody(): void
    {
    }
}
