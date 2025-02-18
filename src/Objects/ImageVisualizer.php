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
        $documentUrl,
        $addtionalContent
    ) {
        $this->documentUrl = $this->sanitizeContent($documentUrl);
        $this->addtionalContent = $this->sanitizeContent($addtionalContent);
    }

    public function viewer(): mixed
    {
        try {
            $html = $this->header();


            $html .= '<div class="flex flex-col items-center w-full p-4 text-gray-800">

                    <div class="flex flex-wrap justify-center items-center gap-4 mb-4">
                        <button id="zoom-out" class="p-2 cursor-pointer icon-button"><i class="fas fa-search-minus"></i></button>
                        <input id="page-input" type="number" min="1" class="p-2 cursor-pointer w-12 p-1 text-center rounded-md" placeholder="1" />
                        ' . $this->addtionalContent . '
                        <button id="zoom-in" class="p-2 cursor-pointer icon-button"><i class="fas fa-search-plus"></i></button>
                    </div>
                    
                    <div class="w-full flex justify-center overflow-auto p-2">
                         <img id="image-viewer" src="' . $this->documentUrl . '" alt="Visualização de imagem" class="rounded-lg shadow-xl" style="max-width: 100%; height: auto;">
                    </div>

                    <div id="error-message" class="hidden text-red-600 font-bold text-lg mt-2">
                        Não foi possível visualizar este documento.
                    </div>
                    <a id="download-link" href="' . $this->documentUrl . '" download class="hidden bg-blue-600 px-4 py-2 rounded-md mt-2">
                        Baixar documento
                    </a>
                </div>
            </div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
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
