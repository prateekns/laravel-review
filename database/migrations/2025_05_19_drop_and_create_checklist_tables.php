<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign key constraints first if table exists
        if (Schema::hasTable('work_order_checklists')) {
            Schema::table('work_order_checklists', function (Blueprint $table) {
                // Check if the foreign key exists before dropping
                $foreignKeys = $this->getForeignKeys('work_order_checklists');
                if (in_array('work_order_checklists_checklist_item_id_foreign', $foreignKeys)) {
                    $table->dropForeign(['checklist_item_id']);
                }
            });
        }

        // Drop tables in reverse order of dependencies
        Schema::dropIfExists('work_order_checklists');
        Schema::dropIfExists('checklist_items');
        Schema::dropIfExists('checklists');

        // Create checklist_items table
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('templates')->onDelete('cascade');
            $table->string('item_text');
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['template_id', 'sort_order']);
            $table->index('business_id');
        });

        // Create work_order_checklists table
        Schema::create('work_order_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('checklist_item_id')->nullable()->constrained('checklist_items')->onDelete('set null');
            $table->text('item_text');
            $table->tinyInteger('service_type')->default(1); // 1 for Cleaning, 2 for Maintenance
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop in reverse order
        Schema::dropIfExists('work_order_checklists');
        Schema::dropIfExists('checklist_items');
    }

    /**
     * Get all foreign key names for a table
     */
    private function getForeignKeys(string $tableName): array
    {
        $foreignKeys = [];
        $database = config('database.connections.mysql.database');

        $constraints = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = '$database' 
            AND TABLE_NAME = '$tableName' 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        foreach ($constraints as $constraint) {
            $foreignKeys[] = $constraint->CONSTRAINT_NAME;
        }

        return $foreignKeys;
    }
};
