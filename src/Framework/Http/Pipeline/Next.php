<?php

namespace Framework\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Next
{
    private $default;
    private $queue;
    private $response;

    public function __construct(\SplQueue $queue, ResponseInterface $response, callable $default)
    {
        $this->default  = $default;
        $this->queue    = $queue;
        $this->response = $response;
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

        return $current($request, $this->response, function (ServerRequestInterface $request) {
            return $this($request);
        });
    }
}