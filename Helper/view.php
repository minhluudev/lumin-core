<?php

$viewData = ['layout'         => '',
             'sections'       => [],
             'currentSection' => null
];

/**
 * Set the layout for the view.
 *
 * This function sets the layout file to be used for the view rendering. The layout file
 * is specified relative to the `resources/views` directory.
 *
 * @param  string  $view  The name of the layout file (without the `.php` extension).
 *
 * @return void
 */
function layout(string $view): void {
    global $viewData;
    $viewData['layout'] = basePath("/resources/views/{$view}.php");
}

/**
 * End the layout rendering.
 *
 * This function includes the layout file specified in the `layout` function.
 * It uses the global `$viewData` array to get the path of the layout file and
 * includes it. This function should be called at the end of the view rendering
 * process to ensure that the layout is properly included.
 *
 * @return void
 */
function layoutEnd(): void {
    global $viewData;
    include_once $viewData['layout'];
}

/**
 * Define a section with a given name and content.
 *
 * This function allows you to define a section in your view with a specified name and content.
 * If the content is a callable, it will be executed and its output will be captured.
 * Otherwise, the content will be converted to a string.
 *
 * @param  string  $name  The name of the section.
 * @param  mixed   $content  The content of the section. It can be a string or a callable.
 *
 * @return void
 */
function section(string $name, mixed $content = null): void {
    if (!$content) {
        return;
    }

    if (is_callable($content)) {
        ob_start();
        $content();
        $content = ob_get_clean();
    } else {
        $content = (string) $content;
    }

    global $viewData;
    $viewData['sections'][$name] = $content;
}

/**
 * Start a new section.
 *
 * This function begins capturing output for a new section. The section name is specified
 * as a parameter, and the output is buffered until `sectionEnd` is called.
 *
 * @param  string  $name  The name of the section to start.
 *
 * @return void
 */
function sectionStart(string $name): void {
    global $viewData;
    $viewData['currentSection'] = $name;
    ob_start();
}

/**
 * End the current section.
 *
 * This function ends the current section by capturing the output buffer and storing it
 * in the sections array. The current section name is retrieved from the global `$viewData`
 * array, and the output buffer is cleared.
 *
 * @return void
 */
function sectionEnd(): void {
    global $viewData;
    $currentSection                        = $viewData['currentSection'];
    $viewData['sections'][$currentSection] = ob_get_clean();
    $viewData['currentSection']            = null;
}

/**
 * Retrieve the content of a section.
 *
 * This function returns the content of a section specified by its name.
 * If the section does not exist, it returns an empty string.
 *
 * @param  string  $name  The name of the section to retrieve.
 *
 * @return string The content of the section, or an empty string if the section does not exist.
 */
function setSection(string $name): string {
    global $viewData;

    return $viewData['sections'][$name] ?? '';
}

/**
 * Render a view with the given data.
 *
 * This function renders a view file located in the `resources/views` directory.
 * It extracts the provided data array into variables, starts output buffering,
 * includes the view file, and then returns the buffered output as a string.
 *
 * @param  string  $view  The name of the view file (without the `.php` extension).
 * @param  array   $data  An associative array of data to be extracted into the view.
 *
 * @return string The rendered view content.
 */
function view(string $view, array $data = []): string {
    extract($data);
    ob_start();
    include_once basePath("/resources/views/{$view}.php");

    return ob_get_clean();
}
