<?php 

namespace WordAdmin\Page\ListStudy;

require_once $_project_dir.'/loader.php';


$tags = \WordAdmin\ORM\getTagsList($_db);

$list_words = \WordAdmin\ORM\getWordsToStudy($_db);

// orden por puntos mayor -> menor, y % aparicion mayor -> menor
$points  = array_column($list_words, 'points');
$apparition = array_column($list_words, 'tags_apparition_porcentage');
array_multisort($points, SORT_DESC, $apparition, SORT_DESC, $list_words );


$template = array(
	'step' => 'studylist',
	'tags' => $tags,
	'words' => $list_words,
);

\WordAdmin\Loader\loadTemplate('admin', $template);