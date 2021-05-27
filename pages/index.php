<?php 

namespace WordAdmin\Page\Index;

require_once $_project_dir.'/loader.php';


if (isset($_POST['search_word']) ) {

    $found = \WordAdmin\ORM\searchWord($_db,$_POST['search_word'] );

    echo json_encode( array( 
        "res" => $found
        ) 
    );
    die();
}



if (isset($_POST['queryJisho']) ) {

    $url_jisho = 'https://jisho.org/api/v1/search/words?keyword='.urlencode($_POST['queryJisho']);

    $response_json = json_decode(file_get_contents($url_jisho),true);

    if ($response_json['meta']['status'] != 200) {
        echo json_encode( array( 
            "res" => false
            ) 
        );
        die();
    }

    $response_json = $response_json['data'];

    $cleaned_response = array();

    foreach($response_json as $item) {

        $cleaned_item = array(
            "slug"       => $item['slug'],
            "word"       => $item['japanese'][0]['word'],
            "reading"    => $item['japanese'][0]['reading'],
            "definition" => "",
            "others"     => "",
        );

        // without kanji: some words do not have "word" , just reading
        if (empty($cleaned_item['word'] && $cleaned_item['reading'])) {
            $cleaned_item['word'] = $cleaned_item['reading'];
        }

        $tmp_others = $item['japanese'];
        unset($tmp_others[0]);
        $tmp_others = \json_encode($tmp_others);
        $cleaned_item['others'] = $tmp_others;

        $tmp_defs = array();
        
        foreach($item['senses'] as $index => $def) {
            $tmp_defs[] = "(" . ($index + 1) . ")  ". implode("; ", $def['english_definitions']);
        }

        $cleaned_item['definition'] = implode(' ; ', $tmp_defs);

        $cleaned_response[] = $cleaned_item;
    }

    echo json_encode( array( 
        "res" => $cleaned_response
        ) 
    );
    die();
}


// para agregado tipo recolleccion
if (isset($_POST['processme'])) {
    // process (aka insert all new words and return them in list with an ID)

    $entries = $_POST['row'];
    $total = count($entries['word']);
    $entriesDatabase = array();

    for ($i=0; $i < $total; $i++) { 

        $tmp = array(
            'word'       => $entries['word'][$i],
            'reading'    => $entries['reading'][$i],
            'definition' => $entries['definition'][$i],
            'id'         => "",
        );

        if ( !in_array($entries['word_db'][$i], ['jisho', 'custom'])) {
            // ya estaba en db, pasar info
            $tmp['id'] = $entries['word_db'][$i];
            
        }
        else {
            // a veces se ingresa pero se rompe el sistema por X. 
            // Buscar palabra en db 
            $id = \WordAdmin\ORM\searchWord($_db, $entries['word'][$i]);

            if ( $id ) {
                $tmp['id'] = $id;
            }
            else {
                // no estaba, se agrega al diccionario
                if (empty($entries['reading'][$i])) {
                    $entries['reading'][$i] = $entries['word'][$i];
                }
            
                $id = \WordAdmin\ORM\addWordDictionary($_db, $entries['word'][$i], $entries['reading'][$i], $entries['definition'][$i]);
                $tmp['id'] = $id;
            }
        }
        
        $entriesDatabase[] = $tmp;
    }


    $template = array(
        'tags' => \WordAdmin\ORM\getTagsList($_db),
        'step' => 'process', 
        'listWords' => $entriesDatabase
    );
    
    
    \WordAdmin\Loader\loadTemplate('index', $template);
}


// para agregado tipo recolleccion
if (isset($_POST['saveme'])) {
    // guardar en log

    $entries = $_POST['list'];
    $entries_source = $_POST['list']['source'];
    $entries_date = $_POST['list']['date'];
    $entries_tag = $_POST['list']['tag_general'];
    $entries_priority = \filter_var($_POST['list']['prioridad_general'], \FILTER_VALIDATE_INT);

    // TODO: pasar esto a ORM y/o clase
    if (!empty($entries_date)) {
        $stmt = $_db->pdo->prepare("INSERT INTO word_history(word_id,priority,source,examples,created) VALUES (?,?,?,?,?)");
    }
    else {
        $stmt = $_db->pdo->prepare("INSERT INTO word_history(word_id,priority,source,examples) VALUES (?,?,?,?)");
    }

    $stmt_tags = $_db->pdo->prepare("INSERT INTO words_tags(word_id,tag_id) VALUES (?,?)");

    $_db->pdo->beginTransaction();

    foreach ($entries as $entry_id => $entry_info) {
        if ( in_array($entry_id, array("source", "date", "tag_general", "prioridad_general"))) {
            continue;
        }

        if (array_key_exists('example', $entry_info) && empty($entry_info['example'])) {
            $entry_info['example'] = null;
        }

        $tmp_priority = ($entries_priority === 0) ? $entry_info['priority'] : $entries_priority;


        $params = [$entry_id, $tmp_priority, $entries_source, $entry_info['example']];

        if (!empty($entries_date)) {
            $params[] = $entries_date;
        }

        $stmt->execute($params);
            
        

        $tmp_tagid = ($entries_tag != "independiente") ? $entries_tag : $entry_info['tag'];
        
        $stmt_tags->execute([$entry_id,$tmp_tagid]);
    }
        
    $_db->pdo->commit();

    $template = array(
        'step' => 'finished', 
        'tags' => \WordAdmin\ORM\getTagsList($_db), // porque se hace hacky-redireccion
    );
    
    
    \WordAdmin\Loader\loadTemplate('index', $template);
}

// para agregado de a uno
if (isset($_POST['new_word_save'])) {
    $new = array(
        'source'   => $_POST['new_source'],
        'date'     => $_POST['new_date'],
        'tag'      => filter_var($_POST['new_word_tag'], \FILTER_VALIDATE_INT),
        'priority' => filter_var($_POST['new_priority'], \FILTER_VALIDATE_INT),
        'example'  => $_POST['new_example'],
        'dbid'     => null,
        'custom_word'    => $_POST['custom']['word'],
        'custom_reading' => $_POST['custom']['reading'],
        'custom_meaning' => $_POST['custom']['meaning'],
    );


    if (isset($_POST['queriedWord'])) {
        $tmp = \filter_var($_POST['queriedWord'], \FILTER_VALIDATE_INT);
        if ($tmp) {
            $new['dbid'] = $tmp;
        }
    }

    if (!$new['dbid'] && !empty($new['custom_word'])) {
       // new word, insert to db and get id to continue 

       $id = \WordAdmin\ORM\addWordDictionary($_db, $new['custom_word'], $new['custom_reading'], $new['custom_meaning']);
       $new['dbid'] = $id;
    }
    elseif ( !$new['dbid'] ) {
        // pasó algo raro. Salir, salir.
        // TODO: poner mensaje
        die("Opps, no hay id de bd ni palabra nueva....");
    }


    $id = \WordAdmin\ORM\addWordLog($_db, $new['dbid'], array(
        'priority'   => $new['priority'],
        'source'     => $new['source'],
        'example'    => $new['example'],
        'source_tag' => $new['tag'],
        'date'       => ($new['date']) ?: null,
    ));

    

    // para datos default de formulario. Sacamos lo que no necesitamos

    unset($new['dbid']);
    unset($new['example']);
    unset($new['custom_word']);
    unset($new['custom_reading']);
    unset($new['custom_meaning']);

    $template = array(
        'step' => 'finished-add', 
        'tags' => \WordAdmin\ORM\getTagsList($_db), // porque se hace hacky-redireccion
        'formValues' => $new,
    );

    \WordAdmin\Loader\loadTemplate('index', $template);
    
}


// ver ultimo listado cargado
if (isset($_GET['src'])) {

    $template = array(
        'tags' => $tags,
        'step' => 'chk-source',
        'words' => \WordAdmin\ORM\getListWordsBySource($_db, rawurldecode($_GET['src'])),
    );
    
    \WordAdmin\Loader\loadTemplate('admin', $template);


}



$template = array(
    'tags' => \WordAdmin\ORM\getTagsList($_db),
    'step' => 'add',
);

\WordAdmin\Loader\loadTemplate('index', $template);