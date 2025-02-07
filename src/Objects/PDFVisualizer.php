<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;
use RyanJunioOliveira\DocumentVisualizer\Traits\HtmlTemplate;

class PDFVisualizer implements VisualizerInterface
{
    use HtmlTemplate;

    public function __construct(
        private $documentUrl,
        private ?string $addtionalContent = null,
    ) {}
    
    public function viewer(): mixed
    {
        $html = $this->header();

        $html .= '
                <div class="flex justify-center items-center space-x-4">

                    <button id="prev" class="icon-button hover:bg-white hover:text-gray-700 text-white font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                        <i class="fas fa-arrow-left"></i>
                    </button>

                    <button id="zoom-out" class="icon-button hover:bg-white hover:text-gray-700 text-white font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                        <i class="fas fa-search-minus"></i>
                    </button>

                    <div class="p-1">
                        <input id="page-input" type="number" min="1" class="text-black p-1 rounded-md text-center w-12" placeholder="1" />
                    </div>

                    ' . $this->addtionalContent . '

                    <button id="zoom-in" class="icon-button hover:bg-white hover:text-gray-700 text-white font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                        <i class="fas fa-search-plus"></i>
                    </button>

                    <button id="next" class="icon-button hover:bg-white hover:text-gray-700 text-white font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                        <i class="fas fa-arrow-right"></i>
                    </button>

                </div>

                <!-- Informações de página centralizadas -->
                <div class="mt-2">
                    <span class="text-white text-sm">Página <span id="page-num">1</span> de <span id="page-count">?</span></span>
                </div>
                
            </div>

            <div class="p-6 w-full max-w-4xl text-center mt-2">
                <div class="flex justify-center mb-4">
                    <canvas id="pdf-canvas" class="rounded-lg shadow-xl bg-white"></canvas>
                </div>
                <div id="error-message" class="hidden text-red-600 font-bold text-lg">
                    Não foi possível visualizar este documento.
                </div>
                <a id="download-link" href="' . $this->documentUrl . '" download class="hidden bg-blue-600 text-white px-4 py-2 rounded-md">
                    Baixar documento
                </a>
            </div>

            <!-- PDF.js CDN -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>

            <!-- Custom JS -->
            <script>
                const url = "' . $this->documentUrl . '";
                let pdfDoc = null,
                    pageNum = 1,
                    pageIsRendering = false,
                    pageNumIsPending = null,
                    scale = 1.5, // Definimos o valor inicial do zoom aqui
                    canvas = document.getElementById("pdf-canvas"),
                    ctx = canvas.getContext("2d"),
                    errorMessage = document.getElementById("error-message"),
                    downloadLink = document.getElementById("download-link");

                // Função para renderizar a página com o zoom aplicado
                const renderPage = num => {
                    pageIsRendering = true;

                    pdfDoc.getPage(num).then(page => {
                        const viewport = page.getViewport({ scale }); // Aplicamos o zoom aqui
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        const renderContext = {
                            canvasContext: ctx,
                            viewport
                        };

                        page.render(renderContext).promise.then(() => {
                            pageIsRendering = false;

                            if (pageNumIsPending !== null) {
                                renderPage(pageNumIsPending);
                                pageNumIsPending = null;
                            }
                        });

                        document.getElementById("page-num").textContent = num;
                    });
                };

                const queueRenderPage = num => {
                    if (pageIsRendering) {
                        pageNumIsPending = num;
                    } else {
                        renderPage(num);
                    }
                };

                // Tentar carregar o documento PDF
                pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
                    pdfDoc = pdfDoc_;
                    document.getElementById("page-count").textContent = pdfDoc.numPages;
                    renderPage(pageNum);
                }).catch(error => {
                    // Exibir mensagem de erro e botão de download
                    canvas.style.display = "none";
                    errorMessage.classList.remove("hidden");
                    downloadLink.classList.remove("hidden");
                    console.error("Erro ao carregar o documento:", error);
                });

                // Controle para página anterior
                document.getElementById("prev").addEventListener("click", () => {
                    if (pageNum <= 1) return;
                    pageNum--;
                    queueRenderPage(pageNum);
                });

                // Controle para próxima página
                document.getElementById("next").addEventListener("click", () => {
                    if (pageNum >= pdfDoc.numPages) return;
                    pageNum++;
                    queueRenderPage(pageNum);
                });

                // Controle para aumentar o zoom
                document.getElementById("zoom-in").addEventListener("click", () => {
                    scale += 0.25; // Aumenta o zoom
                    renderPage(pageNum); // Renderiza novamente a página atual
                });

                // Controle para diminuir o zoom
                document.getElementById("zoom-out").addEventListener("click", () => {
                    if (scale <= 0.5) return; // Define um limite mínimo de zoom
                    scale -= 0.25; // Diminui o zoom
                    renderPage(pageNum); // Renderiza novamente a página atual
                });

                // Input de número da página
                const pageInput = document.getElementById("page-input");

                // Valida e avança para a página ao pressionar Enter ou sair do campo de input
                pageInput.addEventListener("change", () => {
                    let inputPage = parseInt(pageInput.value);
                    if (inputPage >= 1 && inputPage <= pdfDoc.numPages) {
                        pageNum = inputPage;
                        queueRenderPage(pageNum);
                    } else {
                        alert("Número de página inválido. Escolha uma página entre 1 e " + pdfDoc.numPages);
                    }
                });
            </script>';

        $html .= $this->footer();
        return $html;
    }
}
