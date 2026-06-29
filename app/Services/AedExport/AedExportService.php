<?php

namespace App\Services\AedExport;

use App\Models\Aed;

class AedExportService
{
    public function __construct(
        private readonly AedExportPayloadBuilder $payloadBuilder,
        private readonly AedExcelExporter $excelExporter,
    ) {
    }

    public function exportAll()
    {
        $payload = $this->payloadBuilder->buildPayloadAll();
        return $this->excelExporter->export($payload);
    }

    public function exportOne(Aed $aed)
    {
        $payload = $this->payloadBuilder->buildPayloadOne($aed);
        return $this->excelExporter->export($payload);
    }
}

