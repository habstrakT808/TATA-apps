<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Jasa;
use App\Models\JasaImage;

class JasaImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Make sure the destination directories exist
        $galleryDir = public_path('assets3/img/jasa');
        if (!File::exists($galleryDir)) {
            File::makeDirectory($galleryDir, 0755, true);
        }
        // Get all jasas
        $jasas = Jasa::all();
        // Process each jasa
        foreach ($jasas as $jasa) {
            $this->seedImagesForJasa($jasa, $galleryDir);
        }
        
        $this->command->info('JasaImage seeding completed successfully!');
    }
    
    /**
     * Seed images for a specific jasa
     * 
     * @param Jasa $jasa
     * @param string $sourceDir
     * @param string $galleryDir
     */
    private function seedImagesForJasa($jasa, $galleryDir)
    {
        $categoryDir = $galleryDir . '/' . $jasa->kategori;
        if (!File::exists($categoryDir)) {
            $categoryDir = $galleryDir;
        }
        $galleryCount = rand(3, 5);
        for($i = 0; $i < $galleryCount; $i++){
            $sourceDir = database_path('seeders/resources/img/jasa');
            $imageFiles = collect(File::files($sourceDir))
                ->filter(function ($file) {
                    return in_array($file->getExtension(), ['jpg', 'jpeg', 'png']);
                })
                ->shuffle()
                ->first();
            if (!$imageFiles) {
                continue;
            }
            $imageName = $jasa->id_jasa . ($i + 1) . '.' . $imageFiles->getExtension();
            $categoryDir = $jasa->kategori;
            $destinationPath = $galleryDir . '/' . $categoryDir;
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            File::copy($imageFiles->getPathname(), $destinationPath . '/' . $imageName);
            JasaImage::create([
                'image_path' => $imageName,
                'id_jasa' => $jasa->id_jasa,
            ]);
        }
    }
} 