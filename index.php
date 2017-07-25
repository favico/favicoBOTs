<?php

require_once('funcoes.php');


define('BOT_TOKEN', '139084286:AAHhqOBXHQ9RdBY2QcOB-lKB0zl9JE-T6FE');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

$botToken = "139084286:AAHhqOBXHQ9RdBY2QcOB-lKB0zl9JE-T6FE";
$website = "https://api.telegram.org/bot".$botToken;

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);


$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

//$usuario = $update["message"]["chat"]["first_name"];


$user_id = $update["message"]["from"]["id"];

$apelido = consultaApelido($user_id);

if (!empty($apelido)) {
	
	$usuario = $apelido;
	
}else{
	$usuario = $update["message"]["from"]["first_name"];
}




$message = removerAcentos(strtolower(trim($message))); //limpa a string

  date_default_timezone_set('America/Sao_Paulo');
  setlocale( LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese' );


$aguardeArray = array("Um momento, por favor...", "Aguarde um momento...", "Um momento, vou pesquisar.");
$Aguarde = array_rand(array_flip($aguardeArray), 1);



switch($message) {
	
	case "ola":
	case "oi":
		sendMessage($chatId, "Olá $usuario, tudo bem? ");
		break;
	case "favico que dia e hoje?":
		sendMessage($chatId, "Hoje é ". saudar());
		break;		
		
		
		
		case "mega-sena":
		sendAction($chatId, "typing");
		$resultado = getResult('mega-sena', 'Mega-Sena');
		
		sendMessage($chatId, "nada sei sobre ". $message);
		break;

	case "quina":
		sendAction($chatId, "typing");
		sendMessage($chatId, "nada sei sobre ". $message);
		break;
		
	case "lotomania":
		sendAction($chatId, "typing");
		//sendMessage($chatId, getResult('lotomania', $message));
		sendMessage($chatId, "nada sei sobre ". $message);
		break;
		
	case "lotofacil":
		sendAction($chatId, "typing");
		//sendMessage($chatId, getResult('Lotofácil', $message));
		//sendMessage($chatId, "nada sei sobre ". $message);
		break;	
		
	//default: 
	//	sendMessage($chatId, "default");
	
}


//Verificação 2
switch(true) {

	case stristr($message, 'dia e hoje'):
		sendAction($chatId, "typing");
		sendMessage($chatId, "Hoje é ". saudar());
		break;
		
		
	case stristr($message, 'me chame de'):
	case stristr($message, 'chame-me de'):	
		sendAction($chatId, "typing");
			preg_match_all('/(?<=(de))(\s\w*)/',$message, $matches);
			InsereApelido($user_id, ucfirst(trim($matches[0][0])));
		sendMessage($chatId, "Certo, vou me lembrar disso.");
		break;	

	case stristr($message, 'horas sao'):
		sendAction($chatId, "typing");
		sendMessage($chatId, "Agora são exatamente ". date('H:i:s', time()));
		break;

	case stristr($message, 'ano atual'):
	case stristr($message, 'ano estamos'):
		sendAction($chatId, "typing");
		sendMessage($chatId, "Estamos no ano de ". date('Y', time()));
		break;
		
	case stristr($message, 'mes atual'):
	case stristr($message, 'mes estamos'):
		sendAction($chatId, "typing");
		sendMessage($chatId, "Estamos no mês de ". mesAtual());
		break;

	case stristr($message, 'noticia do dia'):
	case stristr($message, 'noticias do dia'):
	case stristr($message, 'noticias sobre'):
		$message = str_replace("noticias sobre","", $message);
	case stristr($message, 'noticia sobre'):
		$message = str_replace("noticia sobre","", $message);
		sendAction($chatId, "typing");
		sendMessage($chatId, $Aguarde);
		sendAction($chatId, "typing");
		sendMessage($chatId, pegaNoticia($message));
		break;


	case stristr($message, 'loteria'):
	//case stristr($message, ''):
		sendAction($chatId, "typing");
		sendMessage($chatId, "Qual resultado deseja?");
		
		 //enviarMensagem("sendMessage", array('chat_id' => $chatId, "text" => 'Legal, sempre estou apostando também.'. "\n".
		// 'Será que estou falando com um ganhador? Para começar, escolha qual loteria você deseja ver o resultado', 'reply_markup' => array(
  //      'keyboard' => array(array('Mega-Sena', 'Quina'),array('Lotofácil','Lotomania')),
  //      'one_time_keyboard' => true)));
        
		break;

						
		
	case stristr($message, 'definir:'):
	case stristr($message, 'definir :'):
	case stristr($message, 'defina:'):
	case stristr($message, 'defina :'):
		//obtem a palavra chave a pesquisar
		$pesquisa = substr($message, ($pos = strpos($message, ':')) !== false ? $pos + 1 : 0);
		
		sendAction($chatId, "typing");
			sendMessage($chatId, $Aguarde);
		sendAction($chatId, "typing");
			sendMessage($chatId, definirXML(trim($pesquisa)));
		break;
}




function sendMessage ($chatId, $message) {
	
	$url = $GLOBALS[website]."/sendMessage?chat_id=".$chatId."&text=".urlencode($message);
	file_get_contents($url);
	
}



function enviarMensagem($method, $parameters) {
  $options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode($parameters),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);
$context  = stream_context_create( $options );
file_get_contents(API_URL.$method, false, $context );
}



function sendAction ($chatId, $action) {
	
	$url = $GLOBALS[website]."/sendChatAction?chat_id=".$chatId."&action=".urlencode($action);
	file_get_contents($url);
	
} 
 

function removerAcentos($string){
  return preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $string ) );
}



?>