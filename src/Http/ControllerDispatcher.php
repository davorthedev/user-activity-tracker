<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Middleware\RequestHandler;

final class ControllerDispatcher implements RequestHandler
{
    /**
     * @param array<class-string, callable()> $controllers
     */
    public function __construct(private readonly array $controllers)
    {
    }

    public function handle(Request $request): Response
    {
        [$class, $method] = $request->getRoute()->getHandler();
        if (!isset($this->controllers[$class])) {
            throw new \LogicException(sprintf('Controller class not registered "%s"', $class));
        }
        $controller = ($this->controllers[$class])();

        return $controller->{$method}($request);
    }
}
