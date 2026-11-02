<?php

interface ResponseContract
{
    public static function fromResponse(): void;
}

class BaseResponse
{
    public static function fromRules(): void
    {
    }
}
