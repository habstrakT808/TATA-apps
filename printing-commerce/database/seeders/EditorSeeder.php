<?php
namespace Database\Seeders;
use App\Models\Editor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class EditorSeeder extends Seeder
{
    private static $tempFile;
    public function __construct(){
        self::$tempFile = database_path('seeders/temp/table.json');
    }
    public function run(): void
    {
        $jsonData = json_decode(file_get_contents(self::$tempFile), true);
        for($i = 1; $i <= 5; $i++){
            $nama = "Editor " . $i;
            $idEditor = Editor::insertGetId([
                'uuid' => Str::uuid(),
                'nama_editor' => $nama,
                'jenis_kelamin' => ['laki-laki', 'perempuan'][rand(0, 1)],
                'no_telpon' => '0855'.mt_rand(00000000,99999999),
            ]);
            $idEditors[] = $idEditor;
        }
        if(!isset($jsonData['editor'])){
            $jsonData['editor'] = [];
        }
        $jsonData['editor'] = array_merge($jsonData['editor'], $idEditors);
        file_put_contents(self::$tempFile,json_encode($jsonData, JSON_PRETTY_PRINT));
    }
}