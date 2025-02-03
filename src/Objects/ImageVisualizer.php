<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;

class ImageVisualizer implements VisualizerInterface
{
    public function __construct(
        private string $documentUrl,
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

            <!-- Tailwind CSS CDN -->
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

            <!-- Font Awesome CDN -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        </head>
        <body class="bg-black bg-opacity-70 backdrop-blur-md flex items-center justify-center min-h-screen pt-16">

            <!-- Navbar fixada no topo -->
            <div class="fixed top-0 left-0 right-0 z-50 bg-gray-900 bg-opacity-70 text-white py-2 text-center shadow-lg backdrop-blur-md">
                <div class="flex justify-center items-center space-x-4">

                    <button id="zoom-out" class="icon-button bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md shadow transition-transform transform hover:scale-105">
                        <i class="fas fa-search-minus"></i>
                    </button>

                    <button id="zoom-in" class="icon-button bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md shadow transition-transform transform hover:scale-105">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>

                ' . $this->addtionalContent . '
            </div>

            <div class="p-6 w-full max-w-4xl text-center mt-2">
                <div class="flex justify-center mb-4">
                    <img id="image-viewer" src="' . $this->documentUrl . '" class="rounded-lg shadow-xl" style="max-width: 100%; height: auto;">
                </div>
            </div>

            <script>
                let zoomLevel = 1;
                const img = document.getElementById("image-viewer");

                document.getElementById("zoom-in").addEventListener("click", function () {
                    zoomLevel += 0.1;
                    img.style.transform = "scale(" + zoomLevel + ")";
                });

                document.getElementById("zoom-out").addEventListener("click", function () {
                    if (zoomLevel <= 0.2) return; // Definir um limite mínimo de zoom
                    zoomLevel -= 0.1;
                    img.style.transform = "scale(" + zoomLevel + ")";
                });
            </script>
        </body>
        </html>';
    }
}
