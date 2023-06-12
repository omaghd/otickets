<?php

use App\Models\TicketReply;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reply_attachments', function (Blueprint $table) {
            $table->id();
            $table->text('filename');
            $table->text('path');
            $table->foreignIdFor(TicketReply::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reply_attachments');
    }
};
