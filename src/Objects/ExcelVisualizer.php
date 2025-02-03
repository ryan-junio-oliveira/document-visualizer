<?php

namespace RyanJunioOliveira\DocumentVisualizer\Objects;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RyanJunioOliveira\DocumentVisualizer\Interfaces\VisualizerInterface;

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

            $numLinhas = $sheet->getHighestRow();
            $numColunas = $sheet->getHighestColumn();

            $html = '
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Visualizador de Planilha</title>
                <!-- Tailwind CSS CDN -->
                <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            </head>
            <body class="bg-gray-100 p-8">

            <!-- Navbar simples -->
            <div class="fixed top-0 left-0 right-0 z-50 bg-gray-900 text-white py-2 shadow-md text-center">
                <div class="flex justify-center items-center">
                    <h1 class="text-xl font-semibold">Visualizador de Planilha Excel</h1>
                </div>
            </div>

            <div class="overflow-x-auto bg-white shadow-lg rounded-lg p-6 mt-12">';

            // Tabela usando Tailwind CSS
            $html .= '<table class="min-w-full table-auto border-collapse text-left">';
            $html .= '<thead class="bg-gray-200">';
            $html .= '<tr>';
            foreach ($sheet->getRowIterator(1, 1) as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $html .= '<th class="px-4 py-2 font-bold text-sm text-gray-600 border-b">' . htmlspecialchars($cell->getValue()) . '</th>';
                }
            }
            $html .= '</tr>';
            $html .= '</thead>';

            $html .= '<tbody class="bg-white divide-y divide-gray-200">';
            for ($linha = 2; $linha <= $numLinhas; $linha++) {
                $html .= '<tr class="hover:bg-gray-100">';
                for ($coluna = 'A'; $coluna <= $numColunas; $coluna++) {
                    $valorCelula = $sheet->getCell($coluna . $linha)->getValue();
                    $html .= '<td class="px-4 py-2 text-sm text-gray-700">' . htmlspecialchars($valorCelula) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div></body></html>';

            return $html;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
