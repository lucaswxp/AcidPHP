<h1>Listagem de usuários</h1>

<table>
<?php

//pr($usuarios);
if( !empty($usuarios) ){

    foreach( $usuarios as $usuario ){
        ?>
        <tr>
            <td>
                <strong><?php echo $usuario["Usuario"]["id"]; ?></strong>
            </td>
            <td>
                <?php echo $usuario["Usuario"]["nome"]; ?>
            </td>
            <td>
                Tarefas: <?php if(!empty($usuario["Tarefa"])) echo count($usuario["Tarefa"]); else echo '0'; ?>
            </td>
            <td>
                <?php $html->link( "Deletar", array("controller" => "site", "action" => "deletar", $usuario["Usuario"]["id"]) ); ?>
                -
                <?php $html->link( "Editar", array("controller" => "site", "action" => "editar", $usuario["Usuario"]["id"]) ); ?>
            </td>
        </tr>


        <?

    }


}



?>

</table>
