
<style>
.word-details label {
    font-size: 1.4em;
}

#paging {
  padding: 0 20px 20px 20px;
  font-size: 13px;
  margin-top: 10px;
}

#paging a {
  color: #000;
  background: #e0e0e0;
  padding: 8px 12px;
  margin-right: 5px;
  text-decoration: none;
}

#paging a.aktif {
  background: #000 !important;
  color: #fff;
}

#paging a:hover {
  border: 1px solid #000;
}

.hidden {
  display: none;
}




</style>


<section id="wordList">

    <dl class="inline">
    <?php if ($template['tag_selected'] !== null || $template['priority'] !== null ):?>
        
        <div><dt>Filtrado por <dt><dd>
            <?php if ($template['tag_selected'] !== null  ):?>
                <?php echo $template['tag_selected']['name'];?>  
            <?php endif;?>

            <?php if ($template['priority'] !== null  ):?>
                <?php echo ($template['priority']) ? ' prioridad: ' . $template['priority'] : '';?>
            <?php endif;?>
        </dd></div>
    <?php endif;?>
        <div><dt>Cantidad <dt><dd><?php echo count($template['list']);?></dd></div>
    </dl>


<?php if ( !$template['tag_selected'] || $template['tag_selected']['id'] > 1 ):?>
    <form action="" method="get">
        <input type="hidden" name="tag" value="<?php echo $template['tag_selected']['id'];?>">
        <p class="radios-prioridad">
            <span>Prioridad:</span>

            <span class="radio-pair"><input type="radio" id="priority_0" name="priority" value="0" checked> <label for="priority_0">Todas</label></span>
            <span class="radio-pair"><input type="radio" id="priority_1" name="priority" value="1"> <label for="priority_1">1</label></span>
            <span class="radio-pair"><input type="radio" id="priority_2" name="priority" value="2"> <label for="priority_2">2</label></span>
            <span class="radio-pair"><input type="radio" id="priority_3" name="priority" value="3"> <label for="priority_3">3</label></span>

            <input type="submit" name="list_priority_btn" value="Por prioridad">

        </p>

    </form>
<?php endif;?>






<p>Con las seleccionadas se puede cambiar prioridad <?php if ( $template['tag_selected'] && $template['tag_selected']['id'] == 1 ):?>o eliminar del listado<?php endif;?></p>

<form action="" method="post">

<input type="hidden" name="tag_id" value=" <?php echo $template['tag_selected']['id'];?>">


<?php 

if ($template['list']) {
    echo '<ul id="review-list">';
}

foreach($template['list'] as $item): 
    $extra_info = "";

    if ( $item['highest_priority'] ) {
        $extra_info .= " priL:" . $item['highest_priority'];
    }
    if ( $item['in_anki'] ) {
        $extra_info .= ($item['in_anki'] ? 'anki' : '');
    }


    $word_label = $item['word'] . ' ' . \WordAdmin\Loader\stringBetweenBrackets($item['reading']);

    ?>

    <li> 
        

        <div class="word-details">
            <input type="checkbox" name="words[]" value="<?php echo $item['id'];?>" id="word_<?php echo $item['id'];?>">
            <label for="word_<?php echo $item['id'];?>">
                <span <?php echo ($item['in_anki']) ? 'class="check_in_anki in_anki--1"' : '';?>><?php echo $word_label ;?></span> 
            </label>
            <br><?php echo $item['definition'];?>
            <br><?php echo ($extra_info) ? '<span style="font-size: 0.8em">(' . $extra_info . ')</span>' : '';?>
        </div>

        <div class="word-actions">
            <a href="/admin/?view=<?php echo $item['id'];?>" target="_blank">Ver</a>
        </div>

    </li>

<?php endforeach;

if ($template['list']) {
    echo "</ul>";
}

?>


<hr>
<p class="radios-prioridad">
    <span>Prioridad:</span>

    <span class="radio-pair"><input type="radio" id="priority_1" name="new_priority" value="1"> <label for="priority_1">1</label></span>
    <span class="radio-pair"><input type="radio" id="priority_2" name="new_priority" value="2"> <label for="priority_2">2</label></span>
    <span class="radio-pair"><input type="radio" id="priority_3" name="new_priority" value="3"> <label for="priority_3">3</label></span>

    <input type="submit" name="priority_change" value="Cambiar">

</p>

<?php if ( $template['tag_selected'] && $template['tag_selected']['id'] == 1 ):?>
    <input type="submit" name="words_remove" value="Eliminar del listado">
<?php endif;?>

</form>
</section>




<?php 

echo \WordAdmin\Loader\templatePartLoadLib('jquery-local');
echo \WordAdmin\Loader\templatePartLoadLib('css-datatables-local');
echo \WordAdmin\Loader\templatePartLoadLib('js-paging-local');

?>

<script type="text/javascript">

$(document).ready( function () {
    
    $("#review-list").JPaging({
	    pageSize: 100
	  });
    
} );

</script>

