<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use App\Models\User;
use App\Models\FileLog;

class UpdateUserExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{   

    private $admin_id  = null;
    private $file_name = null;
    public function __construct($admin_id, $file_name){
        $this->admin_id = $admin_id;
        $this->file_name = $file_name;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {  
        
        $user = User::select('id', 'admin_id', 'username', 'name', 'nic', 'mobile', 'address')->where('admin_id', hashids_decode($this->admin_id))->get();
        //if admin_id exists then update ortherwise create a file log new entry
        $file              = FileLog::firstOrNew(['admin_id'=>hashids_decode($this->admin_id)]);
        $file->admin_id    = hashids_decode($this->admin_id);
        $file->file_name   = $this->file_name;
        $file->total_users = count($user);
        $file->export_date = date('Y-m-d H:i:s');
        $file->save();

        return $user;
        // return User::select('id', 'admin_id', 'username', 'name', 'nic', 'mobile', 'address')->where('admin_id', hashids_decode($this->admin_id))->get();
    }

    public function headings(): array
    {
        return ["username", "name", "nic", "mobile", "address"];
    }

    public function map($user): array
    {   
        return [
            $user->username,
            $user->name,
            $user->nic,
            $user->mobile,
            $user->address,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY    
        ];
    }    
}
