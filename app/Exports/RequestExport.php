<?php

namespace App\Exports;

use App\Models\Request as ModelsRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class RequestExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct(String $year, String $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function query()
    {
        return ModelsRequest::query()->whereYear('start_date', $this->year)
            ->whereMonth('start_date', $this->month)->with('visitors');
    }

    public function map($data): array
    {
        return [
            $data->start_date,
            $data->end_date,
            $data->visit_purpose,
            $data->status,
            $data->description,
            $data->visitors->where('pic', 1)->first()->name
        ];
    }

    public function headings(): array
    {
        return [
            'Start Date',
            'End Date',
            'Visit Purpose',
            'Status',
            'Description',
            'Visitor'
        ];
    }
}
