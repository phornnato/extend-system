<?php

// Fix for Laravel on Vercel to prevent it from stripping the /api prefix
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require __DIR__ . '/../public/index.php';