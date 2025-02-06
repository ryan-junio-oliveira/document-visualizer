<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;

class WordVisualizer implements VisualizerInterface
{
    public function __construct(
        private string $documentUrl,
        private ?string $addtionalContent = null,
    ) {}

    public function viewer(): string
    {
        Settings::setOutputEscapingEnabled(true);

        $word = IOFactory::load($this->documentUrl);
        $htmlWriter = IOFactory::createWriter($word, 'HTML');

        ob_start();
        $htmlWriter->save('php://output');
        $html = ob_get_contents();
        ob_end_clean();

        return '
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <!-- Tailwind CSS CDN -->
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

            <!-- Font Awesome CDN -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

            <!-- Inclui os estilos gerados pelo PhpWord -->
            <style>
                ' . $this->getDocumentStyles($html) . '
            </style>
        </head>
        <body class="flex flex-col w-full bg-black bg-opacity-70 backdrop-blur-md flex items-center justify-center min-h-screen">

            <!-- Navbar fixada no topo -->
            <div class="w-full flex flex-col justify-center items-center z-50 bg-gray-900 bg-opacity-70 text-white py-2 text-center shadow-lg backdrop-blur-md">

                <div class="mt-2">
                    <span class="text-white text-sm">Exibindo documento Word</span>
                </div>

            </div>

            <div class="p-6 w-full max-w-4xl mt-2">
                <div class="mb-4 bg-white p-6 rounded-lg shadow-xl">
                    ' . $html . '
                </div>
            </div>
        </body>
        </html>';
    }

    private function getDocumentStyles(string $html): string
    {
        preg_match('/<style>(.*?)<\/style>/s', $html, $matches);
        return $matches[1] ?? '';
    }
}
