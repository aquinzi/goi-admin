<?php 

if (!$template['word']) {
    die("palabra no encontrada");
}

$main_word = $template['word'][0];

$tags_clean = array(); // id => nombre

foreach ($template['tags'] as $tag){
    if (!\key_exists($tag['id'], $tags_clean)) {
        $tags_clean[$tag['id']] = $tag['name'];
    }
}



?>

<h2><?php echo $main_word['word'];?><?php echo \WordAdmin\Loader\stringBetweenBrackets($main_word['reading']) ;?></h2>
<p><?php echo $main_word['definition'];?></p>


<hr>

<dl class="inline">

<div><dt>Puntaje<dt><dd>
<?php echo \WordAdmin\Loader\colorTextByPoint($template['stadistics']["points"]) ;?></dd></div>
<div><dt>Prioridad más alta<dt><dd><?php echo $main_word['highest_priority'];?></dd></div>
<div><dt>¿En Anki?</dt><dd><?php echo ($main_word['in_anki']) ? 'si' : 'no';?></dd></div>
<div><dt>Cantidad guardada</dt><dd><?php echo $main_word['word_count'];?></dd></div>
<div><dt>Porcentaje aparición (etiquetas)</dt><dd><?php 
 // a count($template['tags']) se le resta 1 porque tambien esta la de "graduada/estudio" y esa no la contamos

echo \WordAdmin\Loader\apparitionPorcentage($main_word['word_count'], (count($template['tags'])) - 1);?> (etiquetas: <?php echo count($template['tags']) - 1;?>) </dd></div>
<div><dt>Porcentaje aparición (etiquetas historial)</dt><dd> 
<ul>
<?php

foreach($template['tagLog_count'] as $tagLog_id => $tagCount) {

    if ($tagCount == 0 ) {
        continue;
    }

    $tmp_cantidadPorcetaje = "";
    if ($tagCount > 0) {
        $tmp_cantidadPorcetaje = \WordAdmin\Loader\apparitionPorcentage($tagCount, $main_word['word_count']);
    }

    $tag_name = "sin etiqueta";
    foreach ($template['wordtags'] as $wt) {
        if ($wt['id'] != $tagLog_id ) {
            continue;
        }
        $tag_name = $wt['name'];
        break;
    }   

    echo "<li>" . $tag_name . ": {$tmp_cantidadPorcetaje} (". $tagCount . ")</li>";
    }
?>
</ul>


</dd></div>
</dl>



<hr>

<form action="?view=<?php echo $main_word['id'];?>" method="post">
<input type="hidden" name="tagword_id" value="<?php echo $main_word['id'];?>">
<input type="hidden" name="mainword_id" value="<?php echo $main_word['id'];?>">

<?php if ($main_word['deleted'] == 1 ): ?>
    <div class="admonition admonition-warning">
        <p>Esta palabra fue eliminada, se deja para historial. Si realmente se quiere eliminar (hard-delete) o revivirla, hacerlo por base de datos</p>
    </div>
<?php else: ?>
    <input type="submit" name="delete_word" value="Eliminar" class="delete">
<?php endif; ?>







<p>Listas: <input type="submit" name="save_word_tags" value="Actualizar listas"> </p>


<fieldset class="tags_columns">
<?php foreach ($template['tags'] as $tag):
    
    // TODO: no hardocdear el tema de la lista para estudiar/graduar
    if ($tag['id'] != 1) {
        continue;
    }
    
    $checked = '';

   
    foreach($template['wordtags'] as $wt) {
        if ( $wt['id'] == $tag['id']) {
            $checked = ' checked ';
            break;
        }
    }

    if ($template['isWordForStudying']) {
        $checked = ' checked ';
    }

    ?>
    <span class="radio-group">
        <input type="checkbox" <?php echo $checked;?> name="wordTag[]" id="tag_<?php echo $tag['id'];?>" value="<?php echo $tag['id'];?>" >
        <label for="tag_<?php echo $tag['id'];?>"><?php echo $tag['name'];?></label><br>
    </span>


<?php endforeach;?>
</fieldset>

</form>


<h4>Historial</h4>


<div class="historial-logs zebra">
<?php foreach ($template['word'] as $item):?>

<div class="historial-log">
    <dl class="inline">
        <div><dt>Prioridad</dt><dd><?php echo $item['log_priority'];?> <a href="?log=<?php echo $item['log_id'];?>&action=edit">Editar</a></dd></div>
        <div><dt>Etiqueta</dt><dd><?php echo $tags_clean[$item['source_tag']];?></dd></div>
        <div><dt>Origen</dt><dd><a href="/added?src=<?php echo rawurlencode($item['log_source']);?>"><?php echo $item['log_source'];?></a></dd></div>
        <div><dt>Fecha</dt><dd><?php echo $item['log_created'];?></dd></div>
        <div><dt>Ejemplos</dt><dd><?php echo $item['log_examples'];?></dd></div>
    
    </dl>


</div>


<?php endforeach; ?>

</div>



<?php /*


<table class="zebra">

    <colgroup>
        <col style="width: 80px;">
        <col style="width: 120px;">
        <col style="width: 300px;">
        <col style="width: 80px;">
        <col>
        <col style="width: 80px;">
    </colgroup>


    <tr>
        <th>Prioridad</th>
        <th>Etiqueta</th>
        <th style="text-align:left;">Origen</th>
        <th>Fecha</th>
        <th style="text-align:left;">Ejemplos</th>
        <th>Acción</th>
    </tr>

    <?php foreach ($template['word'] as $item):?>

        <tr>
            <td style="text-align:center;"><?php echo $item['log_priority'];?></td>
            <td><?php echo $tags_clean[$item['source_tag']];?></td>
            <td><?php echo $item['log_source'];?></td>
            <td><?php echo $item['log_created'];?></td>
            <td><?php echo $item['log_examples'];?></td>
            <td><a href="?log=<?php echo $item['log_id'];?>&action=edit">Editar</a></td>
        </tr>

    <?php endforeach; ?>

</table>

*/?>