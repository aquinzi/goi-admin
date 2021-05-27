<?php 

//NOTE: no hardcodear el tema de los listados
// NOTE: se pone manualmente porque no tiene sentido revisar palabras que estan en dos o mas listados al mismo tiempo


?>

<form id="vocabListReview" action="" method="get">
	<p>Seleccionar listado</p>
    
    <span class="radio-pair">
        <input type="radio" id="filter_tag_1" name="tag" value="1"> <label for="filter_tag_1"><?php echo $template['tags'][0]['name'];?></label>
    </span><br>

    <span class="radio-pair">
        <input type="radio" id="filter_tag_2" name="tag" value="2"> <label for="filter_tag_2">Las otras</label>
    </span><br>
        
    <?php /*foreach($template['tags'] as $tag): ?>
        <span class="radio-pair">
            <input type="radio" id="filter_tag_<?php echo $tag['id'];?>" name="tag" value="<?php echo $tag['id'];?>"> <label for="filter_tag_<?php echo $tag['id'];?>"><?php echo $tag['name'];?></label>
        </span><br>

    <?php endforeach; */?>
    

    <input type="submit" name="filter" value="Filtrar">
</form>


