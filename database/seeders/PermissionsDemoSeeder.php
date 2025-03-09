<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();
        DB::table('roles')->delete();
        DB::table('role_has_permissions')->delete();
        DB::table('users')->delete();

        //
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Menu
        Permission::create(['name' => 'show-master','guard_name'=>'web']);
        Permission::create(['name' => 'show-input-data','guard_name'=>'web']);
        Permission::create(['name' => 'show-transaction','guard_name'=>'web']);
        Permission::create(['name' => 'show-laporan','guard_name'=>'web']);
        Permission::create(['name' => 'show-settings','guard_name'=>'web']);

        // Master Tahapan
        Permission::create(['name' => 'delete-product','guard_name'=>'web']);
        Permission::create(['name' => 'create-product','guard_name'=>'web']);
        Permission::create(['name' => 'show-product','guard_name'=>'web']);
        Permission::create(['name' => 'list-product','guard_name'=>'web']);

        // Transaction Work Ordersrk Orders
        Permission::create(['name' => 'edit-wo','guard_name'=>'web']);
        Permission::create(['name' => 'delete-wo','guard_name'=>'web']);
        Permission::create(['name' => 'create-wo','guard_name'=>'web']);
        Permission::create(['name' => 'show-wo','guard_name'=>'web']);
        Permission::create(['name' => 'show-wo-batal','guard_name'=>'web']);
        Permission::create(['name' => 'upload-wo','guard_name'=>'web']);
        Permission::create(['name' => 'sync-wo','guard_name'=>'web']);
        Permission::create(['name' => 'cetak-wo','guard_name'=>'web']);
        Permission::create(['name' => 'delete-rincian-wo','guard_name'=>'web']);
        Permission::create(['name' => 'edit-rincian-wo','guard_name'=>'web']);
        Permission::create(['name' => 'upload-wo-rincian','guard_name'=>'web']);
        Permission::create(['name' => 'approve-wo','guard_name'=>'web']);

        // report
        Permission::create(['name' => 'show-wo-reports','guard_name'=>'web']);
        Permission::create(['name' => 'show-wo-reports-export-excel','guard_name'=>'web']);

        // Role
        Permission::create(['name' => 'create-role','guard_name'=>'web']);
        Permission::create(['name' => 'show-role','guard_name'=>'web']);
        Permission::create(['name' => 'edit-role','guard_name'=>'web']);
        Permission::create(['name' => 'delete-role','guard_name'=>'web']);

        // users
        Permission::create(['name' => 'create-users','guard_name'=>'web']);
        Permission::create(['name' => 'show-users','guard_name'=>'web']);
        Permission::create(['name' => 'edit-users','guard_name'=>'web']);
        Permission::create(['name' => 'delete-users','guard_name'=>'web']);


        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Product Manager','guard_name'=>'web']);
        $role1->givePermissionTo('show-master');
        $role1->givePermissionTo('show-transaction');
        $role1->givePermissionTo('show-laporan');
        // Master Product
        $role1->givePermissionTo('show-product');
        // Transaction Work Ordersrk Orders
        $role1->givePermissionTo('show-wo');
        $role1->givePermissionTo('show-wo-batal');
        $role1->givePermissionTo('edit-wo');
        $role1->givePermissionTo('delete-wo');
        $role1->givePermissionTo('cetak-wo');
        $role1->givePermissionTo('delete-rincian-wo');
        $role1->givePermissionTo('edit-rincian-wo');
        $role1->givePermissionTo('sync-wo');
        $role1->givePermissionTo('upload-wo-rincian');
        $role1->givePermissionTo('upload-wo');
        $role1->givePermissionTo('approve-wo');

        // Report
        $role1->givePermissionTo('show-wo-reports');
        $role1->givePermissionTo('show-wo-reports-export-excel');

        $role2 = Role::create(['name' => 'Operator','guard_name'=>'web']);
        $role2->givePermissionTo('show-transaction');
        $role2->givePermissionTo('show-laporan');
        $role2->givePermissionTo('show-master');

        // Transaction STS
        $role2->givePermissionTo('show-wo');
        $role2->givePermissionTo('show-wo-batal');
        $role2->givePermissionTo('edit-wo');
        $role2->givePermissionTo('cetak-wo');
        $role2->givePermissionTo('approve-wo');

        // Report
        $role1->givePermissionTo('show-wo-reports');

        $superadminRole = Role::create(['name' => 'administrator']);

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Example User',
            'username'=> 'product-manager01'
        ]);
        $user->assignRole($role1);

        $user = \App\Models\User::factory()->create([

            'username'=> 'operator1'
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([

            'username'=> 'operator2'
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([

            'username'=> 'operator3'
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([

            'username'=> 'operator4'
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([

            'username'=> 'operator5'
        ]);

        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([
            'name' => 'Example superadmin user',
            'email' => 'superadmin@erevenue.com',
            'username' => 'admin1',
            'password' => bcrypt('12345678')
        ]);
        $user->assignRole($superadminRole);
    }
}
