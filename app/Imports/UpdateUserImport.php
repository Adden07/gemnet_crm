<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\UserTmp;
use App\Models\FileLog;
class UpdateUserImport implements ToCollection, WithHeadingRow
{   
    private $admin_id = null;
    private $rows = 0;

    public function __construct($admin_id){
        $this->admin_id   = $admin_id;

    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $arr        = array();
        $task_id    = UserTmp::orderBy('id', 'DESC')->first();
        ($task_id == null) ? $task_id = 1 : $task_id = $task_id->task_id + 1; 

        foreach($rows AS $key=>$row){
            if(!empty($row['name']) && !empty($row['username']) && !empty($row['nic']) && !empty($row['mobile'])){
                    ++$this->rows;
                    $arr[$key] = [
                        'task_id'       => $task_id,
                        'task_datetime' => date('Y-m-d H:i:s'),
                        'task_type'     => 'update-user',
                        'admin_id'      => $this->admin_id,
                        'name'          => $row['name'],
                        'username'      => $row['username'],
                        'nic'           => $row['nic'],
                        'mobile'        => $row['mobile'],
                        'address'       => $row['address'],
                    ];
            }

        }

        UserTmp::insert($arr);


    }

    public function headingRow(): int
    {
        return 1;
    }

        
    public function getRowCount(): int
    {
        return $this->rows;
    }

}
