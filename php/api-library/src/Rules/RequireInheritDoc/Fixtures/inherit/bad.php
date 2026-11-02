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

class GetResponse extends BaseResponse implements ResponseContract
{
    public static function fromResponse(): void
    {
    }

    public static function fromRules(): void
    {
    }
}
