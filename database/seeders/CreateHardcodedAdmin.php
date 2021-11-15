<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateHardcodedAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $u = User::firstOrCreate(['username'=>'admin'],['username'=>'admin','email'=>'admin@example.com','password'=>'1234']);
        $u->syncRoles(Role::findByName("Admin", "api"));
    }
}
