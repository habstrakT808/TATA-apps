<?php
namespace Database\Seeders;

use App\Models\Revisi;
use App\Models\RevisiUser;
use App\Models\RevisiEditor;
use App\Models\Pesanan;
use App\Models\Editor;
use Illuminate\Database\Seeder;

class RevisiSeeder extends Seeder
{
    public function run(): void
    {
        // Sample revision notes
        $userRevisionNotes = [
            'Tolong ubah warna background menjadi lebih terang',
            'Font terlalu kecil, mohon diperbesar',
            'Logo perusahaan kurang jelas, bisa diperjelas?',
            'Tata letak masih kurang rapi, mohon diperbaiki',
            'Warna teks sulit dibaca, bisa diganti?',
            'Posisi logo kurang tepat, mohon dipindah ke kanan',
            'Tambahkan border pada gambar utama',
            'Ukuran font title terlalu besar, mohon dikecilkan',
            'Kontras warna kurang jelas, bisa diperbaiki?',
            'Spacing antar elemen terlalu rapat'
        ];
        
        $editorNotes = [
            'Sudah diperbaiki sesuai permintaan',
            'Preview versi terbaru sudah disesuaikan',
            'Final version setelah revisi lengkap',
            'Mohon dicek kembali hasilnya',
            'Hasil akhir siap untuk review',
            'Perubahan sudah diterapkan',
            'Versi baru dengan perbaikan',
            'Silakan cek hasilnya',
            'Revisi sudah selesai dikerjakan',
            'File final sudah ready'
        ];
        
        // Get editors for assignment
        $editorList = Editor::take(5)->get();
        if ($editorList->isEmpty()) {
            $this->command->info('No editors found, skipping revision seeder');
            return;
        }

        // 1. CREATE REVISIONS FOR PESANAN WITH STATUS 'revisi' (currently in revision)
        $pesananInRevision = Pesanan::where('status_pesanan', 'revisi')
            ->whereNotNull('assigned_at')
            ->get();
            
        foreach ($pesananInRevision as $pesanan) {
            // Create 1-2 revisions for each pesanan in revision status
            $numRevisions = rand(1, 2);
            
            for ($revisionNum = 1; $revisionNum <= $numRevisions; $revisionNum++) {
                // Revision created after pesanan was assigned
                $revisionCreatedAt = $pesanan->assigned_at->copy()->addDays(rand(1, 3));
                
                // Create revision record
                $revision = Revisi::create([
                    'urutan_revisi' => $revisionNum,
                    'id_pesanan' => $pesanan->id_pesanan,
                    'created_at' => $revisionCreatedAt,
                    'updated_at' => $revisionCreatedAt
                ]);

                // User uploads revision files
                for ($fileNum = 1; $fileNum <= rand(1, 2); $fileNum++) {
                    RevisiUser::create([
                        'nama_file' => "user_revision_r{$revisionNum}_f{$fileNum}_{$pesanan->id_pesanan}.pdf",
                        'catatan_user' => $userRevisionNotes[rand(0, count($userRevisionNotes) - 1)],
                        'id_revisi' => $revision->id_revisi,
                        'id_user' => $pesanan->id_user,
                        'created_at' => $revisionCreatedAt,
                        'updated_at' => $revisionCreatedAt
                    ]);
                }
                
                // Editor responds to revision (90% chance)
                if (rand(1, 10) <= 9) {
                    $editorResponseAt = $revisionCreatedAt->copy()->addHours(rand(4, 48));
                    
                    // Assign random editor for this revision
                    $assignedEditor = $editorList->random();
                    
                    RevisiEditor::create([
                        'nama_file' => "editor_response_r{$revisionNum}_{$pesanan->id_pesanan}.pdf",
                        'catatan_editor' => $editorNotes[rand(0, count($editorNotes) - 1)],
                        'id_editor' => $assignedEditor->id_editor,
                        'id_revisi' => $revision->id_revisi,
                        'created_at' => $editorResponseAt,
                        'updated_at' => $editorResponseAt
                    ]);
                }
            }
        }

        // 2. CREATE COMPLETE REVISION HISTORY FOR COMPLETED PESANAN
        $completedPesanan = Pesanan::where('status_pesanan', 'selesai')
            ->whereNotNull('completed_at')
            ->take(10)
            ->get();
            
        foreach ($completedPesanan as $pesanan) {
            // Create complete revision history (1-3 revisions)
            $numRevisions = rand(1, 3);
            
            for ($revisionNum = 1; $revisionNum <= $numRevisions; $revisionNum++) {
                $revisionCreatedAt = $pesanan->assigned_at->copy()->addDays($revisionNum);
                
                // Create revision record
                $revision = Revisi::create([
                    'urutan_revisi' => $revisionNum,
                    'id_pesanan' => $pesanan->id_pesanan,
                    'created_at' => $revisionCreatedAt,
                    'updated_at' => $revisionCreatedAt
                ]);

                // User uploads revision files
                for ($fileNum = 1; $fileNum <= rand(1, 2); $fileNum++) {
                    RevisiUser::create([
                        'nama_file' => "completed_revision_r{$revisionNum}_f{$fileNum}_{$pesanan->id_pesanan}.pdf",
                        'catatan_user' => $userRevisionNotes[rand(0, count($userRevisionNotes) - 1)],
                        'id_revisi' => $revision->id_revisi,
                        'id_user' => $pesanan->id_user,
                        'created_at' => $revisionCreatedAt,
                        'updated_at' => $revisionCreatedAt
                    ]);
                }
                
                // Editor always responds for completed pesanan
                $editorResponseAt = $revisionCreatedAt->copy()->addHours(rand(4, 24));
                
                // Assign random editor for this revision
                $assignedEditor = $editorList->random();
                
                // If this is the last revision, make it final
                $fileType = ($revisionNum === $numRevisions) ? 'final' : 'preview';
                
                RevisiEditor::create([
                    'nama_file' => "completed_editor_r{$revisionNum}_{$pesanan->id_pesanan}.pdf",
                    'catatan_editor' => $editorNotes[rand(0, count($editorNotes) - 1)],
                    'id_editor' => $assignedEditor->id_editor,
                    'id_revisi' => $revision->id_revisi,
                    'created_at' => $editorResponseAt,
                    'updated_at' => $editorResponseAt
                ]);
            }
        }

        // 3. CREATE INITIAL BRIEF FILES FOR PESANAN BEING WORKED ON
        $workingPesanan = Pesanan::where('status_pesanan', 'dikerjakan')
            ->whereNotNull('assigned_at')
            ->get();
            
        foreach ($workingPesanan as $pesanan) {
            // Create initial revisi files (uploaded when pesanan was created)
            for ($fileNum = 1; $fileNum <= rand(1, 3); $fileNum++) {
                RevisiUser::create([
                    'nama_file' => "initial_brief_f{$fileNum}_{$pesanan->id_pesanan}.pdf",
                    'catatan_user' => "File revisi awal untuk pesanan",
                    'id_revisi' => null, // No revision, this is initial revisi
                    'id_user' => $pesanan->id_user,
                    'created_at' => $pesanan->created_at,
                    'updated_at' => $pesanan->created_at
                ]);
            }
            
            // Create initial editor preview (80% chance)
            if (rand(1, 10) <= 8) {
                $previewAt = $pesanan->assigned_at->copy()->addHours(rand(12, 72));
                
                // Assign random editor for initial preview
                $assignedEditor = $editorList->random();
                
                RevisiEditor::create([
                    'nama_file' => "initial_preview_{$pesanan->id_pesanan}.pdf",
                    'catatan_editor' => "Preview awal untuk review",
                    'id_revisi' => null, // No revision, this is initial preview
                    'id_editor' => $assignedEditor->id_editor,
                    'created_at' => $previewAt,
                    'updated_at' => $previewAt
                ]);
            }
        }
    }
}