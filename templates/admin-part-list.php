<p><a href="/admin/liststudy">Palabras que se tendrían que estudiar</a>, <a href="/admin/?ankied=t">Están en anki pero siguen acá</a>,  <a href="/admin/?softdeleted=t">Soft-deleted</a></p>
<p>Favoritos: <a href="https://goi.aquinzi.com/adminer/adminer.php?username=aquinzi_goiadm&db=aquinzi_goi_admin">Ver adminer</a></p>

<hr>

<?php if (\array_key_exists('deleted', $template) && $template['deleted']):?>

<p><strong>Palabra eliminada</strong></p>

<?php endif;?>

<?php if ($template['step'] != "chk-source") : ?>
    <p>Etiquetas/listas: <?php echo count($template['tags']);?></p>
<?php else: ?>
    <p>Total: <?php echo count($template['words']);?></p>
<?php endif;?>



<section id="vocablist">
    <table class="zebra">

        <thead>
            <tr> 
                <th>Palabra</th>
                <th>Lectura</th>
                <th>Cantidad</th>
                <th>Prioridad</th>
                <!--<th>Etiquetas</th>-->
                <th>Accion</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach($template['words'] as $word): ?>
            <tr> 
                <td><span <?php echo ($word['in_anki']) ? 'class="check_in_anki in_anki--1"' : '';?>> <?php echo $word['word'];?></span></td>  
                <td><?php echo $word['reading'];?></td>
                <td><?php echo $word['word_count'];?></td>
                <td><?php echo $word['highest_priority'];?></td>
               <!-- <td><?php echo $word['tags'];?></td>-->
                <td><a href="/admin/?view=<?php echo $word['id'];?>">ver</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>


    </table>
</section>






<?php 

if ($template['step'] != "chk-source") :


echo \WordAdmin\Loader\templatePartLoadLib('jquery-local');
echo \WordAdmin\Loader\templatePartLoadLib('css-datatables-local');
echo \WordAdmin\Loader\templatePartLoadLib('js-datatables-local');
?>

<script type="text/javascript">

$(document).ready( function () {
    $('#vocablist table').DataTable({
        "lengthMenu": [[50, 100, -1], [50, 100, "All"]]
    });
} );
</script>


<?php endif;?>
