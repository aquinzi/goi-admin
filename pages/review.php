<?php 

namespace WordAdmin\Page\Review;

require_once $_project_dir.'/loader.php';

$tagsList = \WordAdmin\ORM\getTagsList($_db);



// recordar que primero el post, porque los otros parametros de la pagina se manejan por get
if (isset($_POST['words_remove'])) {
	
	$words = $_POST['words'];
	$tag = $_POST['tag_id'];


	$stmt_tags_delete = $_db->pdo->prepare("DELETE FROM words_tags WHERE word_id = ? AND tag_id = ?");

	$_db->pdo->beginTransaction();
	
	// delete 
	foreach($words as $item) {
		$stmt_tags_delete->execute([$item, $tag]);
	}


   $_db->pdo->commit();
}


if (isset($_POST['priority_change'])) {
	$words = $_POST['words'];
	$prioridad = $_POST['new_priority'];


	//TODO: ver si se pone en batch o con esto va OK
	
	foreach($words as $word) {
		\WordAdmin\ORM\addWordLog($_db,(int)$word, array("priority" => (int)$prioridad, "examples" => "cambio haciendo repaso", "source" => "repaso"));
	}

}



if (isset($_GET['tag']) || isset($_GET['list_priority_btn'])) {
	// filter words by tag & priority
	$tagId = null;

    if (isset($_GET['tag'])) {
		 $tagId = filter_var($_GET['tag'], FILTER_VALIDATE_INT);
    }


	$priority = null;
	if (isset($_GET['priority'])) {
		$priority = filter_var($_GET['priority'], FILTER_VALIDATE_INT);
	}


	$words = null;

   if ($priority && $priority > 0) {
		$words = \WordAdmin\ORM\getWordsByPriority($_db, $priority);
		//$words = \WordAdmin\ORM\getWordsByTagPriority($_db, $tagId, $priority);
   }
	else {
		if ( $tagId == 1  ) {
			$words = \WordAdmin\ORM\getWordsByListGraduated($_db);
		}
		else {
			$words = \WordAdmin\ORM\getListWords($_db);
			//$words = \WordAdmin\ORM\getWordsByTag($_db, $tagId);
		}
	}
	

	$selected_tag = null;

	foreach($tagsList as $tag) {
		if ($tag['id'] == $tagId) {
			$selected_tag = $tag;
			break;
		}
	}

	$template = array(
		'step' => 'queried',
		'tags' => $tagsList,
		'list' => $words,
		'tag_selected' => $selected_tag,
		'priority' => $priority,
	);

	\WordAdmin\Loader\loadTemplate('review', $template);


}








$template = array(
	'step' => 'initial',
	'tags' => $tagsList,
	'list' => null,
);

\WordAdmin\Loader\loadTemplate('review', $template);
