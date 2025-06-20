<?php
namespace Database\Seeders;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
class ReviewSeeder extends Seeder
{
    private static $tempFile;
    public function __construct(){
        self::$tempFile = database_path('seeders/temp/table.json');
    }
    public function run(): void
    {
        $jsonData = json_decode(file_get_contents(self::$tempFile), true);
        $ids = [];
        $now = Carbon::now();
        
        for($i = 1; $i <= 5; $i++){
            $ids[] = Review::insertGetId([
                'review' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Deleniti eius dolor illo id cupiditate ea labore obcaecati odit commodi rem laboriosam, quis unde! Ipsam dignissimos temporibus molestiae minus sunt. Praesentium.',
                'rating' => rand(1, 5),
                'id_pesanan' => $jsonData['pesanan'][rand(0, min(99, count($jsonData['pesanan'])-1))],
                'created_at' => $now
            ]);
        }
        if(!isset($jsonData['review'])){
            $jsonData['review'] = [];
        }
        $jsonData['review'] = array_merge($jsonData['review'], $ids);
        file_put_contents(self::$tempFile,json_encode($jsonData, JSON_PRETTY_PRINT));
    }
}