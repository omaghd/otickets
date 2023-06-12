<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['unassigned', 'assigned', 'resolved', 'closed'])
                ->default('unassigned');
            $table->enum('priority', ['low', 'medium', 'high'])
                ->nullable();
            $table->dateTime('resolved_at')
                ->nullable();
            $table->dateTime('closed_at')
                ->nullable();
            $table->foreignIdFor(Category::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignIdFor(User::class, 'client_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
