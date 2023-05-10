<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = ["Category","Blog","Users","Violations","Payments","Shipment","Shipment Tracking","Refund","Shipment Cancellation","Payouts"];
        foreach($permissions as $permission){
            $id = DB::table('permission_types')->insert([
                'name' => $permission,
                'slug' => \Str::slug($permission),
            ]);

            DB::table('permissions')->insert([
                'name' => $permission.' Read',
                'slug' => \Str::slug($permission.' Read'),
                'type_id' => $id,
            ]);
            DB::table('permissions')->insert([
                'name' => $permission.' Write',
                'slug' => \Str::slug($permission.' Write'),
                'type_id' => $id,
            ]);
            DB::table('permissions')->insert([
                'name' => $permission.' Delete',
                'slug' => \Str::slug($permission.' Delete'),
                'type_id' => $id,
            ]);
        }
    }
}
