<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoiceTaxExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $month = null;

    public function __construct($month){
        $this->month = $month;
    }
    public function collection()
    {
        return Invoice::where('taxed', 0)->whereMonth('created_at', $this->month)->get();
    }

    public function headings(): array
    {
        return ["Invoice ID", "Package Price", "Sale Tax", "Advance INC Tax", "Total"];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_id,
            $invoice->pkg_price,
            $invoice->sales_tax ??0 ,
            $invoice->adv_inc_tax ?? 0,
            $invoice->total,

        ];
    }
}
