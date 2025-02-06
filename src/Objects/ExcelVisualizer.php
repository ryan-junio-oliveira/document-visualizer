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
            <body class="bg-gray-100">

            <!-- Navbar simples -->
            <div class="w-full z-50 bg-gray-900 bg-opacity-70 text-white py-2 text-center shadow-lg backdrop-blur-md">
                <div class="flex justify-center items-center space-x-4">
                </div>
            </div>

            <div class="overflow-x-auto bg-white shadow-lg rounded-lg p-6 mt-6">';

            $html .= '<table class="min-w-full table-auto border-collapse text-left">';
            $html .= '<thead class="bg-gray-200">';
            $html .= '<tr>';

            foreach ($sheet->getRowIterator(1, 1) as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $valorCelula = htmlspecialchars($this->utf8EncodeIfNeeded($cell->getValue()));
                    $html .= '<th class="px-4 py-2 font-bold text-sm text-gray-600 border-b">' . $valorCelula . '</th>';
                }
            }
            $html .= '</tr>';
            $html .= '</thead>';

            $html .= '<tbody class="bg-white divide-y divide-gray-200">';
            for ($linha = 2; $linha <= $numLinhas; $linha++) {
                $html .= '<tr class="hover:bg-gray-100">';
                for ($coluna = 'A'; $coluna <= $numColunas; $coluna++) {
                    $valorCelula = htmlspecialchars($this->utf8EncodeIfNeeded($sheet->getCell($coluna . $linha)->getValue()));
                    $html .= '<td class="px-4 py-2 text-sm text-gray-700">' . $valorCelula . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div></body></html>';

            return $html;
        } catch (\Throwable $th) {
            return $this->errorPage();
        }
    }

    private function utf8EncodeIfNeeded($valor): string
    {
        if (is_string($valor) && mb_detect_encoding($valor, 'UTF-8', true) === false) {
            return utf8_encode($valor);
        }
        return (string) $valor;
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
