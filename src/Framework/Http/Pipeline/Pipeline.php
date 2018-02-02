<?php

namespace Framework\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Pipeline
{
    private $queue;

    /**
     * Инициализация класса для работы с очередью
     */
    public function __construct()
    {
        $this->queue = new \SplQueue();
    }

    /**
     * Запуск рекурсии
     *
     * @param ServerRequestInterface $request
     * @param callable $next - Последний посредник
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $delegate = new Next(clone $this->queue, $next);

        return $delegate($request);
    }

    /**
     * Добавление посредника в очередь
     *
     * @param callable $middleware
     */
    public function pipe(callable $middleware): void
    {
        $this->queue->enqueue($middleware);
    }
}