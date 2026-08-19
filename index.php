<?php

/**
 * Laravel - Shared Hosting Entry Point
 * Routes all requests through Laravel's public/index.php
 */
$publicPath = __DIR__.'/public';

chdir($publicPath);

require $publicPath.'/index.php';
