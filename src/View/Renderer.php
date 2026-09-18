<?php

declare(strict_types=1);

namespace App\View;

final class Renderer
{
    public function __construct(private readonly string $templateDir)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function page(string $template, array $data = [], string $title = ''): string
    {
        return $this->render(
            'layout',
            [
                'title' => $title,
                'content' => $this->render($template, $data),
            ] + $data
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $path = sprintf('%s/%s.php', $this->templateDir, $template);
        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('Template not found: "%s"', $path));
        }
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            include $path;
        } catch (\Throwable $e) {
            ob_end_clean();

            throw $e;
        }
        $output = ob_get_clean();
        if (false === $output) {
            throw new \RuntimeException('Template failed.');
        }

        return $output;
    }
}