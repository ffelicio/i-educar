<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysOnSerieciascEscolaAguaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('serieciasc.escola_agua', function (Blueprint $table) {
            $table->foreign('ref_cod_escola')
               ->references('cod_escola')
               ->on('pmieducar.escola')
               ->onUpdate('restrict')
               ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('serieciasc.escola_agua', function (Blueprint $table) {
            $table->dropForeign(['ref_cod_escola']);
        });
    }
}