<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;

class Unsupported implements VisualizerInterface
{
    public function __construct(
        private $documentUrl,
        private ?string $addtionalContent = null,
    ) {}
    
    public function viewer(): mixed
    {
        return '
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Documento não suportado</title>
            <!-- Tailwind CSS CDN -->
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        </head>
        <body class="bg-gray-100 flex items-center justify-center min-h-screen">
            <div class="text-center">
                <h1 class="text-3xl text-gray-800 font-bold">Documento não suportado</h1>
                <p class="text-gray-500 mt-2">O tipo de documento enviado não é suportado para visualização.</p>
                <a href="' . $this->documentUrl . '" download class="text-blue-500 underline mt-4">Clique aqui para baixar o documento</a>
            </div>
        </body>
        </html>';
    }
}
