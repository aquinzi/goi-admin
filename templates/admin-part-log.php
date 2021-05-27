<?php 

if (!$template['log']) {
    die("log no encontrado");
}

$template['log']['priority'] = (int)$template['log']['priority'];
?>


<h2>Editar movimiento historial</h2>

<dl class="inline">
<div><dt>Movimiento # <dt><dd><?php echo $template['log']['id'];?></dd></div>
<div><dt>Palabra <dt><dd><?php echo $template['log']['word'];?></dd></div>

</dl>


<p><a href="/admin/?view=<?php echo $template['log']['word_id'];?>">Cancelar</a></p>

<form action="?log=<?php echo $template['log']['id'];?>&action=save" method="post">
<input type="hidden" name="log_id" value="<?php echo $template['log']['id'];?>">
<input type="hidden" name="word_id" value="<?php echo $template['log']['word_id'];?>">
<p>
    <label for="log_source">Origen:</label>
    <input type="text" id="log_source" name="log_source" value="<?php echo $template['log']['source'];?>">
</p>

<p> 
    <label for="word_tag">Etiqueta:</label>
    <select name="log_tag" id="log_tag" required>
        <?php foreach($template['tags'] as $tag): ?>
            <option 
                <?php echo ($template['log']['source_tag'] == $tag['id']) ? 'selected ' : '';?>
                value="<?php echo $tag['id'];?>">
                <?php echo $tag['name'];?>
            </option>
        <?php endforeach; ?>
    </select>
</p>

<p>
    <label for="log_example">Ejemplo:</label>
    <textarea id="log_example" name="log_example" cols="80" rows="5"><?php echo $template['log']['examples'];?></textarea>
</p>


<p>Prioridad: 

<?php for ($i=1; $i < 4; $i++):?>

<span class="radio-pair">
    <input type="radio" id="log_prioridad_lbl_<?php echo $i;?>" name="log_prioridad" value="<?php echo $i;?>" <?php echo ($template['log']['priority'] == $i) ? ' checked':'';?>>
    <label for="log_prioridad_lbl_<?php echo $i;?>"><?php echo $i;?></label>
</span>



<?php endfor; ?>
</p>


<p>
    <label for="log_date">Fecha creacion:</label>
	<input type="date"  name="log_date" id="log_date" value="<?php echo explode(" ", $template['log']['created'])[0];?>">
</p>


<p>
<input type="submit" name="savelog" value="Guardar"><br><br><br>
<input type="submit" name="dellog" value="Eliminar" class="delete">
</p>






</form>



