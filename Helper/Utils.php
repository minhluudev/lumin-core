<?php

/**
 * Get the base path of the application.
 *
 * This function returns the base path of the application by moving up two directory levels
 * from the current directory and appending the provided path.
 *
 * @param  string  $path  The path to append to the base path.
 *
 * @return string The full base path with the appended path.
 */
function basePath(string $path = ''): string {
    return dirname(__DIR__, 2).$path;
}

/**
 * Get the value of an environment variable.
 *
 * This function retrieves the value of an environment variable from the \$_ENV super global.
 * If the environment variable is not set, it returns the provided default value.
 *
 * @param  string      $key  \$key The name of the environment variable.
 * @param  mixed|null  $default  \$default The default value to return if the environment variable is not set.
 *
 * @return mixed The value of the environment variable or the default value.
 */
function env(string $key, mixed $default = null): mixed {
    return $_ENV[$key] ?? $default;
}

/**
 * Get the value of a configuration variable.
 *
 * This function retrieves the value of a configuration variable from the configuration array.
 * If the configuration variable is not set, it returns null.
 *
 * @param  string  $name  The name of the configuration variable.
 *
 * @return mixed The value of the configuration variable or null.
 */
function config(string $name): mixed {
    $config = ['db' => require basePath('/config/database.php')];

    return $config[$name] ?? null;
}