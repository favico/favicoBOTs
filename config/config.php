<?php


//Variáveis de conexao
$conexao['servidor']	=	'localhost';
$conexao['usuario']		=	'favico';
$conexao['senha']		=	'';
$conexao['dbname']		=	'TelegramBot';


//Função de conexão
/*
	$conn = mysqli_connect($conexao['servidor'], $conexao['usuario'], $conexao['senha'])or die("Erro na conexão com o banco de dados.");
	mysqli_select_db($conn, $conexao['dbname'])or die("Não foi possivel selecionar o banco de dados.");
*/


class Conexao { 

	public static $instance; 
	
	private function __construct() { 
    // Nada aqui por enquanto.
	
	} 
	
	
	public static function getInstance() { 
		if (!isset(self::$instance)) { 
			self::$instance = new PDO('mysql:host=localhost;dbname=TelegramBot', 'favico', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 
			self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
			self::$instance->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING); 
		} 
		
		return self::$instance; 
		
	} 
}




