<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('propostas', function (Blueprint $table) {
            $table->id();
            $table->string('cliente_nome');
            $table->string('cliente_cpf', 14)->index();
            $table->decimal('cliente_salario', 12, 2);
            $table->decimal('valor_solicitado', 12, 2);
            $table->integer('prazo_meses');
            $table->decimal('taxa_juros', 8, 4)->default(2.5); 
            $table->decimal('valor_parcela', 12, 2);
            $table->decimal('valor_total', 12, 2);
            $table->decimal('margem_disponivel', 12, 2);
            $table->enum('status', ['rascunho','em_analise','aprovada','reprovada','cancelada'])->default('rascunho');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();          
        });
    }

    public function down()
    {
        Schema::dropIfExists('propostas');
    }
};
