<?php

namespace RyanJunioOliveira\DocumentVisualizer\Factories;

use RyanJunioOliveira\DocumentVisualizer\Objects\Unsupported;
use PhpOffice\PhpWord\Exception\UnsupportedImageTypeException;
use RyanJunioOliveira\DocumentVisualizer\Objects\PDFVisualizer;
use RyanJunioOliveira\DocumentVisualizer\Objects\ExcelVisualizer;
use RyanJunioOliveira\DocumentVisualizer\Objects\ImageVisualizer;
use RyanJunioOliveira\DocumentVisualizer\Objects\WordVisualizer;

class VisualizerFactory
{
    private string $extension;

    public function __construct(private $documentUrl, private ?string $addtionalContent = null)
    {
        $this->extension = pathinfo($documentUrl, PATHINFO_EXTENSION);
    }

    public function create()
    {
        $extension = mb_strtolower($this->extension);

        switch ($extension) {
            case 'docx':
                return new WordVisualizer($this->documentUrl, $this->addtionalContent);
                break;
            case 'pdf':
                return new PDFVisualizer($this->documentUrl, $this->addtionalContent);
                break;
            case 'xlsx':
            case 'xls':
            case 'csv':
                return new ExcelVisualizer($this->documentUrl);
                break;
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'svg':
                return new ImageVisualizer($this->documentUrl, $this->addtionalContent);
                break;
            default:
                return new Unsupported($this->documentUrl, $this->addtionalContent);
        }
    }
}
