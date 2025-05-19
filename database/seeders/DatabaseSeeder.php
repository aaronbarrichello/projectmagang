<?php

namespace Database\Seeders;


use Database\Seeders\UserSeeder;
use App\Models\Request;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    private $permissions = [
        'role-list',
        'role-create',
        'role-edit',
        'role-delete',
        'user-list',
        'user-create',
        'user-edit',
        'user-delete',
        'request-list',
        'request-create',
        'request-edit',
        'request-acceptance',
        'request-delete',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $user = User::create([
            'name' => 'SUPER ADMIN',
            'email' => 'superadmin@mail.com',
            'password' => Hash::make('superadmin')
        ]);

        $role = Role::create(['name' => 'superadmin']);

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);

        $role2 = Role::create(['name' => 'admin']);

        $role2->givePermissionTo('request-list');
        // $role2->givePermissionTo('request-create');
        $role2->givePermissionTo('request-edit');
        $role2->givePermissionTo('request-acceptance');
        $role2->givePermissionTo('request-delete');


        // // seeder request & visitor
        // $request1 = Request::create([
        //     'visit_purpose' => 'Data Center',
        //     'start_date' => Carbon::now()->subMonth()->format('Y-m-d H:i:s'),
        //     'end_date' => Carbon::now()->subMonth()->format('Y-m-d H:i:s'),
        //     'description' => 'ini deskripsinya',
        //     'status' => 'requested'
        // ]);
        // $visitor1 = Visitor::create([
        //     'ktp' => '32712931203123',
        //     'name' => 'wenner',
        //     'file_ktp' => 'ktp\32712931203123.jpg',
        //     'company' => 'PT Ayam',
        //     'occupation' => 'CEO',
        //     'phone' => '081923819123',
        //     'email' => 'wennerfrederikus@gmail.com',
        //     'pic' => true
        // ]);
       
        // $request1->visitors()->attach($visitor1->id);

        // $request2 = Request::create([
        //     'visit_purpose' => 'Network Center',
        //     'start_date' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'end_date' => Carbon::now()->format('Y-m-d H:i:s'),
        //     'description' => 'ini deskripsinya 2',
        //     'status' => 'requested'
        // ]);
        // $visitor2 = Visitor::create([
        //     'ktp' => '32712931203121',
        //     'name' => 'adhimas',
        //     'file_ktp' => 'ktp\32712931203121.jpg',
        //     'company' => 'PT Bebek',
        //     'occupation' => 'Sekretaris',
        //     'phone' => '081923819121',
        //     'email' => 'tahtaadhimas123@gmail.com',
        //     'pic' => true
        // ]);
        // $visitor3 = Visitor::create([
        //     'ktp' => '32712931203122',
        //     'name' => 'sonny',
        //     'file_ktp' => 'ktp\32712931203122.jpg',
        //     'company' => 'PT Sapi',
        //     'occupation' => 'Sekretaris',
        //     'phone' => '081923819121',
        //     'email' => 'sonnyaa@mail.com',
        //     'pic' => false
        // ]);
       
        // $request2->visitors()->attach($visitor2->id);
        // $request2->visitors()->attach($visitor3->id);

        
    }
}
