<?php

/**
 * ServidorAfastarWebTest class.
 *
 * Esse teste precisa ser executado com o banco de dados distribuído na
 * versão 1.0.0.
 *
 * @author  Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @since   1.0.1
 * @version $Id$
 */

require_once realpath(dirname(__FILE__) . '/../') . '/FunctionalBaseTest.class.php';

class ServidorAfastarWebTest extends FunctionalBaseTest {

  private
    $slPessoaNome      = 'Selenese Test User',
    $slPessoaMatricula = 'selen_tuser',
    $slPessoaID        = NULL;



  protected function setUp() {
    parent::setUp();

    $db = new clsBanco();

    // Cria uma nova pessoa e guarda o ID gerado
    $db->Consulta(sprintf("INSERT INTO cadastro.pessoa (nome, data_cad,tipo,situacao,origem_gravacao,  idsis_cad, operacao , idpes_cad) VALUES ('%s', NOW(), 'F', 'P', 'U', 17, 'I' , '1')", $this->slPessoaNome));
    $this->slPessoaID = $id = $db->InsertId('cadastro.seq_pessoa');


    // Cria pessoa física
    $db->Consulta(sprintf("INSERT INTO cadastro.fisica (idpes, origem_gravacao, idsis_cad, data_cad, operacao, idpes_cad , sexo) VALUES ( '%d', 'M', 17, NOW(), 'I', '1' , 'M')", $id));

    // Cria novo funcionário no sistema
    $db->Consulta(
      sprintf(
        "INSERT INTO portal.funcionario
          (ref_cod_pessoa_fj, matricula, senha, ativo, ramal, ref_cod_funcionario_vinculo, tempo_expira_senha, tempo_expira_conta, data_troca_senha, data_reativa_conta, ref_ref_cod_pessoa_fj, proibido, ref_cod_setor_new, matricula_permanente)
        VALUES
          ('%d', '%s', '25d55ad283aa400af464c76d713c07ad', '1', '', '4', '30', '365', NOW(), NOW(), '28', '0', '1', '1')", $id, $this->slPessoaMatricula)
      );


    // Cria um novo servidor, com a função de professor
    $sql = sprintf("INSERT INTO pmieducar.servidor (cod_servidor, ref_idesco, carga_horaria, data_cadastro, ativo, ref_cod_instituicao ) VALUES( '%d', '14', '40', NOW(), '1', '2' )", $id);
    $db->Consulta($sql);

    $sql = sprintf("INSERT INTO pmieducar.servidor_funcao (ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_funcao ) VALUES( '2', '%d', '2')", $id);
    $db->Consulta($sql);


    // Atribue disciplinas ao servidor e o curso em que ministra
    $sql = sprintf("INSERT INTO pmieducar.servidor_disciplina (ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor) VALUES('6', '2', '%d')", $id);
    $db->Consulta($sql);
    $sql = sprintf("INSERT INTO pmieducar.servidor_disciplina (ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor) VALUES('3', '2', '%d')", $id);
    $db->Consulta($sql);
    $sql = sprintf("INSERT INTO pmieducar.servidor_disciplina (ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor) VALUES('4', '2', '%d')", $id);
    $db->Consulta($sql);

    $sql = sprintf("INSERT INTO pmieducar.servidor_curso_ministra (ref_cod_curso, ref_ref_cod_instituicao, ref_cod_servidor) VALUES('1', '2', '%d')", $id);
    $db->Consulta($sql);


    // Aloca tempo de trabalho para o servidor
    $sql = sprintf("INSERT INTO pmieducar.servidor_alocacao (ref_ref_cod_instituicao, ref_usuario_cad, ref_cod_escola, ref_cod_servidor, carga_horaria, periodo, data_cadastro, ativo ) VALUES( '2', '28', '1', '%d', '10:00', '1', NOW(), '1')", $id);
    $db->Consulta($sql);
    $sql = sprintf("INSERT INTO pmieducar.servidor_alocacao (ref_ref_cod_instituicao, ref_usuario_cad, ref_cod_escola, ref_cod_servidor, carga_horaria, periodo, data_cadastro, ativo ) VALUES( '2', '28', '1', '%d', '06:00', '2', NOW(), '1')", $id);
    $db->Consulta($sql);
    $sql = sprintf("INSERT INTO pmieducar.servidor_alocacao (ref_ref_cod_instituicao, ref_usuario_cad, ref_cod_escola, ref_cod_servidor, carga_horaria, periodo, data_cadastro, ativo ) VALUES( '2', '28', '2', '%d', '14:00', '1', NOW(), '1')", $id);
    $db->Consulta($sql);
    $sql = sprintf("INSERT INTO pmieducar.servidor_alocacao (ref_ref_cod_instituicao, ref_usuario_cad, ref_cod_escola, ref_cod_servidor, carga_horaria, periodo, data_cadastro, ativo ) VALUES( '2', '28', '2', '%d', '05:00', '2', NOW(), '1')", $id);
    $db->Consulta($sql);

    // Aloca horas aula ao servidor
    $sql = sprintf("INSERT INTO pmieducar.quadro_horario_horarios ( ref_cod_quadro_horario, ref_cod_serie, ref_cod_escola, ref_cod_disciplina, sequencial, ref_cod_instituicao_servidor, ref_servidor, hora_inicial, hora_final, data_cadastro, ativo, dia_semana ) VALUES( '2', '2', '1', '6', '3', '2', '%d', '09:00', '10:00', NOW(), '1', '3')", $id);
    $db->Consulta($sql);

    $sql = sprintf("INSERT INTO pmieducar.quadro_horario_horarios ( ref_cod_quadro_horario, ref_cod_serie, ref_cod_escola, ref_cod_disciplina, sequencial, ref_cod_instituicao_servidor, ref_servidor, hora_inicial, hora_final, data_cadastro, ativo, dia_semana ) VALUES( '2', '2', '1', '3', '4', '2', '%d', '08:00', '09:00', NOW(), '1', '5' )", $id);
    $db->Consulta($sql);
  }



  protected function tearDown() {
    $db = new clsBanco();

    // ID da pessoa/servidor
    $id = $this->slPessoaID;

    // Array com instruções SQL para limpar os registros
    $sqls = array();

    $sqls[] = sprintf("DELETE FROM pmieducar.quadro_horario_horarios WHERE ref_servidor = '%d'", $id);
    $sqls[] = sprintf("DELETE FROM pmieducar.servidor_alocacao WHERE ref_cod_servidor = '%d'", $id);
    $sqls[] = sprintf("DELETE FROM pmieducar.servidor_curso_ministra WHERE ref_cod_servidor = '%d'", $id);
    $sqls[] = sprintf("DELETE FROM pmieducar.servidor_disciplina WHERE ref_cod_servidor = '%d'", $id);
    $sqls[] = sprintf("DELETE FROM pmieducar.servidor_funcao WHERE ref_cod_servidor = '%d'", $id);
    $sqls[] = sprintf("DELETE FROM pmieducar.servidor_afastamento WHERE ref_cod_servidor = '%d'", $id);  // Criado na interface, apenas para não ter problemas com alguma restrição referencial
    $sqls[] = sprintf("DELETE FROM pmieducar.servidor WHERE cod_servidor = '%d'", $id);
    $sqls[] = sprintf("DELETE FROM portal.menu_funcionario WHERE ref_ref_cod_pessoa_fj = '%d'", $id);    // Não é criado mas apenas para não ser surpreendido por alguma restrição referencial
    $sqls[] = sprintf("DELETE FROM portal.funcionario WHERE ref_cod_pessoa_fj = '%d'", $id);
    $sqls[] = sprintf("DELETE FROM cadastro.fisica WHERE idpes = '%d'", $id);
    $sqls[] = sprintf("DELETE FROM cadastro.pessoa WHERE idpes = '%d'", $id);

    foreach ($sqls as $sql) {
      $db->Consulta($sql);
    }

  }



  /**
   * Testa a ação afastar servidor.
   *
   * Verifica pelo nome de matrícula que aparece na página de detalhes logo após
   * a requisição enviada ao servidor.
   */
  public function testServidorAfastar() {
    $this->doLogin();

    $this->open('/intranet/educar_servidor_lst.php');
    $this->clickAndWait('link=' . $this->slPessoaNome);
    $this->clickAndWait("//input[@value='Afastar Servidor']");

    $this->select('ref_cod_motivo_afastamento', 'label=Capacitação');

    // Cria data daqui a 10 dias
    $data = date('d/m/Y', (time() + (60 * 60 * 24 * 10)));
    $this->type('data_saida', $data);

    $this->clickAndWait('btn_enviar');
    $this->assertTrue($this->isTextPresent($this->slPessoaMatricula));

    $this->doLogout();
  }

}