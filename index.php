<?php
// Include necessary dependencies and setup your database connection here

// Get the requested URI
$request_uri = $_SERVER['REQUEST_URI'];

// Split the URI into an array, removing leading and trailing slashes
$uri_parts = explode('/', trim($request_uri, '/'));

// Determine the controller and action based on the URI
$controller = isset($uri_parts[0]) ? $uri_parts[0] : 'default';
$action = isset($uri_parts[1]) ? $uri_parts[1] : 'index';

// Load the appropriate controller based on the URI
$controller_file = "controllers/{$controller}.php";

if (file_exists($controller_file)) {
    require_once($controller_file);

    // Call the action method if it exists
    $action_function = "{$controller}_{$action}";

    if (function_exists($action_function)) {
        call_user_func($action_function);
    } else {
        // Handle 404: Action not found
        header('HTTP/1.0 404 Not Found');
        echo '404 Not Found';
    }
} else {
    // Handle 404: Controller not found
    header('HTTP/1.0 404 Not Found');
    echo '404 Not Found';
}
