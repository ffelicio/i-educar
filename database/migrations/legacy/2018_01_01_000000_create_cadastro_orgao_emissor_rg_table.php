<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroOrgaoEmissorRgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                SET default_with_oids = true;
                
                CREATE TABLE cadastro.orgao_emissor_rg (
                    idorg_rg integer DEFAULT nextval(\'cadastro.orgao_emissor_rg_idorg_rg_seq\'::regclass) NOT NULL,
                    sigla character varying(20) NOT NULL,
                    descricao character varying(60) NOT NULL,
                    situacao character(1) NOT NULL,
                    codigo_educacenso integer,
                    CONSTRAINT ck_orgao_emissor_rg_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar)))
                );
            '
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cadastro.orgao_emissor_rg');
    }
}
