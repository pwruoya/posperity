<?php
require 'vendor/autoload.php'; // Path to autoload.php for Predis library

use Predis\Client;

// Redis server connection parameters
$redisHost = 'posper-cache.redis.cache.windows.net'; // Redis server host
$redisPort = 6379; // Redis server port

// Create a new Redis client instance
$redis = new Client([
    'scheme' => 'tcp',
    'host'   => $redisHost,
    'port'   => $redisPort,
    'password' => '8gFHmu3qSKSuUykRBIF7ont40rwn3nXYQAzCaFpzpUo=',
]);

// Test the connection
try {
    $redis->ping(); // Ping the Redis server to check connectivity
    // echo "Connected to Redis successfully!";
} catch (Exception $e) {
    echo "Error connecting to Redis: " . $e->getMessage();
}
