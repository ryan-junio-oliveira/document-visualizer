<?php

namespace RyanJunioOliveira\DocumentVisualizer\Traits;

trait HtmlTemplate
{
    private function header()
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
        <body class="bg-black bg-opacity-70 backdrop-blur-md flex items-center flex flex-col w-full min-h-screen">

            <!-- Navbar que ocupa a largura total -->
            <div class="w-full z-10 relative bg-gray-900 bg-opacity-70 text-white py-2 text-center flex flex-col shadow-lg backdrop-blur-md">';
    }

    private function footer()
    {
        return '
            </body>
        </html>';
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
        <body class="bg-gray-100 flex items-center justify-center min-h-screen">
            <div class="text-center">
                <h1 class="text-3xl text-red-600 font-bold">Erro ao carregar o documento</h1>
                <p class="mt-4 text-lg">Ocorreu um erro ao tentar visualizar o documento. Verifique se o arquivo está corrompido ou se o caminho está correto.</p>
            </div>
        </body>
        </html>';
    }
}
