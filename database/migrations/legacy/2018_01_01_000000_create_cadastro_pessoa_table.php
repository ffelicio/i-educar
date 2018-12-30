<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateCadastroPessoaTable extends Migration
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
                
                CREATE TABLE cadastro.pessoa (
                    idpes numeric(8,0) DEFAULT nextval((\'cadastro.seq_pessoa\'::text)::regclass) NOT NULL,
                    nome character varying(150) NOT NULL,
                    idpes_cad numeric(8,0),
                    data_cad timestamp without time zone NOT NULL,
                    url character varying(60),
                    tipo character(1) NOT NULL,
                    idpes_rev numeric(8,0),
                    data_rev timestamp without time zone,
                    email character varying(100),
                    situacao character(1) NOT NULL,
                    origem_gravacao character(1) NOT NULL,
                    operacao character(1) NOT NULL,
                    idsis_rev integer,
                    idsis_cad integer NOT NULL,
                    CONSTRAINT ck_pessoa_operacao CHECK (((operacao = \'I\'::bpchar) OR (operacao = \'A\'::bpchar) OR (operacao = \'E\'::bpchar))),
                    CONSTRAINT ck_pessoa_origem_gravacao CHECK (((origem_gravacao = \'M\'::bpchar) OR (origem_gravacao = \'U\'::bpchar) OR (origem_gravacao = \'C\'::bpchar) OR (origem_gravacao = \'O\'::bpchar))),
                    CONSTRAINT ck_pessoa_situacao CHECK (((situacao = \'A\'::bpchar) OR (situacao = \'I\'::bpchar) OR (situacao = \'P\'::bpchar))),
                    CONSTRAINT ck_pessoa_tipo CHECK (((tipo = \'F\'::bpchar) OR (tipo = \'J\'::bpchar)))
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
        Schema::dropIfExists('cadastro.pessoa');
    }
}
