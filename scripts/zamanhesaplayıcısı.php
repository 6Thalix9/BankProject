<?php

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

function calculatePercentiles($times, $percentile) {
    sort($times);
    $index = (int)ceil(($percentile / 100) * count($times)) - 1;
    return $times[$index] ?? null; 
}

function calculateAverage($times) {
    if (count($times) === 0) {
        return null;
    }
    return array_sum($times) / count($times);
}

$results = [];
foreach ($executionTimes as $key => $times) {
    if (count($times) > 0) {
        $p99 = calculatePercentiles($times, 99);
        $p95 = calculatePercentiles($times, 95);
        $average = calculateAverage($times);
        $results[$key] = ['p99' => $p99, 'p95' => $p95, 'average' => $average];
    } else {
        $results[$key] = ['p99' => null, 'p95' => null, 'average' => null];
    }
}


foreach ($results as $key => $percentiles) {
    echo "Request: $key\n";
    echo "P99: " . ($percentiles['p99'] !== null ? $percentiles['p99'] . "ms" : "N/A") . "\n";
    echo "P95: " . ($percentiles['p95'] !== null ? $percentiles['p95'] . "ms" : "N/A") . "\n";
    echo "Average: " . ($percentiles['average'] !== null ? $percentiles['average'] . "ms" : "N/A") . "\n";
    echo "\n";
}


$threshold = $percentiles['p99']; 
foreach ($executionTimes as $key => $times) {
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

    echo "Request: $key\n";
    echo "Apdex: {$apdex}\n";
    echo "\n";
}
?>
