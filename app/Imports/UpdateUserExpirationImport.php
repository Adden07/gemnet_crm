<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\UserTmp;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UpdateUserExpirationImport implements ToCollection, WithHeadingRow
{
    private $admin_id = null;

    public function __construct($admin_id){
        $this->admin_id = $admin_id;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {   
        $arr = array();

        $task_id = UserTmp::orderBy('id', 'DESC')->first();

        ($task_id == null) ? $task_id = 1 : $task_id = $task_id->task_id + 1; 
        
        foreach($rows AS $key=>$row){
            if(!empty($row['username']) && !empty($row['expiration'])){
                @$date = Date::excelToDateTimeObject(@$row['expiration'])->format('Y-m-d 12:00:00');
                if($date != 1970){
                    $arr[$key] = [
                        'task_id'       => $task_id,
                        'task_datetime' => date('Y-m-d H:i:s'),
                        'admin_id'      => $this->admin_id,
                        // 'name'          => $row['name'],
                        'username'      => $row['username'],
                        // 'password'      => $row['password'],
                        // 'nic'           => $row['nic'],
                        // 'mobile'        => $row['mobile'],
                        // 'address'       => $row['address'],
                        // 'package_id'    => $row['package'],
                        'expiration'    => $date,
                        'task_type'     => 'expiry-update'
                    ];
                }
            }

        }
        UserTmp::insert($arr);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
