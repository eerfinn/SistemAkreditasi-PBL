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
        // First, change the role column from enum to string to avoid enum constraints
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->change();
        });
        
        // Then migrate existing users with dosen1, dosen2, dosen3 roles to 'dosen' with appropriate kriteria_access
        $this->migrateExistingDosenUsers();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We can't easily convert back to the exact enum, so we'll leave it as string
        // This maintains compatibility with the previous state
    }
    
    /**
     * Migrate existing dosen1, dosen2, dosen3 users to the new 'dosen' role
     * with appropriate kriteria_access values
     */
    private function migrateExistingDosenUsers(): void
    {
        // Migrate dosen1 users (kriteria 1-3)
        $dosen1Users = DB::table('users')->where('role', 'dosen1')->get();
        foreach ($dosen1Users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'role' => 'dosen',
                    'kriteria_access' => json_encode([1, 2, 3])
                ]);
        }
        
        // Migrate dosen2 users (kriteria 4-6)
        $dosen2Users = DB::table('users')->where('role', 'dosen2')->get();
        foreach ($dosen2Users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'role' => 'dosen',
                    'kriteria_access' => json_encode([4, 5, 6])
                ]);
        }
        
        // Migrate dosen3 users (kriteria 7-9)
        $dosen3Users = DB::table('users')->where('role', 'dosen3')->get();
        foreach ($dosen3Users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'role' => 'dosen',
                    'kriteria_access' => json_encode([7, 8, 9])
                ]);
        }
    }
};
