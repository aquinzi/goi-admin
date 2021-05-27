<?php 

namespace WordAdmin\ORM;


function getTagsList($db) {

    return $db->run("SELECT id, name FROM tags")->fetchAll();
}


function searchWord($db, $string) {
    $prepare = '%' . trim($string) . '%';
    return $db->run("SELECT wid as id, word, reading, definition, IF(in_anki,true,false) AS in_anki, deleted, word_tags, highest_priority FROM v_WordInfo WHERE word LIKE ? OR reading LIKE ?", [$prepare, $prepare])->fetchAll();
}

/**
 * Add word log/history
 *
 * @param conn $db
 * @param int $word_id  word id a que hace referencia
 * @param array $options  contiene priority, source, example, source_tag, date
 * @return int inserted id
 */
function addWordLog($db, $word_id, $options) {

    if (!\array_key_exists('priority', $options) ) {
        $options['priority'] = 3;
    }

    if ( $options['priority'] == null) {
        $options['priority'] = 3;
    }

    if (!\array_key_exists('source', $options) || $options['source'] == null) {
        $options['source'] = null;
    }

    if (!\array_key_exists('example', $options) || $options['example'] == null) {
        $options['example'] = null;
    }

    if (!\array_key_exists('source_tag', $options) || $options['source_tag'] == null) {
        $options['source_tag'] = null;
    }

    $query_columns = "word_id,priority,source,examples,source_tag";
    $query_params  = "?,?,?,?,?";
    $query_values  = [
        $word_id,
        $options['priority'],
        $options['source'],
        $options['example'], 
        $options['source_tag']
        ];


    if (\array_key_exists('date', $options) && $options['date'] != null) {
        $query_columns = "word_id,priority,source,examples,created,source_tag";
        $query_params  = "?,?,?,?,?,?";
        $query_values  = [
            $word_id,
            $options['priority'],
            $options['source'],
            $options['example'], 
            $options['date'], 
            $options['source_tag']
            ];
    }

    $db->run("INSERT INTO word_history({$query_columns}) VALUES ({$query_params})", $query_values);

    $id = $db->pdo->lastInsertId();

    return $id;
}


/**
 * Add word to dictionary
 *
 * @param conn $db
 * @param string $word
 * @param string $reading
 * @param string $definition
 * 
 * @return int inserted id
 */
function addWordDictionary($db, string $word, string $reading, string $definition) {

    $db->run("INSERT INTO words(word,reading,definition,deleted) VALUES (?,?,?,0)", [$word,$reading,$definition]);

    $id = $db->pdo->lastInsertId();

    return $id;
}



/**
 * Get list of words. ~~Allows filtering~~
 *
 * @param conn $db
 * @param array $filter   Valid keys: priority, tags (array), ankied (true/false), deleted (true/false--)
 * @return void
 */
function getListWords($db, $filter=null) {


    $where_section = array();
    


    if ($filter && \array_key_exists("deleted", $filter)) {
        $where_section[] = "deleted = {$filter['deleted']}";
    }
    else {
        $where_section[] = "deleted = 0";
    }


    $where_anki = "";
    if ($filter && \array_key_exists("ankied", $filter)) {
        $where_anki = "in_anki IS NULL";
        if ($filter['ankied'] == true) {
            $where_anki = "NOT " . $where_anki;
        }
    }


    if ($where_anki) {
        $where_section[] = $where_anki;
    }

    if ($where_section) {
        $where_section = " WHERE " . implode(" AND ", $where_section);
    }


    return $db->run("SELECT
        wid AS id, word, reading, definition, word_count, word_tags AS tags, highest_priority, in_anki
    FROM v_WordInfo

    {$where_section}
    
    ORDER BY word_count DESC
    
    ")->fetchAll();
}



function getWordFromId($db, $word_id) {


    return $db->run('SELECT 
    tbl_winfo.wid AS id,
    tbl_winfo.word,
    tbl_winfo.reading,
    tbl_winfo.definition,
    tbl_winfo.in_anki,
    tbl_winfo.priority_history,
    tbl_winfo.word_count, 
    tbl_winfo.highest_priority

    FROM v_WordInfo tbl_winfo

    WHERE tbl_winfo.wid = ?', [$word_id])->fetchAll();
}


function getListWordsBySource($db, $source) {
    return $db->run(' 

        SELECT wid AS id, word, reading, definition, word_count, word_tags AS tags, highest_priority, in_anki  from word_history wh 

        JOIN v_WordInfo vwi ON vwi.wid = wh.word_id

        WHERE source = ?
        ORDER BY created DESC
        ', [$source])->fetchAll();
}



/**
 * Get word log history
 *
 * @param conn $db
 * @param int $word_id
 * @param string $sort  newest (to oldest, default), oldest
 * @return array
 */
function getWordFromIdHistory($db, $word_id, $sort="newest") {


    $tmp_sorting = "ORDER BY log_created DESC";
    if ($sort == "oldest") {
        $tmp_sorting = "ORDER BY log_created ASC";
    }

    return $db->run('SELECT 
        tbl_winfo.wid AS id,
        tbl_winfo.word,
        tbl_winfo.reading,
        tbl_winfo.definition,
        tbl_winfo.in_anki,
        tbl_winfo.priority_history,
        tbl_winfo.word_count, 
        tbl_winfo.highest_priority, 
        tbl_winfo.deleted, 
        wh.id AS log_id,
        date(wh.created) as log_created,
        wh.priority AS log_priority,
        wh.source AS log_source,
        wh.examples as log_examples,
        wh.source_tag

        FROM v_WordInfo tbl_winfo

        JOIN word_history wh on wh.word_id = tbl_winfo.wid
        WHERE tbl_winfo.wid = ?
        ' . $tmp_sorting . '
    
    ', [$word_id])->fetchAll();
}





function getTagsForWord($db, $word_id) {

    return $db->run('SELECT 
        source_tag AS id, tbl_tags.name AS name
        
        FROM word_history as wh
        
        INNER JOIN tags as tbl_tags ON wh.source_tag = tbl_tags.id
        WHERE word_id = ?', [$word_id])->fetchAll();
}



function getIsWordForStudying($db, $word_id) {

    $query = $db->run('SELECT COUNT(word_id) as total

        FROM words_tags
        WHERE word_id = ? AND tag_id = 1', [$word_id])->fetchAll();

    if ((int) $query[0]['total'] > 0) {
        return true;
    }

    return false;
}



/**
 * Get all sources
 * 
 * @param bool $count  return list sources (false) or only amount (true). Default: false 
 */
function getListSources($db, $count=false) {

    $field = "DISTINCT source";
    
    if ($count) {
        $field = "count(DISTINCT source)";
    }

    $result = $db->run('SELECT ' . $field . ' as source_total FROM word_history')->fetchAll();

    if ($count) {
        return $result[0]['source_total'];
    }

    return $result;

}





/**
 * Returns list of words to be checked to anki
 *  (if weren't checked)
 *
 * @return void
 */
function getWordListCheckIfInAnki($db) {

    return $db->run('SELECT 
        words.id, 
        words.word,
        words.reading,
        words.definition
        FROM 
            words
        WHERE 
            anki_noteid IS NULL 
        ')->fetchAll();
}

/**
 * Get words by tag, ignoring deleted
 * 
 * @param conn $db
 * @param int $tagId  the tag id to query to
 * 
 * @return null|array
 */
function getWordsByTag($db, $tagId) {

    return $db->run("SELECT
        wid AS id, word, reading, definition, word_count, word_tags AS tags, in_anki, highest_priority
        FROM 
            v_WordInfo
        INNER JOIN 
            word_history ON v_WordInfo.wid = word_history.word_id
        WHERE 
            source_tag = ?
            AND deleted = 0
    ", [$tagId])->fetchAll();
}


/**
 * Get words by tag and latest priority
 * 
 * @param conn $db
 * @param int $tagId  the tag id to query to
 * 
 * @return null|array
 */
function getWordsByTagPriority($db, $tagId, $priority) {

    return $db->run("SELECT
        wid AS id, word, reading, definition, word_count, word_tags AS tags, in_anki, highest_priority
        FROM 
            v_WordInfo
    INNER JOIN 
        word_history ON v_WordInfo.wid = word_history.word_id
    WHERE 
        source_tag = ? AND highest_priority = ? 
        AND deleted = 0
    ", [$tagId, $priority])->fetchAll();
}


/**
 * Get words by tag and latest priority
 * 
 * @param conn $db
 * @param int $tagId  the tag id to query to
 * 
 * @return null|array
 */
function getWordsByPriority($db, $priority) {

    return $db->run("SELECT
        wid AS id, word, reading, definition, word_count, word_tags AS tags, in_anki, highest_priority
        FROM 
            v_WordInfo
    WHERE 
        highest_priority = ?
        AND deleted = 0
    ", [$priority])->fetchAll();
}



/**
 * Get words by tag graduated, ignoring deleted
 * 
 * @param conn $db
 * @param int $tagId  the tag id to query to
 * 
 * @return null|array
 */
function getWordsByListGraduated($db) {

    return $db->run("
        SELECT
            wid AS id, word, reading, definition, word_count, word_tags AS tags, in_anki, highest_priority
        FROM 
            v_WordInfo
        INNER JOIN 
            words_tags ON v_WordInfo.wid = words_tags.word_id
        WHERE 
            tag_id = 1
            AND deleted = 0
    ")->fetchAll();
}





/**
 * Busca las palabras que tuvieron una prioridad en particular
 *
 * @param integer $priority
 * @return bool|array
 */
function getWordsTuvieronPrioridad($db, int $priority) {

    if ( $priority == 0 || $priority > 3 ) {
        return false;
    }

    $priority = (string)$priority;


    // Note: por alguna razon no funciona el binding con el like aca. Concadenar normal
    return $db->run('
        SELECT wid,word,reading,definition, in_anki, word_count , word_tags 
        FROM v_WordInfo  
        WHERE priority_history LIKE "%' . $priority. '%"
    ', [$priority])->fetchAll();
}


/**
 * Busca las palabras para estudiar 
 *
 * @param conn $db
 * @return array
 */
function getWordsToStudy($db) {

    $tags = \WordAdmin\ORM\getTagsList($db);

    $saltear_menos_puntaje = 2.0; 

    // note: lista_estudio = lista graduadas
    $list_words_db =  $db->run('
        SELECT 
            wid, wid AS id, word, reading, definition, in_anki, word_count,  priority_history, highest_priority,
            IF(tag_id = 1, true,false) AS lista_estudio, log_tags_all

        FROM 
            v_WordInfo  

        LEFT JOIN 
            words_tags wt ON wt.word_id = wid 
        
        WHERE 
            (deleted = 0 AND word_count > 1)
            OR (deleted = 0 AND word_count = 1 and highest_priority = 1) 

        ORDER BY 
            word_count DESC
    ')->fetchAll();


    $list_words = $list_words_db;
    $list_final = array();

    foreach ($list_words_db as $index => $word) {

        $word_new = \WordAdmin\Loader\getWordStadistics($word, $tags );
        
        // Filtramos. Si no cumplen, saltear, por ahora no nos interesa
        if ((int)$word_new['highest_priority'] != 1 && $word_new['points_con_origen'] < ($saltear_menos_puntaje*100)) {
            continue;
        }
        
        $list_words[$index] = $word_new;

        $list_final[] = $list_words[$index];
    }

    return $list_final;

}

/**
 * @see getWordsToStudy
 *
 * @param [type] $db
 * @param [type] $word_id
 * @return void
 */
function getInfoWordStadistics($db, $word_id) {
    $tags = \WordAdmin\ORM\getTagsList($db);

    $list_words_db =  $db->run('
        SELECT 
            wid, wid AS id, word, reading, definition, in_anki, word_count,  priority_history, highest_priority,
            IF(tag_id = 1, true,false) AS lista_estudio, log_tags_all

        FROM 
            v_WordInfo  

        LEFT JOIN 
            words_tags wt ON wt.word_id = wid 
        
        WHERE 
            wid = ?
            AND (
                (deleted = 0 AND word_count > 1)
                OR (deleted = 0 AND word_count = 1 and highest_priority = 1) 
            )
    ', [$word_id])->fetchAll();


    $word_new = \WordAdmin\Loader\getWordStadistics($list_words_db[0], $tags );

    return $word_new;


}





function getWordLogFromId($db, $log_id) {

    return $db->run('SELECT 
        word_history.id, word_id, word_history.created, priority, source, examples, words.word, source_tag
    FROM word_history
    LEFT JOIN words ON word_id = words.id
    WHERE word_history.id = ?', [$log_id])->fetch();
}




function getWordsInAnki($db) {

    return $db->run('SELECT 
        words.id, 
        words.word,
        words.reading
        FROM 
            words
        WHERE 
            anki_noteid IS NULL 
        ')->fetchAll();
}


/**
 * Return tags ids of word log
 *
 * @param conn $db
 * @param int $word_id
 * @return array
 */
function getTagsFromWordLog($db, $word_id) {
    return $db->run('SELECT DISTINCT source_tag FROM word_history WHERE word_id = ?', [$word_id])->fetchAll();
}