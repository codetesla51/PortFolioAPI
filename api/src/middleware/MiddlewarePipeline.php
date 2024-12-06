<?php
namespace Middleware;
class MiddlewarePipeline
{
  private $middlewares = [];
  public function add($middlewares)
  {
    $this->middlewares = $middleware;
  }
  public function handle($request, $final)
  {
    $middleware = array_shift($this->middlewares);
    if ($middleware) {
      return $middleware->handle($request, function ($req) use ($final) {
        return $this->handle($req, $final);
      });
    }
    return $final($request);
  }
}
