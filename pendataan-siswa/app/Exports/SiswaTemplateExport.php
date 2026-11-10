<?php

namespace App\Exports;

use App\Exports\Template\DataSheet;
use App\Exports\Template\PetunjukSheet;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SiswaTemplateExport implements WithMultipleSheets, Export
{
    public function sheets(): array
    {
        return [
            new DataSheet(),
            new PetunjukSheet(),
        ];
    }
}
