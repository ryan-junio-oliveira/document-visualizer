<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use RuntimeException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use RyanJunioOliveira\DocumentVisualizer\Traits\Sanitize;
use RyanJunioOliveira\DocumentVisualizer\Traits\HtmlTemplate;
use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;

class ExcelVisualizer implements VisualizerInterface
{
    use HtmlTemplate, Sanitize;

    private string $documentUrl;
    private ?string $addtionalContent = null;

    public function __construct(
        $documentUrl
    ) {
        $this->documentUrl = $this->sanitizeContent($documentUrl);
    }

    public function viewer(): string
    {
        try {
            $html = $this->header();

            $html .= '
                <div class="flex justify-center items-center space-x-4">

                    <button id="zoom-out" class="icon-button hover:bg-white hover:text-gray-700 text-white font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                        <i class="fas fa-search-minus"></i>
                    </button>

                    <button id="zoom-in" class="icon-button hover:bg-white hover:text-gray-700 text-white font-bold py-2 px-4 rounded-md transition-transform transform hover:scale-105">
                        <i class="fas fa-search-plus"></i>
                    </button>

                </div>
                
            </div>

            <div id="table-container" style="transform: scale(1); transition: transform 0.3s; overflow-x: auto;" class="overflow-auto p-6 w-full max-w-4xl text-center mt-6 justify-center items-center bg-white rounded z-0">
                <table id="excel-table" cellspacing="0" cellpadding="5" style="border-collapse: collapse; font-family: Arial, sans-serif; width: 100%;">
            ';

            $spreadsheet = IOFactory::load($this->documentUrl);
            $sheet = $spreadsheet->getActiveSheet();
            $mergeCells = $sheet->getMergeCells(); // Células mescladas

            $html .= "<table cellspacing='0' cellpadding='5' style='border-collapse: collapse; font-family: Arial, sans-serif;'>";

            // Para rastrear células já processadas (evitar duplicação)
            $processedCells = [];

            foreach ($sheet->getRowIterator() as $row) {
                $html .= "<tr>";

                foreach ($row->getCellIterator() as $cell) {
                    $coordinate = $cell->getCoordinate();

                    // Se a célula já foi processada (por ser parte de uma mesclagem), pula ela
                    if (in_array($coordinate, $processedCells)) {
                        continue;
                    }

                    $value = $cell->getFormattedValue(); // Mantém a formatação original (datas, números)
                    $style = $sheet->getStyle($coordinate);
                    $font = $style->getFont();
                    $fill = $style->getFill();
                    $alignment = $style->getAlignment();
                    $borders = $style->getBorders(); // Obtenção das bordas

                    // Configurações de Mesclagem
                    $colspan = 1;
                    $rowspan = 1;

                    foreach ($mergeCells as $mergedRange) {
                        if ($sheet->getCell($coordinate)->isInRange($mergedRange)) {
                            [$start, $end] = explode(':', $mergedRange);

                            if ($coordinate === $start) {
                                $startColumn = preg_replace('/\d/', '', $start);
                                $endColumn = preg_replace('/\d/', '', $end);
                                $startRow = preg_replace('/\D/', '', $start);
                                $endRow = preg_replace('/\D/', '', $end);

                                $colspan = ord($endColumn) - ord($startColumn) + 1;
                                $rowspan = $endRow - $startRow + 1;

                                for ($col = ord($startColumn); $col <= ord($endColumn); $col++) {
                                    for ($rowIdx = $startRow; $rowIdx <= $endRow; $rowIdx++) {
                                        $mergedCoordinate = chr($col) . $rowIdx;
                                        if ($mergedCoordinate != $coordinate) {
                                            $processedCells[] = $mergedCoordinate;
                                        }
                                    }
                                }
                            } else {
                                continue 2;
                            }
                        }
                    }

                    // CSS Dinâmico
                    $css = "padding: 5px;"; // Apenas padding básico

                    // Fontes
                    if ($font->getBold()) $css .= "font-weight: bold; ";
                    if ($font->getItalic()) $css .= "font-style: italic; ";
                    if ($font->getColor()->getRGB() !== '000000') $css .= "color: #" . $font->getColor()->getRGB() . "; ";
                    if ($font->getSize()) $css .= "font-size: {$font->getSize()}px; ";

                    // Preenchimento
                    if ($fill->getFillType() === 'solid' && $fill->getStartColor()->getRGB() !== 'FFFFFF') {
                        $css .= "background-color: #" . $fill->getStartColor()->getRGB() . "; ";
                    }

                    // Alinhamento
                    if ($alignment->getHorizontal() === Alignment::HORIZONTAL_CENTER) $css .= "text-align: center; ";
                    if ($alignment->getHorizontal() === Alignment::HORIZONTAL_RIGHT) $css .= "text-align: right; ";

                    // Verifica bordas
                    $borderCss = '';

                    if ($borders->getTop()->getBorderStyle() != Border::BORDER_NONE) {
                        $borderCss .= 'border-top: ' . $this->getBorderStyle($borders->getTop()) . '; ';
                    }
                    if ($borders->getBottom()->getBorderStyle() != Border::BORDER_NONE) {
                        $borderCss .= 'border-bottom: ' . $this->getBorderStyle($borders->getBottom()) . '; ';
                    }
                    if ($borders->getLeft()->getBorderStyle() != Border::BORDER_NONE) {
                        $borderCss .= 'border-left: ' . $this->getBorderStyle($borders->getLeft()) . '; ';
                    }
                    if ($borders->getRight()->getBorderStyle() != Border::BORDER_NONE) {
                        $borderCss .= 'border-right: ' . $this->getBorderStyle($borders->getRight()) . '; ';
                    }

                    if ($borderCss) {
                        $css .= $borderCss;
                    }

                    $html .= "<td style='$css' colspan='$colspan' rowspan='$rowspan'>$value</td>";
                }

                $html .= "</tr>";
            }

            $html .= "</table>";
            $html .= '</div>';

            $html .= $this->footer();

            // Adicionando script para funcionalidade de zoom
            $html .= '
            <script>
                let zoomLevel = 1;
                const tableContainer = document.getElementById("table-container");

                document.getElementById("zoom-in").addEventListener("click", function () {
                    zoomLevel += 0.1;
                    tableContainer.style.transform = "scale(" + zoomLevel + ")";
                });

                document.getElementById("zoom-out").addEventListener("click", function () {
                    if (zoomLevel <= 0.2) return;
                    zoomLevel -= 0.1;
                    tableContainer.style.transform = "scale(" + zoomLevel + ")";
                });
            </script>
            ';

            return $html;
        } catch (\Throwable $th) {
            return $this->errorPage();
        }
    }

    private function getBorderStyle($border): string
    {
        switch ($border->getBorderStyle()) {
            case Border::BORDER_THIN:
                return '1px solid #000';
            case Border::BORDER_MEDIUM:
                return '2px solid #000';
            case Border::BORDER_THICK:
                return '3px solid #000';
            case Border::BORDER_DOTTED:
                return '1px dotted #000';
            case Border::BORDER_DASHED:
                return '1px dashed #000';
            default:
                return 'none';
        }
    }
}
