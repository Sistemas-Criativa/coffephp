<?php

/**
 * Registra a função de autoload
 */
spl_autoload_register(function ($classe) {
	/*Lista os separadores */
	$separador = array('\\', '/');

	/*troca os separadores*/
	$arquivo = str_replace($separador, DIRECTORY_SEPARATOR, $classe);
	
	/*verifica se o arquivo existe*/
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $arquivo . ".php")) {
		require_once $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $arquivo . '.php';
	} else {
		throw new Exception("Arquivo não encontrado '$classe'",1);  
	}
});
?>