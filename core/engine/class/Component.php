<?php
/**
 * COMPONENT
 *
 * Classe que contém funções nativas a todos os Components.
 *
 * @package Component
 * @name Component
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1, 03/08/2009
 */
class Component
{
    /**
     *
     * @var array Parâmetros do sistema como $_POST, $_GET, Controller, Action
     */
    protected $params;
    /**
     *
     * @var array Dados enviados de um formulário
     */
    protected $data;
    /**
     *
     * @var array Models carregados no ambiente
     */
    protected $models;
    /**
     * Cada classe contém um versão. Esta versão é descrita nesta variável.
     *
     * @var string Versão desta classe
     */
    protected $version;


    function __construct($params = ""){

        /**
         * Inicialização de variáveis
         */
        /**
         * $this->params
         */
        $this->params = ( empty($params["params"]) ) ? array() : $params["params"];
        /**
         * $this->data
         */
        $this->data = ( empty($params["data"]) ) ? array() : $params["data"];
        /**
         * $this->models
         */
        $this->models = ( empty($params["models"]) ) ? array() : $params["models"];
        //$this->models = array();
    }

    /**
     * MÉTODOS DE INFORMAÇÕES SOBRE CLASSE
     */
    /**
     * Cada classe contém um versão. Este método retorna a versão desta
     * classe.
     *
     * @return string Versão desta classe
     */
    public function version(){
        return $version;
    }
}

?>