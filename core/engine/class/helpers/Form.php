<?php
/**
 * HELPER FORM
 *
 * Contém gerador de elementos FORM automáticos.
 *
 * @package Helpers
 * @name Form
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1, 19/07/2009
 */
/**
 * SESSIONS
 *
 * A estrutura das sessions para reter informações de FormHelper segue o padrão:
 *      $_SESSION["Sys"]["FormHelper"][$informacao] = $valor;
 *
 */
class FormHelper extends Helper
{
    /**
     * modelName
     *
     * Aqui é guardado o nome do Model principal do formulário.
     *
     * Alguns campos são especificados na seguinte sintaxe: "Model.campo". Se
     * "Model" for diferente do primeiro Model criado, $this->modelName é
     * alterado para o novo "Model".
     *
     * @var string Nome do Model principal
     */
    protected $modelName;
    /**
     *
     * @var array Contém as propriedades do respectivo Model objeto
     */
    protected $modelProperties;

    protected $destionationUrl;

    protected $formId = 0;

    protected $editable;

    function __construct($params=""){
        parent::__construct($params);
        
    }

    /**
     * create()
     *
     * Inicializa um formulário HTML e um novo objeto de formulário.
     *
     * Cada formulário é criado sob o domínio de um Model. Estas informações
     * ficam guardadas em $this->modelProperties, que retém propriedades
     * relacionadas a cada model.
     *
     * @param string $modelName Nome do Model responsável pelo tratamento dos
     *                          dados
     * @param array $options Opções de configuração e amostragem do formulário
     * @return string Código HTML contendo a abertura do formulário <form>
     */
    public function create($modelName, $options = ''){
        $conteudo = "";


        /**
         * formId
         *
         * Dá um ID único para cada formulário
         */
        if( empty($options["formId"]) ){
            $this->formId = $this->formId+1;//;sha1( rand(1, 99999) );
            unset($this->data);
        } else
            $this->formId = $options["formId"];

        //$_SESSION = array();

        if( isset($_SESSION["Sys"]["addToThisData"][$this->formId]) ){
            $this->data = $_SESSION["Sys"]["addToThisData"][$this->formId];
        }
        //pr($_SESSION);
        global $globalVars;
        /**
         * Login
         *
         * Se há um login estipulado, sobrescreve <form action> atual
         */
        /**
         * @todo - Usuário logado precisa que o action do formulário seja
         * realmente sobrescrito?
         */
        if( !empty($globalVars["defaultLoginPage"]) AND $options == "login" ){
            $options = array();
            if( !empty($globalVars["defaultLoginPage"]["controller"]) ){
                $options["controller"] = $globalVars["defaultLoginPage"]["controller"];
            }
                //echo "Oij".$globalVars["defaultLoginPage"]["action"];
            if( !empty($globalVars["defaultLoginPage"]["action"]) )
                $options["action"] = $globalVars["defaultLoginPage"]["action"];

        }

        /**
         * Ajusta o nome do model no objeto instanciado
         */
        if( !empty($modelName) ){
            $this->modelName = $modelName;
        }

        /**
         * DescribedTables
         *
         * Carrega as tabelas descritas deste respectivo Model deste $form
         */
        global $describedTables;
        $this->modelProperties["describedTables"] = $describedTables;

        /**
         * Controller
         *
         * Verifica se o usuário especificou um controller que deverá
         * ser usado para salvar as informações do formulário
         */
        $controller = (empty($options["controller"])) ? $this->modelName : $options["controller"];
        /**
         * Action
         *
         * O action padrão para salvar um formulário é 'save'
         */
        $action = (empty($options["action"])) ? 'save' : $options["action"];

        /**
         * ABRE FORMULÁRIO
         */
        $this->destionationUrl = WEBROOT.''.$controller.'/'.$action."/";
        $conteudo.= '<form method="post" action="'.$this->destionationUrl.'" class="formHelper">';

        /**
         * INPUTS HIDDEN
         *
         * Mostra todos os inputs type=hidden se existir
         */
        if( !empty($options["hidden"]) ){
            foreach( $options["hidden"] as $chave=>$valor){
                if( is_string($valor) ){
                    $conteudo.= $valor;
                }
            }
        }


        /**
         * formId
         *
         * Dá um ID único para cada formulário
         */
        if( isset($options["edit"]) AND $options["edit"] == false ){
            $this->editable = false;
        } else
            $this->editable = true;

        /**
         * MODEL PRINCIPAL
         */
        if( !empty($modelName) ){
            $conteudo.= '<input type="hidden" name="modelName" value="'.$modelName.'" />';
        }
        /**
         * Indica que é formulário de um FormHelper
         */
        $conteudo.= '<input type="hidden" name="sender" value="formHelper" />';

        /**
         * Indica que é formulário de um FormHelper
         */
        $conteudo.= '<input type="hidden" name="formId" value="'.$this->formId.'" />';

        /**
         * Qual o endereço do formulário
         */
            $formUrl = translateUrl( array(
                "controller" => $this->params["controller"],
                "action" => $this->params["action"],
                implode("/", $this->params["args"])
            ));
        $conteudo.= '<input type="hidden" name="formUrl" value="'.$formUrl.'/" />';

        return $conteudo;
    }

    /**
     * @todo - campos de models relacionados hasMany devem ter uma
     * nomenclatura do tipo [model][0][campo1], [model][1][campo1], ectes
     */

    /**
     * Cria inputs de formulários HTML automaticamente, necessitando somente
     * indicar o nome do campo relacionado na base de dados.
     *
     * @param string $fieldName Nome do campo no banco de dados.
     * @param array $options Opções de configurações e amostragem.
     *      "label" string :
     *      "value" string : Valor padrão do campo
     *      "select" array :
     *      ""
     * @return string Código HTML para o form input pedido.
     */
    public function input($fieldName, $options = ''){
        /**
         * ID
         *
         * Se um id foi especificado, vai no DB e busca o registro dele
         */

        global $describedTables;
        if( $fieldName == "id"  AND isset($describedTables[$this->modelName]) ){

            if( is_int($options) OR is_string($options) )
                $fieldValue = $options;
            else if( !empty($options["value"]) )
                $fieldValue = $options["value"];

            /**
             * Se é editavel
             */
            if($this->editable == true){

                /**
                 * Carrega as informações sobre o determinado ID
                 */
                $this->data = $this->models[$this->modelName]->find($fieldValue, "first");

                $_SESSION["Sys"]["addToThisData"][$this->formId][$this->modelName]["id"] = $fieldValue;
                $_SESSION["Sys"]["options"]["addToThisData"][$this->formId]["destLocation"] = $this->destionationUrl;
                if( !empty($options["show"]) AND is_array($options) AND $options["show"] == true ){
                } else {
                    unset($options);
                    return false;
                }
            }
            
        }

        $argFieldName = $fieldName;
        
        $conteudo ='';

        $conteudo.= '<div class="input input_'.str_replace(".","_", $fieldName).'">';

        /**
         * ANÁLISE DO ARGUMENTO $OPTIONS
         *
         * Após análise, cada item de $options é deletado, os que sobrarem são
         * inseridos na tag HTML como propriedade
         */
        /**
         * Analisa $options["label"]
         *
         * Se Label não foi especificado
         */
        if( empty($options["label"]) ){
            $label = $argFieldName;
        } else {
            $label = $options["label"];
            unset($options["label"]);
        }

        /**
         * ["select"]
         * 
         * Tipos de campos
         */
        if( !empty($options["select"]) ){
            $inputType = "select";
            unset($options["select"]);
        }

        /**
         * ["value"]
         *
         * Padrão padrão do input
         */
        $fieldValue = "";
        if( !empty($options["value"]) ){
            $fieldValue = 'value="'.$options["value"].'" ';
            unset($options["value"]);
        }

        /**
         * ["before"]
         */
        if( !empty($options["before"]) ){
            $before = $options["before"];
            unset($options["before"]);
        }

        /**
         * ["after"]
         */
        if( !empty($options["after"]) ){
            $after = $options["after"];
            unset($options["after"]);
        }

        /**
         * ["between"]
         *
         * Texto entre label e input
         */
        if( !empty($options["between"]) ){
            $between = $options["between"];
            unset($options["between"]);
        }
        // fim análise $options
        


        /**
         * NOMES DO INPUTS
         *
         * Gera nomes para os inputs
         */
        /**
         * Verifica se o Model é padrão ou foi especificado algum outro
         */
        $dotPos = strpos($fieldName, "." );
        if( $dotPos === false ){
            /**
             * É o model padrão
             */
            $modelName = $this->modelName;
        }
        /**
         * Outro Model foi especificado
         */
        else {
            $modelName = substr( $fieldName, 0, $dotPos );
            $fieldName = substr( $fieldName, $dotPos+1, 100 );
        }

        /**
         * VALOR AUTOMÁTICO
         */
        if( empty($fieldValue) ) {

            /**
             * @todo - mostrar valores para outros campos fora input=text,
             * como select, radio, etc.
             */

            if( !empty($this->data[$modelName][$fieldName]) ){
                $fieldValue = 'value="'. $this->data[$modelName][$fieldName]. '"';
            } else {
                $fieldValue = 'value=""';
            }
        }

        /**
         * PROPRIEDADES-PADRÃO
         */
        $standardAtrib = 'id="input-'.$fieldName.'" ';
        $standardAtribValue = $fieldValue;

        /**
         * @todo - Escrever value digitado anteriormente quando envia formulário
         * e retorna para ele de volta.
         */

        /**
         * Define o nome do input
         */
        if( !empty($modelName) ){
            $inputName = "data[".$modelName."][".$fieldName."]";
        } else {
            $inputName = "data[".$fieldName."]";
        }

        /**
         * ANÁLISE DE TIPOS DE CAMPOS
         */
        /**
         * Mostra inputs de acordo com o especificado
         */
        /**
         * Verifica tabela
         *
         * Se não há tipos especificados na configuração do $form, verifica qual
         * o tipo de campo na tabela e mostra o campo <input> de acordo
         */
        if( empty($inputType) AND !empty($this->modelProperties["describedTables"][$modelName]) ){
            /**
             * Verifica global $describedTables que já está carregado e salvo
             * em $this->modelProperties[$modelName]["describedTables"]
             */
             $physicalType = $this->modelProperties["describedTables"][$modelName][$fieldName]["Type"];
             /**
              * Esta variável com nome gigante tem a posição do primeiro
              * parêntesis no nome físico deste campo na tabela do banco de
              * dados. Isto serve para tomar o nome do tipo de dados somente.
              *
              * Ex.:
              *     - varchar() -> pega somente "varchar"
              *     - tinyint() -> pega somente "tinyint"
              */
             $physicalTypeNameParenthesisPos = strpos( $physicalType, "(" );
             if( $physicalTypeNameParenthesisPos > 0 ){
                 $physicalType = substr( $physicalType, 0, $physicalTypeNameParenthesisPos );
             }

             /**
              * Segundo o tipo de dado físico de cada campo na tabela,
              * instancia um tipo para um campo do formulário.
              */
             switch($physicalType){
                 /**
                  * Textos pequenos como varchar
                  */
                 case "varchar"     : $inputType = "text"; break;
                 /**
                  * Textos grandes como text, blob
                  */
                 case "text"        : $inputType = "textarea"; break;
                 case "blob"        : $inputType = "textarea"; break;
                 /**
                  * Números como int
                  */
                 case "int"         : $inputType = "text"; break;
                 /**
                  * Boolean, tinyint
                  */
                 case "bool"        : $inputType = "checkbox"; break;
                 case "tinyint"     : $inputType = "checkbox"; break;
                 /**
                  * Datas e time
                  */
                 case "datetime"    : $inputType = "text"; break;
                 case "date"        : $inputType = "text"; break;
                 case "time"        : $inputType = "text"; break;
                 default            : $inputType = "text"; break;
             }
        } else if( empty($inputType) ) {
            $inputType = "text";
        }
        
        /**
         * Se BEFORE está especificado
         */
        if( !empty($before) ){
            $conteudo.= '<span class="input_before">';
            $conteudo.= $before;
            $conteudo.= '</span>';
        }

        /**
         * Ajusta o label se não for do tipo checkbox, etc
         *
         * Somente inputs com labels podem ter o atributo between
         */
        if( !in_array($inputType, array("checkbox")) ){
            $conteudo.= '<label for="input-'.$fieldName.'">'.$label.'</label>';
            
            /**
             * Se BETWEEN está especificado
             */
            if( !empty($between) ){
                $conteudo.= '<span class="input_between">';
                $conteudo.= $between;
                $conteudo.= '</span>';
            }
        }
        /**
         * INPUTS
         *
         * TYPE="TEXT"
         */
        if( $inputType == "text" ){

            /**
             * Verifica campo de password
             */
            if( in_array($fieldName, Config::read("modelPasswordFields")) )
                $inputType = "password";

            /**
             * Gera conteúdo para o formulário
             */
            $conteudo.= '<div class="input_field input_text">';
            $conteudo.= '<input type="'.$inputType.'" name="'.$inputName.'" '.$standardAtrib.' '.$standardAtribValue.' />';
        }
        /**
         * CHECKBOX
         */
        else if( $inputType == "checkbox" ){
            $conteudo.= '<div class="input_field input_checkbox">';
            $conteudo.= '<input type="checkbox" name="'.$inputName.'" '.$standardAtrib.' />';
            $conteudo.= '<label for="input-'.$fieldName.'">'.$label.'</label>';
        }
        /**
         * <TEXTAREA>
         *
         * Quando o tipo de dados do DB for text, blog, etc, o campo será
         * textarea.
         */
        else if( $inputType == "textarea"){
            $conteudo.= '<div class="input_field input_textarea">';
            $conteudo.= '<textarea name="'.$inputName.'" '.$standardAtrib.' />';
            $conteudo.= $standardAtribValue;
            $conteudo.= '</textarea>';
        }
        /**
         * <SELECT>
         *
         * Pega as informações de $options["select"] e cria
         * um <select> com vários <option></option>
         */
        else if( $inputType == "select" ){
            $select = $options["select"];
            /**
             * <option> selecionado
             */
            $selectSelected = $select["selected"];
            /**
             * Opções a serem mostradas
             */
            $selectOptions = $select["options"];
            $conteudo.= '<div class="input_field input_select">';
            $conteudo.= '<select name="'.$inputName.'" '.$standardAtrib.'>';
            /**
             * Loop pelo select criando <options>
             */
            foreach($selectOptions as $chave=>$valor){
                /**
                 * Verifica se o <option> atual deve ser selecionado por
                 * padrão
                 */
                if( !empty($selectSelected) AND $selectSelected == $chave ){
                    $selectThis = 'selected="true"';
                } else {
                    $selectThis = false;
                }
                $conteudo.= '<option '.$selectThis.' value="'.$chave.'">'.$valor.'</option>';
            }

            $conteudo.= '</select>';
        }
        /*
         * Não é nenhum tipo de campo pré-definido.
         *
         * Imprime campo com mensagem de erro para que o desenvolvedor veja
         * a falha.
         */
        else {
            $conteudo.= '<div class="input_field input_text">';
            $conteudo.= '<input type="text" name="'.$inputName.'" value="ERRO NO TIPO DE CAMPO" '.$standardAtrib.'>';
        }
        $conteudo.= '</div>'; // fecha div do .input_field

        /**
         * VALIDAÇÃO
         *
         * Mostra mensagens de erro de validação
         */
         //pr($_SESSION);
        if( !empty($_SESSION["Sys"]["FormHelper"]["notValidated"][$modelName][$fieldName] ) ){
            $conteudo.= '<div class="input_validation_error">';
            $conteudo.= $_SESSION["Sys"]["FormHelper"]["notValidated"][$modelName][$fieldName]["message"];
            $conteudo.= '</div>';
        }

        /**
         * Se AFTER está especificado
         */
        if( !empty($after) ){
            $conteudo.= '<span class="input_after">';
            $conteudo.= $after;
            $conteudo.= '</span>';
        }

        $conteudo.= '</div>'; // fecha div do .input

        return $conteudo;
    }

    /**
     * Termina um formulário, inserindo o campo Submit e fechando o bloco de
     * código HTML <form></form>.
     *
     * @param string $submitValue Valor a ser escrito no botão de Submit
     * @param array $options Opções de amostragem e configurações
     * @return string Código HTML para finalizar bloco de código <form></form>
     */
    public function end($submitValue = "Enviar", $options = ""){
        $conteudo = '';
        $conteudo.= '<input type="submit" name="formSubmit" value="'.$submitValue.'" class="submit" />';
        $conteudo.= '</form>';
        return $conteudo;
    }

    public function statusMessage(){
        /**
         * Se de fato existe alguma mensagem de status
         */
        if( !empty( $_SESSION["Sys"]["FormHelper"]["statusMessage"] ) ){
            $sM = $_SESSION["Sys"]["FormHelper"]["statusMessage"];
            $conteudo = '';
            $conteudo.= '<div class="form_status_message">';

            if( $sM["class"] == "incorrect"){
                $conteudo.= '<div class="error incorrect">';
                $conteudo.= $sM["message"];
            } else if( $sM["class"] == "denied" OR $sM["class"] == "denied2" ){
                $conteudo.= '<div class="error denied">';
                $conteudo.= $sM["message"];
            }

            $conteudo.= '</div>';
            $conteudo.= '</div>';

            return $conteudo;
        }
        return false;
    }

}

?>