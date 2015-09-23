<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';

require_once 'App/Model/MatriculaSituacao.php';
require_once 'Portabilis/View/Helper/Application.php';
/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matr�cula');
    $this->processoAp = 578;
    $this->addEstilo("localizacaoSistema");
  }
}



/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  var $ref_cod_matricula;
  var $ref_cod_reserva_vaga;
  var $ref_ref_cod_escola;
  var $ref_ref_cod_serie;
  var $ref_cod_abandono_tipo;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_aluno;
  var $aprovado;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  function Gerar()
  {

    // carrega estilo para feedback messages, exibindo msgs da api.

    $style = "/modules/Portabilis/Assets/Stylesheets/Frontend.css";
    Portabilis_View_Helper_Application::loadStylesheet($this, $style);

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = "Matr�cula - Detalhe";
    $this->addBanner("imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet");

    $this->ref_cod_matricula = $_GET["cod_matricula"];

    $obj_matricula = new clsPmieducarMatricula();
    $lst_matricula = $obj_matricula->lista($this->ref_cod_matricula);

    if ($lst_matricula) {
      $registro = array_shift($lst_matricula);
    }

    if (! $registro) {
      header("Location: educar_aluno_det.php?cod_aluno=" . $registro['ref_cod_aluno']);
      die();
    }

    // Curso
    $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
    $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

    // S�rie
    $obj_serie = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
    $det_serie = $obj_serie->detalhe();
    $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

    // Nome da institui��o
    $obj_cod_instituicao = new clsPmieducarInstituicao( $registro['ref_cod_instituicao'] );
    $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

    // Nome da escola
    $obj_ref_cod_escola = new clsPmieducarEscola( $registro['ref_ref_cod_escola'] );
    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
    $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];

    // Nome do aluno
    $obj_aluno = new clsPmieducarAluno();
    $lst_aluno = $obj_aluno->lista($registro['ref_cod_aluno'], NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, 1);

    if (is_array($lst_aluno)) {
      $det_aluno = array_shift($lst_aluno);
      $nm_aluno = $det_aluno['nome_aluno'];
    }

    if ($registro['cod_matricula']) {
      $this->addDetalhe(array('N�mero Matr�cula', $registro['cod_matricula']));
    }

    if ($nm_aluno) {
      $this->addDetalhe(array('Aluno', $nm_aluno));
    }

    if ($registro['ref_cod_instituicao']) {
      $this->addDetalhe(array('Institui��o', $registro['ref_cod_instituicao']));
    }

    if ($registro['ref_ref_cod_escola']) {
      $this->addDetalhe(array('Escola', $registro['ref_ref_cod_escola']));
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Curso', $registro['ref_cod_curso']));
    }

    if ($registro['ref_ref_cod_serie']) {
      $this->addDetalhe(array('S�rie', $registro['ref_ref_cod_serie']));
    }

    // Nome da turma
    $enturmacoes = new clsPmieducarMatriculaTurma();
    $enturmacoes = $enturmacoes->lista($this->ref_cod_matricula, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, 1);

    $existeTurma = false;
    $nomesTurmas = array();
    $datasEnturmacoes = array();
    foreach ($enturmacoes as $enturmacao) {
      $turma         = new clsPmieducarTurma($enturmacao['ref_cod_turma']);
      $turma         = $turma->detalhe();
      $nomesTurmas[] = $turma['nm_turma'];
      $datasEnturmacoes[] = Portabilis_Date_Utils::pgSQLToBr($enturmacao['data_enturmacao']);
    }
    $nomesTurmas = implode('<br />', $nomesTurmas);
    $datasEnturmacoes = implode('<br />', $datasEnturmacoes);

    if ($nomesTurmas){
      $this->addDetalhe(array('Turma', $nomesTurmas));
      $this->addDetalhe(array('Data Enturma��o Atual', $datasEnturmacoes));
      $existeTurma = true;
    }else {
      $this->addDetalhe(array('Turma', ''));
      $this->addDetalhe(array('Data Enturma��o', ''));
    }

    if ($registro['ref_cod_reserva_vaga']) {
      $this->addDetalhe(array('N�mero Reserva Vaga', $registro['ref_cod_reserva_vaga']));
    }

    $campoObs = false;

    if ($registro['aprovado']) {
      if ($registro['aprovado'] == 1) {
        $aprovado = 'Aprovado';
      }
      elseif ($registro['aprovado'] == 2) {
        $aprovado = 'Reprovado';
      }
      elseif ($registro['aprovado'] == 3) {
        $aprovado = 'Em Andamento';
      }
      elseif ($registro['aprovado'] == 4) {
        $aprovado = 'Transferido';
      }
      elseif ($registro['aprovado'] == 5) {
        $aprovado = 'Reclassificado';
      }
      elseif ($registro['aprovado'] == 6) {
        $aprovado = 'Abandono';
        $campoObs = true;
      }
      elseif ($registro['aprovado'] == 7) {
        $aprovado = 'Em Exame';
      }
      elseif ($registro['aprovado'] == 12) {
        $aprovado = 'Aprovado com depend&ecirc;ncia';
      }

      $this->addDetalhe(array('Situa��o', $aprovado));
    }

    if($registro[aprovado] == 4){
      $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();

      $lst_transferencia = $obj_transferencia->lista(NULL, NULL, NULL, NULL,
        NULL, $registro['cod_matricula'], NULL, NULL, NULL, NULL, NULL, 1,
        NULL, NULL, $registro['ref_cod_aluno'], FALSE);

      if (is_array($lst_transferencia)) {
        $det_transferencia = array_shift($lst_transferencia);
      }
      // echo "<pre>"; var_dump($det_transferencia["ref_cod_escola_destino"]); die;
      if(!is_null($det_transferencia["ref_cod_escola_destino"])) {
        $tmp_obj = new clsPmieducarEscola($det_transferencia["ref_cod_escola_destino"]);
        $tmp_det = $tmp_obj->detalhe();
        $this->addDetalhe(array("Escola destino", $tmp_det["nome"]));
      }else{
        $this->addDetalhe(array("Escola destino", $det_transferencia["escola_destino_externa"]));
      }
    }

    if ($campoObs){

      $tipoAbandono = new clsPmieducarAbandonoTipo($registro['ref_cod_abandono_tipo']);
      $tipoAbandono = $tipoAbandono->detalhe();

      $this->addDetalhe(array('Motivo do Abandono',$tipoAbandono['nome']));
      $this->addDetalhe(array('Observa��o',$registro['observacao']));
    }

    $this->addDetalhe(array('Formando', $registro['formando'] == 0 ? 'N&atilde;o' : 'Sim'));

    $obj_permissoes = new clsPermissoes();
    if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
      // verifica se existe transferencia
      if ($registro['aprovado'] != 4 && $registro['aprovado'] != 6) {
        $obj_transferencia = new clsPmieducarTransferenciaSolicitacao();

        $lst_transferencia = $obj_transferencia->lista(NULL, NULL, NULL, NULL,
          NULL, $registro['cod_matricula'], NULL, NULL, NULL, NULL, NULL, 1,
          NULL, NULL, $registro['ref_cod_aluno'], FALSE);

        // verifica se existe uma solicitacao de transferencia INTERNA
        if (is_array($lst_transferencia)) {
          $det_transferencia = array_shift($lst_transferencia);
        }

        $data_transferencia = $det_transferencia['data_transferencia'];
      }

      if ($registro['aprovado'] == 3 &&
         (!is_array($lst_transferencia) && !isset($data_transferencia))
      ) {

        // Verificar se tem permissao para executar cancelamento de matricula
        if($this->permissao_cancelar()){

          $this->array_botao[]            = 'Cancelar matr�cula';
          $this->array_botao_url_script[] = "if(confirm(\"Deseja realmente cancelar esta matr�cula?\"))go(\"educar_matricula_cad.php?cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
        }

        $this->array_botao[]            = 'Ocorr�ncias disciplinares';
        $this->array_botao_url_script[] = "go(\"educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";

        // Apenas libera a dispensa de disciplina quando o aluno estiver enturmado
        //
        if ($registro['ref_ref_cod_serie'] && $existeTurma) {
          $this->array_botao[]            = 'Dispensa de componentes curriculares';
          $this->array_botao_url_script[] = "go(\"educar_dispensa_disciplina_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
        }

        if ($registro['ref_ref_cod_serie'] && $existeTurma) {
          $this->array_botao[]            = 'Disciplinas de depend&ecirc;ncia';
          $this->array_botao_url_script[] = "go(\"educar_disciplina_dependencia_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";
        }

        $this->array_botao[]            = ( $GLOBALS['coreExt']['Config']->app->database->dbname == 'botucatu' ? 'Troca de sala / Remanejamento' :'Enturmar' );
        $this->array_botao_url_script[] = "go(\"educar_matricula_turma_lst.php?ref_cod_matricula={$registro['cod_matricula']}\")";

        $this->array_botao[]            = 'Abandono';
        $this->array_botao_url_script[] = "go(\"educar_abandono_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\");";

        if ($registro['ref_ref_cod_serie']) {
          $this->array_botao[]            = 'Reclassificar';
          $this->array_botao_url_script[] = "go(\"educar_matricula_reclassificar_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
        }
      }

      if ($registro['aprovado'] != 4 && $registro['aprovado'] != 6) {
        if (is_array($lst_transferencia) && !isset($data_transferencia)) {
          $this->array_botao[]            = 'Cancelar solicita��o transfer�ncia (escola do sistema)';
          $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true\")";
        }
        elseif ($registro['aprovado'] == App_Model_MatriculaSituacao::EM_ANDAMENTO) {
          if ($registro['ref_ref_cod_serie']) {
            $this->array_botao[]            = ( $GLOBALS['coreExt']['Config']->app->database->dbname == 'botucatu' ? 'Deslocamento / Transfer�ncia' : 'Solicitar transfer�ncia' );
            $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}\")";
          }
        }

        if ($registro['aprovado'] == 3 &&
           (!is_array($lst_transferencia) && !isset($data_transferencia))
        ) {
          if ($registro['formando'] == 0) {
            $this->array_botao[]            = 'Formando';
            $this->array_botao_url_script[] = "if(confirm(\"Deseja marcar a matr�cula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=1\")";
          }
          else {
            $this->array_botao[]            = "Desmarcar como formando";
            $this->array_botao_url_script[] = "if(confirm(\"Deseja desmarcar a matr�cula como formando?\"))go(\"educar_matricula_formando_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&formando=0\")";
          }
        }
      }

      if($registro['aprovado'] == 4 &&
         $this->canCancelTransferenciaExterna($registro['cod_matricula'], $registro['ref_cod_aluno'])) {
        $this->array_botao[]            = 'Cancelar transfer�ncia (escola externa)';


        # TODO ver se c�digo, seta matricula como em andamento, ativa ultima matricula_turma for matricula, e desativa transferencia solicitacao
        $this->array_botao_url_script[] = "go(\"educar_transferencia_solicitacao_cad.php?ref_cod_matricula={$registro['cod_matricula']}&ref_cod_aluno={$registro['ref_cod_aluno']}&cancela=true&reabrir_matricula=true\")";
      }

      if ($registro['aprovado'] == App_Model_MatriculaSituacao::ABANDONO) {
        $this->array_botao[]            = "Desfazer abandono";
        $this->array_botao_url_script[] = "deleteAbandono({$registro['cod_matricula']})";
      }

      if ($registro['aprovado'] == App_Model_MatriculaSituacao::RECLASSIFICADO){
        $this->array_botao[]            = 'Desfazer reclassifica��o';
        $this->array_botao_url_script[] = "deleteReclassificacao({$registro['cod_matricula']})";
      }
    }

    $this->url_cancelar = 'educar_aluno_det.php?cod_aluno=' . $registro['ref_cod_aluno'];
    $this->largura      = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""                                  => "Detalhe da matr&iacute;cula"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    // js
    $scripts = array(
      '/modules/Portabilis/Assets/Javascripts/Utils.js',
      '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
      '/modules/Cadastro/Assets/Javascripts/MatriculaShow.js'
    );

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
  }

  // Verificar se pode cancelar matricula
  function permissao_cancelar(){
    @session_start();

    $this->pessoa_logada = $_SESSION['id_pessoa'];
    $acesso = new clsPermissoes();

    session_write_close();

    /**
     * @param Processo
     * @param Usu�rio logado
     * @param N�vel de acesso
     * @param Redirecionar p�gina
     * @param Super Usu�rio
     * @param Verifica usu�rio biblioteca
     */
    return $acesso->permissao_excluir(627, $this->pessoa_logada, 7, null, true);
  }

  function canCancelTransferenciaExterna($matriculaId, $alunoId) {
    $sql = "select 1 from pmieducar.matricula where ativo = 1 and cod_matricula > $matriculaId and ref_cod_aluno = $alunoId limit 1";

    $db = new clsBanco();
    $existeNovaMatricula = $db->CampoUnico($sql) == '1';

    return ! $existeNovaMatricula;
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do �  p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
