<?php

declare(strict_types=1);

class View
{
    public static function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!is_readable($viewFile)) {
            http_response_code(500);
            echo 'View not found';
            exit;
        }
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        $layoutFile = VIEWS_PATH . '/layouts/' . $layout . '.php';
        if (!is_readable($layoutFile)) {
            echo $content;
            return;
        }
        require $layoutFile;
    }

    public static function partial(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $path = VIEWS_PATH . '/partials/' . $name . '.php';
        if (is_readable($path)) {
            require $path;
        }
    }
}
