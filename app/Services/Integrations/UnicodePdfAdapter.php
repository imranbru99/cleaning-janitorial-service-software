<?php

namespace App\Services\Integrations;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Throwable;

class UnicodePdfAdapter
{
    public function render(string $view, array $data = [], string $locale = 'en'): string
    {
        if (class_exists(\ImranDev\UnicodePdf\Facades\UnicodePdf::class)) {
            try {
                return \ImranDev\UnicodePdf\Facades\UnicodePdf::view($view, $data)
                    ->locale($locale)
                    ->output();
            } catch (Throwable $e) {
                Log::warning('UnicodePdf failed, falling back to DomPDF', ['error' => $e->getMessage()]);
            }
        }

        return Pdf::loadView($view, $data)->output();
    }

    public function download(string $view, array $data, string $filename, string $locale = 'en')
    {
        $content = $this->render($view, $data, $locale);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
