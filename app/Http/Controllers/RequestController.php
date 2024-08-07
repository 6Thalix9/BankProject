<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Sentry\State\HubInterface;

class RequestController extends Controller
{
    private $sentry;

    public function __construct(HubInterface $sentry)
    {
        $this->sentry = $sentry;
    }

    public function request1()
    {
        $client = new Client();

        try {
            $response = $client->request('GET', 'http://10.150.238.165:8000/api/Bank/9');
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            // Process the response
            return response()->json([
                'status' => $statusCode,
                'data' => json_decode($body)
            ]);
        } catch (RequestException $e) {
            // Capture the exception with Sentry
            $this->sentry->captureException($e);

            // Handle the exception
            return response()->json([
                'error' => 'Failed to connect to the server.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
