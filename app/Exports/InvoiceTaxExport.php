<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class InvoiceTaxExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $date = null;

    public function __construct($date){
        $this->date = $date;
    }
    public function collection()
    {   
        return Invoice::with(['user'])->where('tax_paid', 0)->where('created_at', $this->date)->get();
    }

    public function headings(): array
    {
        return ["NTN", "CNIC", "Name Of Buyer", "District Of Buyer", "Buyer Type", "Document Type", "Document Number", "Document Date", "HS Code", "Sale Type", "Rate", "Value Of Sales Exluding Sales Tax", "Sales Tax Involve", "ST Withheld at Source"];
    }

    public function map($invoice): array
    {
        return [
            $invoice->user->ntn,
            $invoice->user->ntn ?? $invoice->user->nic,
            $invoice->user->name,
            'Hyderabad',
            'End_Consumer',
            'SI',
            $invoice->invoice_id,
            Date::dateTimeToExcel(date_create($invoice->created_at)),
            '',
            'Services',
            '19.50',
            $invoice->pkg_price,
            $invoice->sales_tax,
            ''
        ];
    }
}
