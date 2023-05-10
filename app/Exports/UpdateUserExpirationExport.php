<?php

namespace App\Exports;

use App\Models\UserTmp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class UpdateUserExpirationExport implements FromCollection, WithHeadings,WithMapping, WithColumnFormatting
{   
    
    private $admin = null;

    public function __construct($admin){
        $this->admin = $admin;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return UserTmp::select("username", "expiration", "errors")
                        ->where('admin_id', $this->admin)
                        ->where('task_type', 'expiry-update')
                        ->get();
    }

    public function headings(): array
    {
        return ["username", "expiration", "errors"];
    }

    public function map($user): array
    {   
        return [
            // $user->name,
            $user->username,
            // $user->password,
            // $user->nic,
            // $user->mobile,
            // $user->address,
            // $user->package_id,
            // $user->expiration,
            // DateTime::createFromFormat('Y-m-d H:i:s', $user->expiration),
            // Date::dateTimeToExcel('2020-09-25 12:00'),
            // Date::excelToDateTimeObject($user->expiration->toDateTimeString()),
            Date::dateTimeToExcel(date_create($user->expiration)),
            json_decode($user->errors),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY    
        ];
    }
}
