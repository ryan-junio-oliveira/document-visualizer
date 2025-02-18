<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;
use RyanJunioOliveira\DocumentVisualizer\Traits\HtmlTemplate;
use RyanJunioOliveira\DocumentVisualizer\Traits\Sanitize;

class PDFVisualizer implements VisualizerInterface
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
        $html = $this->header();

        try {
            $html .= '<div class="flex flex-col items-center w-full p-4 text-gray-800">

                    <div class="flex flex-wrap justify-center items-center gap-4 mb-4">
                        <button id="prev" class="p-2 cursor-pointer icon-button"><i class="fas fa-arrow-left"></i></button>
                        <button id="zoom-out" class="p-2 cursor-pointer icon-button"><i class="fas fa-search-minus"></i></button>
                        <input id="page-input" type="number" min="1" class="p-2 cursor-pointer w-12 p-1 text-center rounded-md" placeholder="1" />
                        ' . $this->addtionalContent . '
                        <button id="zoom-in" class="p-2 cursor-pointer icon-button"><i class="fas fa-search-plus"></i></button>
                        <button id="next" class="p-2 cursor-pointer icon-button"><i class="fas fa-arrow-right"></i></button>
                    </div>

                    <span class="text-sm">Página <span id="page-num">1</span> de <span id="page-count">?</span></span>
                    
                    <div class="w-full flex justify-center overflow-auto p-2">
                        <canvas id="pdf-canvas" class="max-w-full h-auto rounded-lg bg-white"></canvas>
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
                const url = "' . $this->documentUrl . '";
                let pdfDoc = null, pageNum = 1, scale = 1.5, canvas = document.getElementById("pdf-canvas"), ctx = canvas.getContext("2d");
                const renderPage = num => {
                    pdfDoc.getPage(num).then(page => {
                        const viewport = page.getViewport({ scale });
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        page.render({ canvasContext: ctx, viewport });
                        document.getElementById("page-num").textContent = num;
                    });
                };
                pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
                    pdfDoc = pdfDoc_;
                    document.getElementById("page-count").textContent = pdfDoc.numPages;
                    renderPage(pageNum);
                }).catch(() => {
                    document.getElementById("error-message").classList.remove("hidden");
                    document.getElementById("download-link").classList.remove("hidden");
                });
                document.getElementById("prev").addEventListener("click", () => pageNum > 1 && renderPage(--pageNum));
                document.getElementById("next").addEventListener("click", () => pageNum < pdfDoc.numPages && renderPage(++pageNum));
                document.getElementById("zoom-in").addEventListener("click", () => { scale += 0.25; renderPage(pageNum); });
                document.getElementById("zoom-out").addEventListener("click", () => { scale > 0.5 && (scale -= 0.25, renderPage(pageNum)); });
                document.getElementById("page-input").addEventListener("change", (e) => {
                    let inputPage = parseInt(e.target.value);
                    if (inputPage >= 1 && inputPage <= pdfDoc.numPages) renderPage(pageNum = inputPage);
                });
            </script>';

            $html .= $this->footer();
            return $html;
        } catch (\Throwable $th) {
            return $this->errorPage();
        }
    }
}
