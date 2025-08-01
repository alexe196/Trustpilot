<?php

namespace App\Service;

use App\Traits\ExportToCsv;

class ExportCSVService
{
    use ExportToCsv;

    public function getLincExportFile(): string
    {
        return $this->exportToCsvFile();
    }
}
