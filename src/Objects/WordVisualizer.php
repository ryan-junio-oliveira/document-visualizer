<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use RuntimeException;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use RyanJunioOliveira\DocumentVisualizer\Traits\Sanitize;
use RyanJunioOliveira\DocumentVisualizer\Traits\HtmlTemplate;
use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;

class WordVisualizer implements VisualizerInterface
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

    public function viewer(): string
    {
        try {

            Settings::setOutputEscapingEnabled(true);

            $word = IOFactory::load($this->documentUrl);
            $htmlWriter = IOFactory::createWriter($word, 'HTML');

            ob_start();
            $htmlWriter->save('php://output');
            $wordHtml = ob_get_contents();
            ob_end_clean();

            $styles = $this->getDocumentStyles($wordHtml);

            $html = $this->header();

            $html .= '<style>
                    ' . $styles . '
                    #word-content {
                        word-wrap: break-word; /* Quebra o texto automaticamente */
                        width: 100%; /* O texto ocupa 100% do espaço disponível */
                        overflow-wrap: break-word;
                    }
                </style>';

            $html .= '
            <div class="mt-2 flex justify-center items-center space-x-4 text-gray-800">
                <button id="align-left" class="align-button icon-button hover:text-gray-700 font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                    <i class="fas fa-align-left"></i>
                </button>
                <button id="align-center" class="align-button icon-button hover:text-gray-700 font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                    <i class="fas fa-align-center"></i>
                </button>
                <button id="align-right" class="align-button icon-button hover:text-gray-700 font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                    <i class="fas fa-align-right"></i>
                </button>
                <button id="align-justify" class="align-button icon-button hover:text-gray-700 font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                    <i class="fas fa-align-justify"></i>
                </button>
            </div>
        </div>';

            $html .= '
        <div class="p-6 w-full max-w-4xl mt-2">
            <div id="word-content" class="mb-4 bg-white p-6 rounded-lg shadow-xl">
                ' . $wordHtml . '
            </div>
        </div>';

            $html .= '
        <script>
            // Selecionar o conteúdo do documento Word
            const wordContent = document.getElementById("word-content");

            // Função para definir o alinhamento de texto
            function setAlignment(alignment) {
                wordContent.style.textAlign = alignment;
            }

            // Controles de alinhamento de texto
            document.getElementById("align-left").addEventListener("click", () => setAlignment("left"));
            document.getElementById("align-center").addEventListener("click", () => setAlignment("center"));
            document.getElementById("align-right").addEventListener("click", () => setAlignment("right"));
            document.getElementById("align-justify").addEventListener("click", () => setAlignment("justify"));
        </script>';

            $html .= $this->footer();

            return $html;
            
        } catch (\Throwable $th) {
            return $this->errorPage();
        }
    }

    private function getDocumentStyles(string $html): string
    {
        preg_match('/<style>(.*?)<\/style>/s', $html, $matches);
        return $matches[1] ?? '';
    }
}
