<?php

namespace Framework\Http;

class Request
{
    public function getCookies(): array
    {
        return $_COOKIE;
    }

    public function getQueryParrams()
    {
        return $_GET;
    }

    public function getParsedBody()
    {
        return $_POST ?: null;
    }
}