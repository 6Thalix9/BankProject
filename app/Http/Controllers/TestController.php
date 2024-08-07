<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class TestController extends Controller
{
    public function analyticValues()
    {
        $logFile = '/home/ubuntu/BankProject/BerkeBank/storage/logs/laravel.log';
        $executionTimes = [];

        $handle = fopen($logFile, 'r');

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (preg_match('/\{"method":"(GET|POST)","uri":"(\/api\/\S+)","execution_time":(\d+\.\d+)\}/', $line, $matches)) {
                    $requestType = $matches[1];
                    $endpoint = $matches[2];
                    $executionTime = (float)$matches[3];
                    $key = $requestType . ' ' . $endpoint;

                    if (!isset($executionTimes[$key])) {
                        $executionTimes[$key] = [];
                    }
                    $executionTimes[$key][] = $executionTime;
                }
            }
            fclose($handle);
        }

        function calculatePercentiles($times, $percentile)
        {
            sort($times);
            $index = (int)(($percentile / 100) * count($times));
            return $times[$index] ?? null;
        }

        function calculateAverage($times)
        {
            if (count($times) === 0) {
                return null;
            }
            return array_sum($times) / count($times);
        }

        $results = [];
        foreach ($executionTimes as $key => $times) {
            if (count($times) > 0) {
                $p75 = calculatePercentiles($times, 75);
                $p90 = calculatePercentiles($times, 90);
                $p95 = calculatePercentiles($times, 95);
                $p99 = calculatePercentiles($times, 99);
                $average = calculateAverage($times);
                $results[$key] = ['p90' => $p90, 'p95' => $p95, 'p99' => $p99, 'average' => $average, 'p75' => $p75];
            } else {
                $results[$key] = ['p90' => null, 'p95' => null, 'p99' => null, 'average' => null];
            }
        }

        foreach ($executionTimes as $key => $times) {
            $threshold = $results[$key]['p99'];
            $satisfied = 0;
            $tolerating = 0;
            $total = count($times);

            foreach ($times as $time) {
                if ($time <= $threshold) {
                    $satisfied++;
                } elseif ($time <= 4 * $threshold) {
                    $tolerating++;
                }
            }

            $apdex = $total > 0 ? ($satisfied + ($tolerating / 2)) / $total : 0;

            $results[$key]['apdex'] = $apdex;
        }

        return response()->json($results);
    }
}