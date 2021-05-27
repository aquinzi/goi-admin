<?php 

namespace WordAdmin\API;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET, POST");
header("Access-Control-Max-Age: 3600");


require_once $_project_dir.'/loader.php';


/**
 * Create json object and set http response code. Adds response key in json object, dies.
 *
 * @param int $response_code  http_response_code (200 (default), 400,....)
 * @param array $payload
 * @return void
 */
function printApiResponse($payload, $response_code=200) {

	http_response_code($response_code);
	$payload["response"] = $response_code;

	echo \json_encode($payload);
	die();
}



$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];

$request = \str_replace("/api/", "", $request);


$request = explode("?", $request)[0];


$allow_access = false;

if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
	list($type, $data) = explode(" ", $_SERVER["HTTP_AUTHORIZATION"], 2);
	if (strcasecmp($type, "Bearer") == 0) {
		if ($data == $_apikey ) {
			$allow_access = true;
		}
	} else {
		printApiResponse(array("message" => "Falto bearer"), 400);
	}
} else {
	printApiResponse(array("message" => "Falto header"), 400);
}

if (!$allow_access) {
	printApiResponse(array("message" => "No tenes permisos"), 400);
}


switch($method) {
	case "GET":
		if ($request == "listanki") {
			list_words_anki($_db);
		}
		break;

	case "POST":
		if ($request == "taganki") {

			// esto es para poder aceptar un post con array/json
			$raw_post = file_get_contents("php://input");
			$data = json_decode($raw_post, true);

			if ($data) {
				wordsWriteAnkiId($_db, $data);
			}
		}

		break;

	default:
		printApiResponse(array("message" => "No implementado"), 400);
}




/**
 * Genera lista para checkear contra anki
 * Devuelve las palabras que faltan por checkear contra anki (las que no se marcaron como activas)
 * 
 * @return json
 */
function list_words_anki($db) {
	$found = \WordAdmin\ORM\getWordListCheckIfInAnki($db);

	printApiResponse(array("data" => $found), 200);

}


function wordsWriteAnkiId($db, $pairs){

	// se inserta por cada palabra, anki id. Nueva forma para "taggear" las que estan en anki (antes se usaba un tag)


	$stmt_tags = $db->pdo->prepare("UPDATE words SET anki_noteid = ? WHERE id = ?");

	$db->pdo->beginTransaction();

	$not_valids = array();
	foreach ($pairs as $pair) {

		if (\in_array('dbid', $pair) || \in_array('ankiid', $pair)  ) {
			$not_valids[] = $pair;
			continue;
		}

		if ( !filter_var($pair['dbid'], FILTER_VALIDATE_INT) ) {
			$not_valids[] = $pair;
			continue;
		}
		
		$stmt_tags->execute([$pair['ankiid'], $pair['dbid']]);
	}

	$db->pdo->commit();
	

	$payload = array("message" => "Etiquetados");
	
	if (count($not_valids) > 0 ) {
		$payload["message"] = "Algunos etiquetados pero otros con avisos.";
		$payload["data"] = $not_valids;
	}
	
	printApiResponse($payload, 200);

}
