<?php

use RyanJunioOliveira\DocumentVisualizer\DocumentViewer;

require('vendor/autoload.php');

$addtionalContent = '
    <button id="print" class="icon-button bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md shadow transition-transform transform hover:scale-105">
        <i class="fas fa-print"></i>
    </button>
    <script>
        document.getElementById("print").addEventListener("click", () => {
            window.print();
        });
    </script>
';

$viewer = new DocumentViewer('teste2.pdf', 'Visualizador de documentos', $addtionalContent);

?>

<script src="https://unpkg.com/@tailwindcss/browser@4"></script>

<div class="w-full max-w-80 m-auto mt-16">
    <?php echo $viewer->visualize(); ?>
</div>