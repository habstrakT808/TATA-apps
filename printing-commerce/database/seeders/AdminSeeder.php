<?php
namespace Database\Seeders;
use App\Models\Auth;
use App\Models\Admin;
use Illuminate\Database\Seeder;
Use Illuminate\Support\Facades\Hash;
Use Illuminate\Support\Str;
class AdminSeeder extends Seeder
{
    private static $tempFile;
    public function __construct(){
        self::$tempFile = database_path('seeders/temp/table.json');
    }
    public function run(): void
    {
        $ids = [];
        // Super Admin credentials
        $idAuth = Auth::insertGetId([
            'email' => "SuperAdmin@gmail.com",
            'password'=> Hash::make('Admin@1234567890'),
            'role' => 'super_admin',
        ]);
        $ids[] = Admin::insertGetId([
            'uuid' =>  Str::uuid(),
            'nama_admin' => 'Super Admin',
            'id_auth' => $idAuth,
        ]);

        // Admin Chat credentials
        $idAuth = Auth::insertGetId([
            'email' => "adminchat@gmail.com",
            'password' => Hash::make('Fansspongebobno2!'),
            'role' => 'admin_chat',
        ]);
        $ids[] = Admin::insertGetId([
            'uuid' =>  Str::uuid(),
            'nama_admin' => 'Admin Chat',
            'id_auth' => $idAuth,
        ]);

        // Admin Pengelola pesanan credentials
        $idAuth = Auth::insertGetId([
            'email' => "editor@gmail.com",
            'password' => Hash::make('Fansspongebobno2'),
            'role' => 'admin_pemesanan',
        ]);
        $ids[] = Admin::insertGetId([
            'uuid' =>  Str::uuid(),
            'nama_admin' => 'Admin Pengelola',
            'id_auth' => $idAuth,
        ]);

        // Add additional admin users if needed
        $roles = ['admin_chat', 'admin_pemesanan'];
        foreach($roles as $role){
            $nameRole = ucwords(str_replace('admin_', '', $role));
            for($i = 1; $i <= 5; $i++){
                $idAuth = Auth::insertGetId([
                    'email' => "AdminTesting".$nameRole.$i."@gmail.com",
                    'password' => Hash::make('Admin@1234567890'),
                    'role' => $role,
                ]);
                $ids[] = Admin::insertGetId([
                    'uuid' =>  Str::uuid(),
                    'nama_admin' => 'Admin ' . $nameRole . '' . $i,
                    'id_auth' => $idAuth,
                ]);
            }
        }
        $jsonData = json_decode(file_get_contents(self::$tempFile), true);
        if(!isset($jsonData['admin'])){
            $jsonData['admin'] = [];
        }
        $jsonData['admin'] = array_merge($jsonData['admin'], $ids);
        file_put_contents(self::$tempFile,json_encode($jsonData, JSON_PRETTY_PRINT));
    }
}