<?php
/**
 * Controller padrão da aplicação
 *
 * @package Controller
 * @name Main
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1 18/07/2009
 */

class SiteController extends AppController
{

    var $uses = array("Usuario");

    function index(){

        $temp = $this->Usuario->find(array(
                                        'conditions' => array(
                                            'NOT' => array(
                                                'OR' => array(
                                                    'Usuario.id' => array('20', '21'),
                                                    'Tarefa.id' => array('22', '23'),
                                                ),
                                            ),
                                            'Tarefa9.id' => '29',
                                        ),
                                        
                                        'fields' => array(
                                            'Usuario.nome',
                                            'Tarefa.nome',
                                            'Idade.titulo',
                                        ),
                                        'order' => 'Usuario.id ASC',
                                        //'limit' => '10',
                                    ),
                                    "all"
            );

        $this->set('temp', $temp);
    }

    function listar($id="nao", $id2="naotb"){
        
    }

    function save(){
        pr( $this->Usuario->saveAll( $this->data ) );
    }

    function savetarefa(){
        pr( $this->Tarefa->saveAll( $this->data ) );

        $this->render("save");
    }


}
?>