<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name'=>'Admin','guard_name'=>'api']);
        $customerRole = Role::firstOrCreate(['name'=>'Customer','guard_name'=>'api']);

        $adminRole->permissions()->firstOrCreate(['name'=>'all_transactions.view.*', 'guard_name'=>'api']);
        $adminRole->permissions()->firstOrCreate(['name'=>'all_transactions.validate.*', 'guard_name'=>'api']);

        $customerRole->permissions()->firstOrCreate(['name'=>'incomes.create.*', 'guard_name'=>'api']);
        $customerRole->permissions()->firstOrCreate(['name'=>'expenses.create.*', 'guard_name'=>'api']);
        $customerRole->permissions()->firstOrCreate(['name'=>'transactions.view.*', 'guard_name'=>'api']);
    }
}
