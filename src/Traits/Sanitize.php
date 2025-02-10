<?php

namespace RyanJunioOliveira\DocumentVisualizer\Traits;

trait Sanitize
{
    protected function sanitizeContent(?string $html = null)
    {
        try {
            if (!empty($html)) {
                $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
                $html = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $html);
                $html = preg_replace('/<object\b[^>]*>(.*?)<\/object>/is', '', $html);
                $html = preg_replace('/<embed\b[^>]*>(.*?)<\/embed>/is', '', $html);
            }

            return $html;
        } catch (\Throwable $th) {
        }
    }

    protected function escapeContent(?string $content = null)
    {
        try {
            if (!empty($content)) {
                return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
            }

            return $content;
        } catch (\Throwable $th) {
        }
    }
}
