<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BasicAuthentication
{
	private const USER = 'berke';
	private const PASS = '1';

	public function handle(Request $request, Closure $next)
	{
		if (!$request->hasHeader('Authorization')) {
			return response('Unauthorized', 401)
				->header('WWW-Authenticate', 'Basic realm="HiBit"');
		}

		$authorizationHeader = $request->header('Authorization');
		if (strpos($authorizationHeader, 'Basic ') !== 0) {
			return response('Unauthorized', 401)
				->header('WWW-Authenticate', 'Basic realm="HiBit"');
		}

		$credentials = base64_decode(substr($authorizationHeader, 6));
		list($username, $password) = explode(':', $credentials, 2);

		if ($username !== self::USER || $password !== self::PASS) {
			return response('Unauthorized', 401)
				->header('WWW-Authenticate', 'Basic realm="HiBit"');
		}

		return $next($request);
	}
}