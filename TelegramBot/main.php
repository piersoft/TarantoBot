$reply = "Funzione non ancora implementata";

$content = array('chat_id' => $chat_id, 'text' => $reply);
$telegram->sendMessage($content);<?php
/**
 * Telegram Bot example.
 * @author Gabriele Grillo <gabry.grillo@alice.it>
  * designed starting from https://github.com/Eleirbag89/TelegramBotPHP
  * rebulding by @Piersoft on project Emergenzeprato by Matteo Tempestini.
 */

include(dirname(__FILE__).'/../settings.php');
include('settings_t.php');
include(dirname(dirname(__FILE__)).'/getting.php');
include("Telegram.php");
include("broadcast.php");
include("QueryLocation.php");


class getshorturl {

  public function shorturl($longUrl){
    // Get API key from : http://code.google.com/apis/console/
    $apiKey = API;

    $postData = array('longUrl' => $longUrl, 'key' => $apiKey);
    $jsonData = json_encode($postData);

    $curlObj = curl_init();

    curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
    curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlObj, CURLOPT_HEADER, 0);
    curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
    curl_setopt($curlObj, CURLOPT_POST, 1);
    curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

    $response = curl_exec($curlObj);

    // Change the response json string to object
    $json = json_decode($response);

    curl_close($curlObj);
  //  $reply="Puoi visualizzarlo su :\n".$json->id;
  //  $shortLink = get_object_vars($json);
   return $json->id;

  //  return $shortLink['id'];

}


}



class main{

const MAX_LENGTH = 4096;

 function start($telegram,$update)
	{

		date_default_timezone_set('Europe/Rome');
		$today = date("Y-m-d H:i:s");
  //  $api = new GoogleURL('AIzaSyBUMmMuuo4WkImc3IHrch3yMLHu5DeFtPA');

		// Instances the class
		$data=new getdata();
  //  $geturl=new getshorturl();
		$db = new PDO(DB_NAME);
$log="";
		/* If you need to manually take some parameters
		*  $result = $telegram->getData();
		*  $text = $result["message"] ["text"];
		*  $chat_id = $result["message"] ["chat"]["id"];
		*/

		$text = $update["message"] ["text"];
		$chat_id = $update["message"] ["chat"]["id"];
		$user_id=$update["message"]["from"]["id"];
		$location=$update["message"]["location"];
		$reply_to_msg=$update["message"]["reply_to_message"];

		$this->shell($telegram, $db,$data,$text,$chat_id,$user_id,$location,$reply_to_msg);
$db = NULL;
	}

	//gestisce l'interfaccia utente
	 function shell($telegram,$db,$data,$text,$chat_id,$user_id,$location,$reply_to_msg)
	{
		date_default_timezone_set('Europe/Rome');
		$today = date("Y-m-d H:i:s");

		if ($text == "/start") {
				$log=$today. ";new chat started;" .$chat_id. "\n";
			}
			//richiedi previsioni meteo di oggi
			elseif ($text == "/meteo oggi" || $text == "meteo oggi") {
        $reply = "Previsioni Meteo per oggi:\n" .$data->get_forecast("tarantoggi");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        $log=$today. ";previsioni Lecce sent;" .$chat_id. "\n";
				}
			//richiede previsioni meteo di domani
			elseif ($text == "/previsioni" || $text == "previsioni") {

        $reply = "Previsioni Meteo :\n" .$data->get_comunicatimeteo("taranto");
    //    $reply = "Previsioni Meteo :\n" .$data->get_forecast("taranto");

    $chunks = str_split($reply, self::MAX_LENGTH);
    foreach($chunks as $chunk) {
     // $forcehide=$telegram->buildForceReply(true);
        //chiedo cosa sta accadendo nel luogo
        $content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
        $telegram->sendMessage($content);

    }

    //    $content = array('chat_id' => $chat_id, 'text' => $reply);
    //    $telegram->sendMessage($content);
        $log=$today. ";previsioni sent;" .$chat_id. "\n";
			}	//richiede rischi di oggi a Lecce
  			elseif ($text == "/messaggi allerta" || $text == "messaggi allerta") {
          $reply = "Messaggi di allerta Protezione Civile Puglia:\n\n" .$data->get_allertameteo("allerta");
          $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
          $telegram->sendMessage($content);

  				$log=$today. ";rischi sent;" .$chat_id. "\n";

  			}//richiede rischi di oggi a Lecce
    			elseif ($text == "/bollettini criticità" || $text == "bollettini criticità") {
            $reply = "Bollettini criticità Protezione Civile Puglia:\n\n" .$data->get_bollettini("allerta");
            $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
            $telegram->sendMessage($content);

    				$log=$today. ";rischi sent;" .$chat_id. "\n";

    			}
  			//richiede la temperatura
  			elseif ($text == "/temperatura" || $text == "temperatura") {

          $reply = "Temperatura misurata in zona Taranto centro : " .$data->get_temperature();
          $content = array('chat_id' => $chat_id, 'text' => $reply);
          $telegram->sendMessage($content);
          $log=$today. ";temperatura Taranto sent;" .$chat_id. "\n";

  			}

			//crediti
			elseif ($text == "/informazioni" || $text == "informazioni") {
				 $reply = ("Taranto Bot e' un servizio sperimentale e dimostrativo per segnalazioni meteo Taranto, messaggi e bollettini Protezione Civile Puglia.
			 Applicazione sviluppata da Piero Paolicelli @piersoft (ottbre 2015). Licenza MIT codice in riuso da : http://iltempe.github.io/Emergenzeprato/
          \nFonti:
          Previsioni Meteo,Messaggi e Bollettini di allerta    -> Protezione Civile Puglia (feeds)
          Temperatura e Meteo di oggi -> Api pubbliche di www.wunderground.com
          ");

				 $content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
				 $telegram->sendMessage($content);
				 $log=$today. ";crediti sent;" .$chat_id. "\n";
			}
			//----- gestione segnalazioni georiferite : togliere per non gestire le segnalazioni georiferite -----
			elseif($location!=null)
			{
      //  $reply = "Funzione non ancora implementata";

      //  $content = array('chat_id' => $chat_id, 'text' => $reply);
      //  $telegram->sendMessage($content);
          $this->location_manager($db,$telegram,$user_id,$chat_id,$location);
          exit;

			}

			elseif($reply_to_msg!=null)
			{
				//inserisce la segnalazione nel DB delle segnalazioni georiferite

        $response=$telegram->getData();



    $type=$response["message"]["video"]["file_id"];
    $text =$response["message"] ["text"];
    $risposta="";
    $file_name="";
    $file_path="";
    $file_name="";

    if ($type !=NULL) {
    $file_id=$type;
    $text="video allegato";
    $risposta="ID dell'allegato:".$file_id;
    }

    $file_id=$response["message"]["photo"][0]["file_id"];

    if ($file_id !=NULL) {

    $telegramtk=TELEGRAM_BOT; // inserire il token
    $rawData = file_get_contents("https://api.telegram.org/bot".$telegramtk."/getFile?file_id=".$file_id);
    $obj=json_decode($rawData, true);
    $file_path=$obj["result"]["file_path"];
    $caption=$response["message"]["caption"];
    if ($caption != NULL) $text=$caption;
    $risposta="ID dell'allegato: ".$file_id;

    }
    $typed=$response["message"]["document"]["file_id"];

    if ($typed !=NULL){
    $file_id=$typed;
    $file_name=$response["message"]["document"]["file_name"];
    $text="documento: ".$file_name." allegato";
    $risposta="ID dell'allegato:".$file_id;

    }

    $typev=$response["message"]["voice"]["file_id"];
    if ($typev !=NULL){
    $file_id=$typev;
    $text="audio allegato";
    $risposta="ID dell'allegato:".$file_id;

    }
    $csv_path=dirname(__FILE__).'/./db/map_data.csv';
    $db_path=dirname(__FILE__).'/./db/taranto.sqlite';
    $username=$response["message"]["from"]["username"];
    $first_name=$response["message"]["from"]["first_name"];

    $db1 = new SQLite3($db_path);
    $q = "SELECT lat,lng FROM ".DB_TABLE_GEO ." WHERE bot_request_message='".$reply_to_msg['message_id']."'";
    $result=	$db1->query($q);
    $row = array();
    $i=0;

    while($res = $result->fetchArray(SQLITE3_ASSOC)){

    						if(!isset($res['lat'])) continue;

    						 $row[$i]['lat'] = $res['lat'];
    						 $row[$i]['lng'] = $res['lng'];
    						 $i++;
    				 }

    		//inserisce la segnalazione nel DB delle segnalazioni georiferite
    			$statement = "UPDATE ".DB_TABLE_GEO ." SET text='".$text."',file_id='". $file_id ."',filename='". $file_name ."',first_name='". $first_name ."',file_path='". $file_path ."',username='". $username ."' WHERE bot_request_message ='".$reply_to_msg['message_id']."'";
    			print_r($reply_to_msg['message_id']);
    			$db->exec($statement);
    	//		$this->create_keyboard_temp($telegram,$chat_id);

    if ($text=="farmacie")
    {
      $around=AROUND;
    	$tag="amenity=pharmacy";

    	      $lon=$row[0]['lng'];
    				$lat=$row[0]['lat'];
    	//prelevo dati da OSM sulla base della mia posizione
    					$osm_data=give_osm_data($lat,$lon,$tag,$around);

    					//rispondo inviando i dati di Openstreetmap
    					$osm_data_dec = simplexml_load_string($osm_data);

    					//per ogni nodo prelevo coordinate e nome
    					foreach ($osm_data_dec->node as $osm_element) {

    						$nome="";
    						foreach ($osm_element->tag as $key) {
                  //print_r($key);
    							if ($key['k']=='name' || $key['k']=='wheelchair' || $key['k']=='phone' || $key['k']=='addr:street' || $key['k']=='bench'|| $key['k']=='shelter')
    							{
                    $valore=utf8_encode($key['v']);
                    $valore=str_replace("yes","si",$valore);

    							if ($key['k']=='wheelchair')
    									{

    											$valore=str_replace("limited","con limitazioni",$valore);
    											$nome .="Accessibile da disabili: ".$valore;
    									}
    							if ($key['k']=='phone')	$nome  .="Telefono: ".utf8_encode($key['v'])."\n";
    							if ($key['k']=='addr:street')	$nome .="Indirizzo: ".utf8_encode($key['v'])."\n";
    							if ($key['k']=='name')	$nome  .="Nome: ".utf8_encode($key['v'])."\n";

                  }

    						}
    						//gestione musei senza il tag nome
    						if($nome=="")
    						{
    							//	$nome=utf8_encode("Luogo non presente o identificato su Openstreetmap");
    							//	$content = array('chat_id' => $chat_id, 'text' =>$nome);
    							//	$telegram->sendMessage($content);
    						}
                $nome=utf8_decode($nome);
    						$content = array('chat_id' => $chat_id, 'text' =>$nome);
    						$telegram->sendMessage($content);


  $longUrl = "http://www.openstreetmap.org/?mlat=".$osm_element['lat']."&mlon=".$osm_element['lon']."#map=19/".$osm_element['lat']."/".$osm_element['lon']."/".$_POST['qrname'];

  $apiKey = API;

  $postData = array('longUrl' => $longUrl, 'key' => $apiKey);
  $jsonData = json_encode($postData);

  $curlObj = curl_init();

  curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
  curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curlObj, CURLOPT_HEADER, 0);
  curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
  curl_setopt($curlObj, CURLOPT_POST, 1);
  curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

  $response = curl_exec($curlObj);

  // Change the response json string to object
  $json = json_decode($response);

  curl_close($curlObj);
//  $reply="Puoi visualizzarlo su :\n".$json->id;
  $shortLink = get_object_vars($json);
//return $json->id;

  $reply="Puoi visualizzarlo su :\n".$shortLink['id'];

                $chunks = str_split($reply, self::MAX_LENGTH);
                foreach($chunks as $chunk) {
                 // $forcehide=$telegram->buildForceReply(true);
                    //chiedo cosa sta accadendo nel luogo
                    $content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
                    $telegram->sendMessage($content);

                }
            //		$content = array('chat_id' => $chat_id, 'text' => $reply);
    				//		$telegram->sendMessage($content);
    					 }

    					//crediti dei dati
    					if((bool)$osm_data_dec->node)
    					{
    						$content = array('chat_id' => $chat_id, 'text' => utf8_encode("Queste sono le Farmacie vicine a te entro 5km \n(dati forniti tramite OpenStreetMap. Licenza ODbL (c) OpenStreetMap contributors)"),'disable_web_page_preview'=>true);
    						$bot_request_message=$telegram->sendMessage($content);
    					}else
    					{
    						$content = array('chat_id' => $chat_id, 'text' => utf8_encode("Non ci sono sono luoghi vicini, mi spiace! Se ne conosci uno nelle vicinanze mappalo su www.openstreetmap.org"));
    						$bot_request_message=$telegram->sendMessage($content);
    					}
    }else{


    			$reply = "La segnalazione è stata Registrata.\n".$risposta."\nGrazie! ";

          // creare una mappa su umap, mettere nel layer -> dati remoti -> il link al file map_data.csv
    			$longUrl= "http://umap.openstreetmap.fr/it/map/segnalazioni-con-taranto-bot-x-interni_56688#19/".$row[0]['lat']."/".$row[0]['lng']."/".$_POST['qrname'];
          $apiKey = API;

          $postData = array('longUrl' => $longUrl, 'key' => $apiKey);
          $jsonData = json_encode($postData);

          $curlObj = curl_init();

          curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
          curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($curlObj, CURLOPT_HEADER, 0);
          curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
          curl_setopt($curlObj, CURLOPT_POST, 1);
          curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

          $response = curl_exec($curlObj);

          // Change the response json string to object
          $json = json_decode($response);

          curl_close($curlObj);
        //  $reply="Puoi visualizzarlo su :\n".$json->id;
          $shortLink = get_object_vars($json);
        //return $json->id;

          $reply .="Puoi visualizzarlo su :\n".$shortLink['id'];

    			$content = array('chat_id' => $chat_id, 'text' => $reply);
    			$telegram->sendMessage($content);
    			$log=$today. ";information for maps recorded;" .$chat_id. "\n";
          $csv_path=dirname(__FILE__).'/./db/map_data.csv';
          $db_path=dirname(__FILE__).'/./db/taranto.sqlite';
    			exec(' sqlite3 -header -csv '.$db_path.' "select * from segnalazioni;" > '.$csv_path. ' ');

    }

    		}
			//comando errato
			else{
				 $reply = "Hai selezionato un comando non previsto";
				 $content = array('chat_id' => $chat_id, 'text' => $reply);
				 $telegram->sendMessage($content);
				 $log=$today. ";wrong command sent;" .$chat_id. "\n";
			 }


			//aggiorna tastiera
			$this->create_keyboard($telegram,$chat_id);

			//log
			file_put_contents(dirname(__FILE__).'/./telegram.log', $log, FILE_APPEND | LOCK_EX);

			//db
		//	$statement = "INSERT INTO " . DB_TABLE_LOG ." (date, text, chat_id, user_id, location, reply_to_msg) VALUES ('" . $today . "','" . $text . "','" . $chat_id . "','" . $user_id . "','" . $location . "','" . $reply_to_msg . "')";
    //        $db->exec($statement);

	}


	// Crea la tastiera
	 function create_keyboard($telegram, $chat_id)
		{
				$option = array(["meteo oggi","previsioni"],["messaggi allerta","bollettini criticità"],["temperatura","informazioni"]);
				$keyb = $telegram->buildKeyBoard($option, $onetime=false);
				$content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[seleziona un comando]");
				$telegram->sendMessage($content);
		}


  function location_manager($db,$telegram,$user_id,$chat_id,$location)
  	{
  			$lng=$location["longitude"];
  			$lat=$location["latitude"];

  			//rispondo
  			$response=$telegram->getData();
  			$bot_request_message_id=$response["message"]["message_id"];
  			$time=$response["message"]["date"]; //registro nel DB anche il tempo unix

  			$h = "2";// Hour for time zone goes here e.g. +7 or -4, just remove the + or -
  			$hm = $h * 60;
  			$ms = $hm * 60;
  			$timec=gmdate("Y-m-d\TH:i:s\Z", $time+($ms));
  			$timec=str_replace("T"," ",$timec);
  			$timec=str_replace("Z"," ",$timec);
  			//nascondo la tastiera e forzo l'utente a darmi una risposta
  			$forcehide=$telegram->buildForceReply(true);


  			//chiedo cosa sta accadendo nel luogo
//  		$content = array('chat_id' => $chat_id, 'text' => "[Scrivici cosa sta accadendo qui]", 'reply_markup' =>$forcehide, 'reply_to_message_id' =>$bot_request_message_id);

      $content = array('chat_id' => $chat_id, 'text' => "[Cosa vuole comunicarci su questo posto? oppure scriva: farmacie (tutto minuscolo).\n\nLe indicheremo quelle più vicini nell'arco di 5km]", 'reply_markup' =>$forcehide, 'reply_to_message_id' =>$bot_request_message_id);

        $bot_request_message=$telegram->sendMessage($content);

  			//memorizzare nel DB
  			$obj=json_decode($bot_request_message);
  			$id=$obj->result;
  			$id=$id->message_id;

  			//print_r($id);
  			$statement = "INSERT INTO ". DB_TABLE_GEO. " (lat,lng,user,username,text,bot_request_message,time,file_id,file_path,filename,first_name) VALUES ('" . $lat . "','" . $lng . "','" . $user_id . "',' ',' ','". $id ."','". $timec ."',' ',' ',' ',' ')";
  						$db->exec($statement);


  	}


  }

  ?>
