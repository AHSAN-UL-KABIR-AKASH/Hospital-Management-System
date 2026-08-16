<?php
require_once __DIR__ . '/../bootstrap.php';

$_SESSION = [];
session_destroy();

json_response(['success' => true, 'message' => 'Logged out successfully']);
