<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ETagResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldProcess($request, $response)) {
            $etag = '"' . md5($response->getContent()) . '"';
            $response->headers->set('ETag', $etag);
            $response->headers->set('Cache-Control', 'private, must-revalidate');

            if ($request->headers->get('If-None-Match') === $etag) {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        }

        return $response;
    }

    private function shouldProcess(Request $request, Response $response): bool
    {
        return $request->isMethod('GET')
            && $response->isSuccessful()
            && !$response->headers->has('ETag')
            && $response->getContent() !== false;
    }
}
