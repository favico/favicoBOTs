<?php


//include_once('modulos/loterias.php');
//include('modulos/simple_html_dom.php');


include_once('config/config.php');


function saudar(){
		  $ano    = date('Y');
		  $dia    = date('d')-0;
		  $dsemana= date('w');
		  $data   = date('n');
		  $mes[1] ='Janeiro';
		  $mes[2] ='Fevereiro';
		  $mes[3] ='Março';
		  $mes[4] ='Abril';
		  $mes[5] ='Maio';
		  $mes[6] ='Junho';
		  $mes[7] ='Julho';
		  $mes[8] ='Agosto';
		  $mes[9] ='Setembro';
		  $mes[10]='Outubro';
		  $mes[11]='Novembro';
		  $mes[12]='Dezembro';
		  $semana[0] = 'Domingo';
		  $semana[1] = 'Segunda-Feira';
		  $semana[2] = 'Terça-Feira';
		  $semana[3] = 'Quarta-Feira';
		  $semana[4] = 'Quinta-Feira';
		  $semana[5] = 'Sexta-Feira';
		  $semana[6] = 'Sádado';
		  return $semana[$dsemana].', '.$dia.' de '.$mes[$data].' de '.$ano;
  	}
  	

function mesAtual(){
		  $data   = date('n');
		  $mes[1] ='Janeiro';
		  $mes[2] ='Fevereiro';
		  $mes[3] ='Março';
		  $mes[4] ='Abril';
		  $mes[5] ='Maio';
		  $mes[6] ='Junho';
		  $mes[7] ='Julho';
		  $mes[8] ='Agosto';
		  $mes[9] ='Setembro';
		  $mes[10]='Outubro';
		  $mes[11]='Novembro';
		  $mes[12]='Dezembro';
		  
		  return $mes[$data];
  	}
  	  	
  	
  	
  	
  	
  	function definir($variavel) {
  		
  	$consulta = "https://pt.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles=". $variavel;
  	
  	$resposta = file_get_contents($consulta);
	$resultado = json_decode($resposta);
  		
  		//return $resposta;//$resposta["query"]["pages"]["extract"];
  		
	  foreach($parsed_json->query->pages as $k)
		{
	    	$resposta = $k->extract;
		}
  		
  		return $resposta;
  		
  	}
  	


	function definirXML($variavel){
		
		$consulta = "https://pt.wikipedia.org/w/api.php?action=opensearch&search=$variavel&format=xml&limit=1";
		$resposta = file_get_contents($consulta);
		
		// $xml_reader = new XMLReader();
		// $xml_reader->xml($resposta, "UTF-8");
		
		 $xml = simplexml_load_string($resposta);
		 
		 if((string)$xml->Section->Item->Description) {
			return (string)$xml->Section->Item->Description;
		 } else {
		 	
		 	return "Desculpe, não encontrei nada :(";
		 }
		 
		 
        // if((string)$xml->Section->Item->Description) {
        //     print_r(array((string)$xml->Section->Item->Text, 
        //     (string)$xml->Section->Item->Description, 
        //     (string)$xml->Section->Item->Url));
        // } else {
        //     echo "sorry";
        // } 
		
		
	}
	

	function noticiadoDia(){
		
		$url = "http://www.uol.com.br/";
		
		$html = file_get_contents($url);
		
			if (!empty($html)) {
				//$concurso = $html->find('span.loteria-numero',0)->plaintext;
				$noticia = $html->find('span.linha.font1.cor2-hover.cor-transition',0)->plaintext;
			
			return $noticia;
				
			}else {
				
			return "nada encontrado...";
				
			}
	}
		
	
	function PegaRss($url){
		
		//$url = "http://feeds.bbci.co.uk/portuguese/rss.xml";
		$conteudo = file_get_contents($url);

		$xml = simplexml_load_string($conteudo);
		
		$i = 1;
		foreach($xml->channel->item as $item){ 

			$title[$i] = (string)$item->title;
		    //$link[$i] = (string)$item->link;
		    //$description[$i] = (string)$item->description;
		    $i++;
			
	
        	//echo "Título: ".utf8_decode($item -> title)." ";

		}
		$resposta = array_rand(array_flip($title), 1);
		
		return $resposta;
		
		if (!empty($title)) {
			return array_rand(array_flip($title), 1);	
		
		}else{
			
			return "Desculpe, nada encontrei.";
			
		}
		
		
	}
	
	
		
		
	function pegaNoticia($assunto){
		
		
		switch(true){
			
			case  stristr($assunto, 'noticia'):
				return PegaRss('http://rss.home.uol.com.br/index.xml');
			break;

			case  stristr($assunto, 'vestibular'):
				return PegaRss('http://rss.uol.com.br/feed/vestibular.xml');
			break;
			
			case  stristr($assunto, 'esporte'):
				return PegaRss('http://esporte.uol.com.br/ultimas/index.xml');
			break;
			
			case  stristr($assunto, 'futebol'):
				return PegaRss('http://esporte.uol.com.br/futebol/ultimas/index.xml');
			break;
												
			
			
		}
		
		
	}
	
	
	
	
	function InsereApelido($user_id, $apelido){
		
		
		try { 
	
				$consulta = Conexao::getInstance()->prepare('INSERT INTO tbl_identificacao (user_id, apelido)VALUES(:user_id, :apelido)');
			
				$consulta ->bindParam(':user_id', $user_id, PDO::PARAM_INT);
				$consulta ->bindParam(':apelido', $apelido,  PDO::PARAM_STR);
				
				$consulta->execute();
				
				//echo $consulta->rowCount(); 
			
			}catch(PDOException $e){ 
	

			}
		
		
		
		
		
	}
	
	
	function consultaApelido($user_id){
		
		
		
	    $sql = "SELECT apelido
			  FROM tbl_identificacao
			  WHERE user_id = :user_id
			  ORDER BY data_inclusao DESC
			  LIMIT 1";

		$consulta = Conexao::getInstance()->prepare($sql);
		$consulta->bindParam(':user_id', $user_id, PDO::PARAM_STR);
		
		$consulta->execute();
		$resultado = $consulta->fetch(PDO::FETCH_ASSOC);
   

    	return $resultado['apelido'];
		
		
		
	}
		
		
		
