<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\UserTmp;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class UsersImport implements ToCollection, WithHeadingRow
{   
    
    private $city_id = null;

    public function __construct($city_id){
        $this->city_id = $city_id;
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
            if(!empty($row['name']) && !empty($row['username']) && !empty($row['package']) && !empty($row['expiration'])){
                // dd('done');
                @$date = Date::excelToDateTimeObject(@$row['expiration'])->format('Y-m-d 12:00:00');
                if($date != 1970){
                    $arr[$key] = [
                        'task_id'       => $task_id,
                        'task_datetime' => date('Y-m-d H:i:s'),
                        'admin_id'      => 1,
                        'city_id'       => $this->city_id,
                        'name'          => $row['name'],
                        'username'      => $row['username'],
                        'password'      => $row['password'],
                        'nic'           => $row['cnic'],
                        'mobile'        => $row['mobile'],
                        'address'       => $row['address'],
                        'package_id'    => $row['package'],
                        'expiration'    => $date,
                    ];
                }
            }
            
        }
        // dd($arr);
        UserTmp::insert($arr);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
