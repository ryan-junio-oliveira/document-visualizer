<?php

namespace RyanJunioOliveira\DocumentVisualizer;

use RyanJunioOliveira\DocumentVisualizer\Factories\VisualizerFactory;

class DocumentViewer
{
    public function __construct(
        private $documentUrl,
        private ?string $addtionalContent = null,
    ) {}

    public function visualize(): string
    {
        $factory = new VisualizerFactory($this->documentUrl, $this->addtionalContent);
        $object = $factory->create();
        return $object->viewer();
    }
}
