<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the current column type
        $columnInfo = DB::select("SHOW COLUMNS FROM auth WHERE Field = 'role'");
        
        if (!empty($columnInfo)) {
            $columnType = $columnInfo[0]->Type;
            echo "Current role column type: " . $columnType . "\n";
            
            // If it's an enum, modify it to include 'superadmin'
            if (str_contains($columnType, 'enum')) {
                $values = [];
                preg_match("/^enum\(\'(.*)\'\)$/", $columnType, $matches);
                
                if (isset($matches[1])) {
                    $values = explode("','", $matches[1]);
                    
                    // Check if 'superadmin' is already in the enum
                    if (!in_array('superadmin', $values)) {
                        $values[] = 'superadmin';
                        $newEnum = "enum('" . implode("','", $values) . "')";
                        
                        DB::statement("ALTER TABLE auth MODIFY COLUMN role $newEnum");
                        echo "Modified role column to include 'superadmin'\n";
                    } else {
                        echo "'superadmin' is already in the enum values\n";
                    }
                }
            } else {
                // If it's not an enum, change it to VARCHAR to be safe
                Schema::table('auth', function (Blueprint $table) {
                    $table->string('role', 20)->change();
                });
                echo "Changed role column to VARCHAR(20)\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert as we're just expanding the enum or making it more flexible
    }
}; 