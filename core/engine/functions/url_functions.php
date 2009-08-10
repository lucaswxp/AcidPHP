<?php
/**
 * Funções de tratamento de URLs
 *
 * @package Core
 * @category Funções
 * @name URL Functions
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1
 */

/**
 * translateUrl() traduz uma URL
 *
 * @global Object $engine Objeto inicializador do sistema
 * @param mixed $mixed Configuração de Url para criação de string final
 * @return string Retorna um endereço Url válido
 */
function translateUrl($mixed){

    global $engine;
    /**
     * $mixed é array
     */
    if( is_array($mixed) ){
        $controller = ( empty($mixed["controller"]) ) ? $engine->callController : $mixed["controller"];
        $action = ( empty($mixed["action"]) ) ? "index" : $mixed["action"];
        $args = ( empty($mixed[0]) ) ? "" : $mixed[0];

        if( isset($args[0]) AND $args[0] != "/" ){
            $args = "/".$args;
        }

        $url = $engine->webroot.$controller."/".$action.$args;
    }
    /**
     * $mixed é string
     */
    else if( is_string($mixed) ){

        if( !in_array(  StrTreament::getNameSubStr($mixed, ":"),
                        array("http","ftp","ssh","git","https") )
        ){

            $url = explode("/", $mixed);

            $args = array();
            $i = 0;
            foreach( $url as $chave=>$valor ){
                if( empty($valor) ){
                    unset($url[$chave]);
                } else {
                    if( $i == 0 ){
                        $controller = $valor;
                    } else if( $i == 1 ){
                        $action = $valor;
                    } else {
                        $args[] = $valor;
                    }
                    $i++;
                }

            }
            $url = $engine->webroot.$controller."/".$action."/".implode("/", $args);
        } else {
            $url = $mixed;
        }
    }
    return $url;

    return false;
}

/**
 * Redireciona o cliente para o endereço $url indicado.
 *
 * Se $url é uma array, trata-a para um endereço válido
 *
 * @param string $url Endereço Url válido a ser aberto
 * @return boolean Retorna falso se não conseguir redirecionar
 */
function redirect($url=""){
    /**
     * Segurança: se $url for array
     */
    if( is_array($url) ){
        $url = translateUrl($url);
    }

    /**
     * Redireciona
     */
    if( !empty($url) ){
        header("Location: ". $url);
        return false;
    } else {
        return false;
    }
}

?>