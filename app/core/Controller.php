<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $config = $this->config;
        ob_start();
        require $this->config['base_path'] . '/app/views/' . $view . '.php';
        $content = ob_get_clean();
        require $this->config['base_path'] . '/app/views/layouts/main.php';
    }

    protected function redirect(string $route): void
    {
        header('Location: ?r=' . $route);
        exit;
    }
}
