<?php

namespace App\Exports;
use App\Models\Invoice;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvoiceTaxFbrExport implements FromCollection,WithHeadings, WithMapping
{
    private $month = null;

    public function __construct($month){
        $this->month = $month;
    }
    public function collection()
    {
        return Invoice::with(['user'])->where('taxed', 0)->whereMonth('created_at', $this->month)->get();
    }

    public function headings(): array
    {
        return ["Payment Section", "TaxPayer_NTN", "TaxPayer_CNIC", "TAXPayer_NAME", "TaxPayer_City", "TaxPayer_Address", "TaxPayer _Satus", "TaxPayer_Business", "Taxable_Amount", "Tax_Amount"];
    }

    public function map($invoice): array
    {
        return [
            '236(1)(d)',
            $invoice->user->ntn,
            (is_null($invoice->user->ntn)) ? $invoice->user->nic : '',
            $invoice->user->name,
            'Hyderabad',
            'Hyderabad',
            $invoice->user->user_type,
            $invoice->user->business_name,
            $invoice->pkg_price,
            $invoice->sales_tax,
        ];
    }
}
