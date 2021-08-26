<?php

namespace Wenhsing\UrlSign\Laravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Wenhsing\UrlSign\UrlSign;

class UrlSignMiddleware
{
    protected $urlSign;

    public function __construct(UrlSign $urlSign)
    {
        $this->urlSign = $urlSign;
    }

    public function handle($request, Closure $next)
    {
        if ($this->urlSign->verify($this->getUri($request), $request->query())) {
            return $next($request);
        }
        return $this->errorResponse($request, $next);
    }

    public function getUri($request)
    {
        $uri = $request->getPathInfo();
        if ('/' !== $uri) {
            return trim($uri, '/');
        }

        return $uri;
    }

    public function errorResponse($request, $next)
    {
        throw new HttpException(401);
    }
}
