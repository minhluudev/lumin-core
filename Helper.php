<?php

namespace Lumin;

class Helper {
    /**
     * Retrieve an environment variable.
     *
     * This function fetches the value of an environment variable specified by its name.
     * If the environment variable is not set, it returns the provided default value.
     *
     * @param  string  $name  The name of the environment variable.
     * @param  string  $default  The default value to return if the environment variable is not set.
     *
     * @return mixed The value of the environment variable, or the default value if not set.
     */
    public static function env(string $name, string $default = ''): mixed {
        return $_ENV[$name] ?? $default;
    }

    /**
     * Retrieve a configuration value.
     *
     * This function fetches a configuration value specified by its name.
     * If the configuration value is not set, it returns `null`.
     *
     * @param  string  $name  The name of the configuration value.
     *
     * @return mixed The value of the configuration value, or `null` if not set.
     */
    public static function config(string $name): mixed {
        $config = [
            'db' => include_once self::basePath('/config/database.php')
        ];

        return $config[$name] ?? null;
    }

    /**
     * Get the base path of the application.
     *
     * This function returns the base path of the application, which is the directory
     * containing the `framework` directory.
     *
     * @param  string  $path  The path to append to the base path.
     *
     * @return string The base path of the application.
     */
    public static function basePath(string $path = ''): string {
        return dirname(__DIR__, 3).$path;
    }

    /**
     * Convert a string to snake case.
     *
     * This function converts a string to snake case. It replaces any uppercase
     * characters with an underscore followed by the lowercase version of the character.
     *
     * @param  string  $input  The string to convert to snake case.
     *
     * @return string The string converted to snake case.
     */
    public static function convertToSnakeCaseAndPlural(string $input): string {
        $snake_case = preg_replace('/(?<!^)[A-Z]/', '_$0', $input);
        $snake_case = strtolower($snake_case);

        if (str_ends_with($snake_case, 'y')) {
            $snake_case = substr($snake_case, 0, -1).'ies';
        } else if (!str_ends_with($snake_case, 's')) {
            $snake_case .= 's';
        }

        return $snake_case;
    }
}
