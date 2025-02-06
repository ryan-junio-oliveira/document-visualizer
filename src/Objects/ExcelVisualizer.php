<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExcelVisualizer implements VisualizerInterface
{
    public function __construct(
        private $documentUrl
    ) {}

    public function viewer(): string
    {
        try {
            $spreadsheet = IOFactory::load($this->documentUrl);
            $sheet = $spreadsheet->getActiveSheet();
            $mergeCells = $sheet->getMergeCells(); // Células mescladas
    
            $html = "<table cellspacing='0' cellpadding='5' style='border-collapse: collapse; font-family: Arial, sans-serif;'>";
    
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
                                // Somente a primeira célula da mesclagem é renderizada
                                $startColumn = preg_replace('/\d/', '', $start);
                                $endColumn = preg_replace('/\d/', '', $end);
                                $startRow = preg_replace('/\D/', '', $start);
                                $endRow = preg_replace('/\D/', '', $end);
    
                                $colspan = ord($endColumn) - ord($startColumn) + 1;
                                $rowspan = $endRow - $startRow + 1;
    
                                // Marca todas as outras células da mesclagem como processadas
                                for ($col = ord($startColumn); $col <= ord($endColumn); $col++) {
                                    for ($rowIdx = $startRow; $rowIdx <= $endRow; $rowIdx++) {
                                        $mergedCoordinate = chr($col) . $rowIdx;
                                        if ($mergedCoordinate != $coordinate) {
                                            $processedCells[] = $mergedCoordinate;
                                        }
                                    }
                                }
                            } else {
                                // Se a célula não for a primeira da mesclagem, pula para a próxima
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
    
                    // Somente aplica borda se houver uma borda definida
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
    
                    // Adiciona as bordas se existirem
                    if ($borderCss) {
                        $css .= $borderCss;
                    }
    
                    // Renderizar a célula com colspan e rowspan, se aplicável
                    $html .= "<td style='$css' colspan='$colspan' rowspan='$rowspan'>$value</td>";
                }
    
                $html .= "</tr>";
            }
    
            $html .= "</table>";
    
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

    private function errorPage(): string
    {
        return '
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Erro ao carregar o documento</title>
            <!-- Tailwind CSS CDN -->
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        </head>
        <body class="bg-red-100 flex items-center justify-center min-h-screen">
            <div class="text-center">
                <h1 class="text-3xl text-red-600 font-bold">Erro ao carregar o documento</h1>
                <p class="text-red-500 mt-2">Não foi possível visualizar o documento no momento.</p>
                <a href="' . $this->documentUrl . '" download class="text-blue-500 underline mt-4">Clique aqui para baixar o documento</a>
            </div>
        </body>
        </html>';
    }
}
