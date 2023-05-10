<?php

namespace App\Exports;

use App\Models\UserTmp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class UserTmpExport implements FromCollection, WithHeadings,WithMapping, WithColumnFormatting
{   
    
    private $task_id = null;

    public function __construct($task_id){
        $this->task_id = $task_id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return UserTmp::select("name", "username", "password", "nic", "mobile", "address", "package_id", "expiration", "errors")
                        ->where('task_id', $this->task_id)
                        ->get();
    }

    public function headings(): array
    {
        return ["name", "username", "password", "nic", "mobile", "address", "package", "expiration", "errors"];
    }

    public function map($user): array
    {   
        return [
            $user->name,
            $user->username,
            $user->password,
            $user->nic,
            $user->mobile,
            $user->address,
            $user->package_id,
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
