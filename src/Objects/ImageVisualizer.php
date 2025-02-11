<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use RuntimeException;
use RyanJunioOliveira\DocumentVisualizer\Traits\HtmlTemplate;
use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;
use RyanJunioOliveira\DocumentVisualizer\Traits\Sanitize;

class ImageVisualizer implements VisualizerInterface
{
    use HtmlTemplate, Sanitize;

    private string $documentUrl;
    private ?string $addtionalContent = null;

    public function __construct(
        $documentUrl, $addtionalContent
    ) {
        $this->documentUrl = $this->sanitizeContent($documentUrl);
        $this->addtionalContent = $this->sanitizeContent($addtionalContent);
    }

    public function viewer(): mixed
    {
        try {
            $html = $this->header();

            $html .= '
                <div class="flex justify-center items-center space-x-4">

                    <button id="zoom-out" class="icon-button hover:bg-white hover:text-gray-700 text-white font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                        <i class="fas fa-search-minus"></i>
                    </button>

                    ' . $this->addtionalContent . '

                    <button id="zoom-in" class="icon-button hover:bg-white hover:text-gray-700 text-white font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                        <i class="fas fa-search-plus"></i>
                    </button>

                </div>
                
            </div>

            <div class="p-6 w-full max-w-4xl text-center mt-6 justify-center items-center">

                <div class="flex justify-center mb-4">
                    <img id="image-viewer" src="' . $this->documentUrl . '" alt="Visualização de imagem" class="rounded-lg shadow-xl" style="max-width: 100%; height: auto;">
                </div>

                <div id="error-message" class="text-white text-lg hidden">
                    Não foi possível visualizar este documento. <br>
                    <a href="' . $this->documentUrl . '" download class="underline text-blue-400">Clique aqui para baixar o documento.</a>
                </div>

            </div>

            <script>
                let zoomLevel = 1;
                const img = document.getElementById("image-viewer");
                const errorMessage = document.getElementById("error-message");

                // Zoom in
                document.getElementById("zoom-in").addEventListener("click", function () {
                    zoomLevel += 0.1;
                    img.style.transform = "scale(" + zoomLevel + ")";
                });

                // Zoom out
                document.getElementById("zoom-out").addEventListener("click", function () {
                    if (zoomLevel <= 0.2) return;
                    zoomLevel -= 0.1;
                    img.style.transform = "scale(" + zoomLevel + ")";
                });

                // Detecta erro no carregamento da imagem e exibe a mensagem de erro
                img.onerror = function () {
                    img.style.display = "none";
                    errorMessage.classList.remove("hidden");
                };
            </script>';

            $html .= $this->footer();

            return $html;
        } catch (\Throwable $th) {
            return $this->errorPage();
        }
    }
}
