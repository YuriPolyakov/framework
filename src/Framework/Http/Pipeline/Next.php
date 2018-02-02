<?php

namespace Framework\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Next
{
    private $default;
    private $queue;

    public function __construct(\SplQueue $queue, callable $default)
    {
        $this->default = $default;
        $this->queue   = $queue;
    }

    /**
     * Рекурсивный проход по элементам в очереди
     *
     * @param ServerRequestInterface $request
     * @param callable $default - Последний посредник
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return ($this->default)($request);
        }

        $current = $this->queue->dequeue();

        return $current($request, function (ServerRequestInterface $request) {
            return $this($request);
        });
    }
}