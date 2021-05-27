<?php 

namespace WordAdmin\Page\Index;

require_once $_project_dir.'/loader.php';
$tags =  \WordAdmin\ORM\getTagsList($_db);

if (isset($_POST['save_word_tags'])) {

	$word_id = $_POST['tagword_id'];
	$tags_seleccionadas = $_POST['wordTag'];
	$tags_word_db = \WordAdmin\ORM\getTagsForWord($_db, $word_id);

	$tags_word_db_all = $tags_word_db;
	$tags_word_db = array();

	foreach ($tags_word_db_all as $item) {
		$tags_word_db[] = $item['id'];
	}

	$tags_eliminar = array();
	$tags_agregar  = array();

	foreach($tags_seleccionadas as $selected) {
		if (!in_array((int)$selected, $tags_word_db)) {
			$tags_agregar[] = (int)$selected;
		}
	}

	foreach($tags_word_db as $selected) {
		if (!in_array((int)$selected, $tags_seleccionadas)) {
			$tags_eliminar[] = (int)$selected;
		}
	}

	// TODO: esto se hace porque desde hace un tiempo (ej: antes de 2021 abril) la unica lista de etiqueta que maneja esto es el del estudiar
	if ($tags_seleccionadas == null) {
		$tags_eliminar[] = 1;
	}

	
	$stmt_tags_add = $_db->pdo->prepare("INSERT INTO words_tags(word_id,tag_id) VALUES (?,?)");
	$stmt_tags_delete = $_db->pdo->prepare("DELETE FROM words_tags WHERE word_id = ? AND tag_id = ?");

	$_db->pdo->beginTransaction();
	
	// delete 
	foreach($tags_eliminar as $item) {
		$stmt_tags_delete->execute([$word_id,$item]);
	}

	// add 
	foreach($tags_agregar as $item) {
		$stmt_tags_add->execute([$word_id,$item]);
	}


    $_db->pdo->commit();
}


if (isset($_POST['delete_word'])) {
	$stmt = $_db->pdo->prepare("UPDATE words SET deleted = true WHERE id = ?");
	$stmt->execute([ $_POST['mainword_id'] ]);


	/*
	// viejo (ahora se le pone flag) delete word and logs, tags, etc

	$query_tags = $_db->pdo->prepare("DELETE FROM words_tags WHERE word_id = ?");
	$query_log  = $_db->pdo->prepare("DELETE FROM word_history WHERE word_id = ?");
	$query_word = $_db->pdo->prepare("DELETE FROM words WHERE id = ?");

	$_db->pdo->beginTransaction();


	$query_tags->execute([$_POST['tagword_id']]);
	$query_log->execute([$_POST['tagword_id']]);
	$query_word->execute([$_POST['tagword_id']]);
	$_db->pdo->commit();
	*/


	$template = array(
		'step' => 'initial',
		'deleted' => true,
		'tags' => $tags,
		'words' => \WordAdmin\ORM\getListWords($_db),
	);

	\WordAdmin\Loader\loadTemplate('admin', $template);
	header("location: /admin");

}



if (isset($_GET['view'])) {
	$word_id = \filter_var($_GET['view'], \FILTER_VALIDATE_INT);
	
	$found   = \WordAdmin\ORM\getWordFromIdHistory($_db, $word_id);
	if (!$found || count($found) == 0) {
		die("palabra no encontrada");
	}
	$logTags = \WordAdmin\ORM\getTagsFromWordLog($_db, $word_id);
	$new_word = \WordAdmin\ORM\getInfoWordStadistics($_db, $word_id);
	// conteo de log tag por tag se podría hacer por varias consultas por sql. Pero para evitar eso:
	$ctr_tags = array();

	foreach($logTags as $logTag) {
		$tag_id = $logTag['source_tag'];

		if (!\key_exists($tag_id , $ctr_tags)) {
			$ctr_tags[$tag_id] = 0;
		}

		foreach($found as $entry) {
			if ($entry['source_tag'] != $tag_id ) {
				continue;
			}

			$ctr_tags[$tag_id]++;
		}
	}


	$template = array(
		'step' => 'view',
		'word' => $found,
		'tags' =>$tags,
		'wordtags' => \WordAdmin\ORM\getTagsForWord($_db, $word_id),
		'sources_count' => \WordAdmin\ORM\getListSources($_db,true),
		'tagLog_count' => $ctr_tags,
		'isWordForStudying' =>  \WordAdmin\ORM\getIsWordForStudying($_db, $word_id),
		'stadistics' =>  $new_word,
	);

	\WordAdmin\Loader\loadTemplate('admin', $template);
}



if (isset($_GET['log'])) {

	if ( isset($_GET['action'])  && $_GET['action'] == "edit") {

			$tmp = \filter_var($_GET['log'], \FILTER_VALIDATE_INT);
		
			$found = false;
			if ($tmp) {
				$found = \WordAdmin\ORM\getWordLogFromId($_db,$tmp);
			}
			
			$template = array(
				'step' => 'edit-log',
				'log' => $found,
				'tags' => $tags,
			);
		
			\WordAdmin\Loader\loadTemplate('admin', $template);
	}

	if ($_POST['savelog'] == "Guardar") {

		$log_id      = $_POST['log_id'];
		$word_id      = (int)$_POST['word_id'];
		$log_source  = $_POST['log_source'];
		$log_example = $_POST['log_example'];
		$log_prioridad = $_POST['log_prioridad'];
		$log_date      = $_POST['log_date'];
		$log_tag      = $_POST['log_tag'];


		$tmp = \filter_var($log_id, \FILTER_VALIDATE_INT);

		$found = false;

		if ($tmp) {

			$stmt = $_db->pdo->prepare("UPDATE word_history SET source = ?, examples = ?, priority = ?, created = ?, source_tag = ? WHERE id = ?");
			$stmt->execute([
				$log_source, $log_example, $log_prioridad, $log_date, $log_tag, $log_id
			]);
		}
		
		header("location: /admin/?view=" . $word_id);
		exit();

	}


    if ($_POST['dellog'] == "Eliminar") {

		$log_id = \filter_var($_POST['log_id'], \FILTER_VALIDATE_INT);
		$word_id = (int)$_POST['word_id'];

		$_db->run("DELETE FROM word_history WHERE id = ?", [$log_id]);

		header("location: /admin/?view=" . $word_id);
		exit();
    }

}


if (isset($_GET['ankied'])) {
	// palabras que estan en anki pero siguen aca

	$template = array(
		'tags' => $tags,
		'step' => 'view-ankied',
		'words' => \WordAdmin\ORM\getListWords($_db, array("ankied" => true)),
		'sources_count' => \WordAdmin\ORM\getListSources($_db,true),
	);

	\WordAdmin\Loader\loadTemplate('admin', $template);

}

if (isset($_GET['softdeleted'])) {
	// palabras eliminadas para ver si eliminar correctamente (hard-delete)

	$template = array(
		'tags' => $tags,
		'step' => 'view-softdeleted',
		'words' => \WordAdmin\ORM\getListWords($_db, array("deleted" => true)),
	);

	\WordAdmin\Loader\loadTemplate('admin', $template);

}




$template = array(
	'tags' => $tags,
	'step' => 'initial',
	'words' => \WordAdmin\ORM\getListWords($_db),
	'sources_count' => \WordAdmin\ORM\getListSources($_db,true),
);

\WordAdmin\Loader\loadTemplate('admin', $template);