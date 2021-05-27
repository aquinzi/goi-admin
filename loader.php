<?php 

namespace WordAdmin\Loader;

require_once __DIR__.'/inc/db.class.php';
require_once __DIR__.'/inc/orm.php';

$_project_dir = __DIR__;
$_db_name     = "";
$_db_user     = "";
$_db_password = "";
$_db_host     = "localhost";
$_env_mode    = "dev";


if (file_exists(__DIR__.'/config-local.php')) {
	require __DIR__.'/config-local.php';
}
elseif (file_exists(__DIR__.'/config-shigoto.php')) {
	$_db_name = "tmp";
	$_db_user = "root";
	$_db_password = "root";
} 
elseif (file_exists(__DIR__.'/config-prod.php')) {
	$_env_mode = "prod";
	require __DIR__.'/config-prod.php';
}

if (empty($_db_password) || empty($_db_name) || empty($_db_name) ) {
	die("Faltan datos conexión");
}


$_db = new \aq\db\Database($_db_name, $_db_user, $_db_password, $_db_host);

// config -> moverlo a su propio archivo?

// generar contraseña hasehada
	//die(echo password_hash("admin", PASSWORD_BCRYPT));
	
$_username     = "admin";
$_userpassword = '$2y$10$IV3x874C6nluao/k5heHuuwHhj4dbdP6GHJzbbA.1ZRugQPg./Gmi';
$_apikey       = '$2y$10$IV3x874C6nluao/k5heHuuwHhj4dbdP6GHJzbbA.1ZRugQPg./Gmi';



$_words_points_system = array(
	// ponerle el puntaje (numeros puestos asi no mas. No hay razón alguna)
	'equivalencia_prioridad' => array(
		1 => 250,
		2 => 150,
		3 => 50
	),
	// puntaje por origen. Recordar que los IDs vienen de la tabla. Si esto funciona, pasar los puntajes allá
	// (numeros puestos asi no mas. No hay razón alguna)
	'origen' => array(
		3  => 150, // SNS
		4  => 20,  // canciones
		7  => 60,  // noticias
		8  => 110, // podcasts
		10 => 80,  // manga
	),
	// todas para >=  
	'highlight_colors' => array(
		3 => 'paintme-green',
		4 => 'paintme-yellow',
		5 => 'paintme-orange',
		6 => 'paintme-red',
		7 => 'paintme-reder',
	),
);



// end config






/**
 * Load template
 *
 * @param string $key  nombre del template
 * @param array $template variables para popular template
 * @return void
 */
function loadTemplate($key, $template) {
	global $_project_dir;

	require $_project_dir . '/templates/' . $key . '.php';
	die();
}

/**
 * Load template part (libs)
 * 
 * @param string $name  nombre de la parte a cargar
 * @return string
 */
function templatePartLoadLib($name) {
	switch($name) {

		case 'jquery-local':
			return '<script src="/libs/jquery-3.5.1.min.js"></script>';
		case 'jquery-cdn':
			return '<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>';
	
		case 'css-datatables-cdn':
			return '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.css">';
		case 'js-datatables-cdn':
			return '<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.js"></script>';
		case 'css-datatables-local':
			return '<link rel="stylesheet" type="text/css" href="/libs/datatables.min.css">';
		case 'js-datatables-local':
			return '<script type="text/javascript" charset="utf8" src="/libs/datatables.min.js"></script>';
		case 'js-paging-local':
			return '<script type="text/javascript" charset="utf8" src="/libs/paging.min.js"></script>';
		case 'js-paging-cdn':
			return '<script type="text/javascript" charset="utf8" src="/libs/paging.min.js"></script>';
		default: 
			return '';
	}
}


function stringBetweenBrackets($string) {
	return '【' . $string .'】';
}


function apparitionPorcentage($word_count, $amount_sources) {
	return round((int)$word_count/$amount_sources*100) . '%';
}


/**
 * Check si inició sesión
 * 
 * @return bool
 */
function checkIfLoggedIn() {
	session_start();
	if ($_SESSION['login']) {
		return true;
	}
    return false;
}

/**
 * Redireccion a login si no lo estaba
 */
function redirectToLogin() {
	$tmp = checkIfLoggedIn();
	
	if(!$tmp) {
		header('LOCATION:/login'); 
		die();
	}
}


/**
 * Print priority radio buttons, wrapped by spans
 *
 * @param str $field_id   field id root (ex.new_priority), suffixed autom. by priority number. (final: new_priority_1)
 * @param str $field_name
 * @param str $default_value
 * @param boolean $prepend_separate  --- not working
 * @return void
 */
function printRadiosPriority($field_id, $field_name, $default_value=null, $prepend_separate=false) {

	for ($i=1; $i<4; $i++) { 
		?>
		<span class="radio-pair">
			<input 
				type="radio" 
				id="<?php echo $field_id;?>_<?php echo $i;?>" 
				name="<?php echo $field_name;?>" 
				value="<?php echo $i;?>"
				<?php echo ($default_value && $default_value == $i) ? ' checked ' : '' ;?>
				> 
				<label for="<?php echo $field_name;?>_<?php echo $i;?>"><?php echo $i;?></label>
		</span>
		<?php 
	}
}

/**
 * Get stadistics for word: points, tags_apparition_porcentage, etc
 *
 * @param array $word (must contain word_count, log_tags_all, highest_priority)
 * @param array $tags \WordAdmin\ORM\getTagsList($db)
 * @return array ($word)
 */
function getWordStadistics($word, $tags) {
	global $_words_points_system;
	$equivalencia_prioridad = $_words_points_system['equivalencia_prioridad'];
	$puntaje_por_origen =  $_words_points_system['origen'];

	// se resta 1 a tags por tener el de estudio/graduadas
	$tmp_apparition = \WordAdmin\Loader\apparitionPorcentage($word['word_count'],count($tags) - 1);

	$tmp = (int)str_replace("%", "", $tmp_apparition);

	$tmp_points = ($tmp + $equivalencia_prioridad[(int)$word['highest_priority']]);

	// por source historial
	$tmp = explode(",",$word['log_tags_all']);
	$puntos_por_origen = 0;
	foreach($tmp as $tag_id) {
		if (\array_key_exists((int)$tag_id,$puntaje_por_origen)) {
			$puntos_por_origen += $puntaje_por_origen[(int)$tag_id];
		}
	}

	$tmp_points_sin_origen = $tmp_points;

	$tmp_points += $puntos_por_origen;


	$word['tags_apparition_porcentage'] = $tmp_apparition;
	$word['points_sin_origen'] = $tmp_points_sin_origen;
	$word['points_con_origen'] = $tmp_points;
	$word['points'] = $tmp_points / 100;

	return $word;
}


function colorTextByPoint($points) {
	global $_words_points_system;

	$paint_me = $points;
	foreach($_words_points_system['highlight_colors'] as $amount => $class) {
		if ((int)$points >= $amount ) {
			$paint_me = $class;
		} 
	}

	if ($paint_me) {
		$paint_me = "<span class='{$paint_me}'>{$points}</span>";
	}

  return $paint_me;
}