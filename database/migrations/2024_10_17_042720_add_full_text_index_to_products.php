<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('text')
                ->nullable();

            if (!$this->isSqlite()){
                $table->fullText(['title', 'text']);
            }

        });
    }


    public function down(): void
    {
        if(!app()->isProduction){
            Schema::table('products', function (Blueprint $table) {
                //
            });
        }
    }


    private function isSqlite(): bool
    {
        return 'sqlite' === Schema::connection($this->getConnection())
            ->getConnection()
            ->getPdo()
            ->getAttribute(PDO::ATTR_DRIVER_NAME);
    }
};
