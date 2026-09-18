<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Exception\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Shared\Logger;
use App\View\Renderer;

final class CatchErrors implements Middleware
{
    public function __construct(
        private readonly Renderer $renderer,
        private readonly Logger $logger,
    ) {
    }

    public function process(Request $request, RequestHandler $next): Response
    {
        try {
            return $next->handle($request);
        } catch (HttpException $e) {
            $statusCode = $e->getStatusCode();
            if ($statusCode >= 500) {
                $this->logger->error(
                    'HTTP error',
                    [
                        'method' => $request->getMethod(),
                        'path' => $request->getPath(),
                        'exception' => $e,
                    ]
                );
            }
            $response = $this->errorPage($statusCode);
            foreach ($e->getHeaders() as $name => $value) {
                $response = $response->withHeader($name, $value);
            }

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error(
                'Unhandled exception',
                [
                    'method' => $request->getMethod(),
                    'path' => $request->getPath(),
                    'exception' => $e,
                ]
            );

            return $this->errorPage(500);
        }
    }

    private function errorPage(int $status): Response
    {
        $messages = [
            403 => 'You do not have access to this page.',
            404 => 'That page does not exist.',
            405 => 'Method is not allowed.',
            500 => 'Something went wrong.',
        ];
        $body = $this->renderer->page(
            'error',
            [
                'status' => $status,
                'message' => $messages[$status] ?? 'Request failed.',
            ],
            sprintf('Error %d', $status)
        );

        return Response::html($body, $status);
    }
}
