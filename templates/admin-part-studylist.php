<?php
global $_words_points_system;
$highlight_colors_points = $_words_points_system['highlight_colors'];

?>



<h2>Palabras que se tendrían que estudiar</h2>

<p>Palabras que tuvieron prioridad 1, o con conteo de mas de 4</p>


<table style="width: 300px;">
    <tr><th>color</th><th>mas de </th></tr>
    <?php foreach($highlight_colors_points as $amount => $class): ?>
        <tr><td><?php echo str_replace("paintme-", "", $class);?></td><td><?php echo $amount;?></td></tr>
    <?php endforeach;?>
</table>


<p>Ícono <span title="en lista estudio">🗒️</span> = en listado estudiar/graduadas</p>
<p>Palabras mostradas: <?php echo count($template['words']);?></p>
<section id="vocablist">
    <table class="zebra">


        <thead>
            <tr> 
                <th>Palabra</th>
                <th>Puntaje</th>
                <th>Aparición</th>
                <th>Prioridad</th>
                
            </tr>
        </thead>

        <tbody>
        <?php foreach($template['words'] as $word): 
            $tmp_priority = explode(",",$word['priority_history']);
            $tmp_priority = array_unique($tmp_priority);
            $tmp_priority = implode(", ", $tmp_priority);

            // current pri:1 + conteo > 5: paint red
            // conteo > 5: paint red

            $paint_me = "";

            /*
            if ($word['word_count'] > 5 ) {
                $paint_me = "paintme-red";
            }
            */

            foreach($highlight_colors_points as $amount => $class) {
                if ($word['points'] >= $amount ) {
                    $paint_me = $class;
                } 
            }

          
            
            // si esta en lista estudio / graduadas, se señalan
            $study_list = "";

            if ( $word['lista_estudio'] ) {
                $study_list = '<span title="en lista estudio">🗒️</span>';
            }


            ?>
            <tr class="<?php echo $paint_me;?>"> 
                <td><a href="/admin/?view=<?php echo $word['id'];?>" target="_blank"><span <?php echo ($word['in_anki']) ? 'class="check_in_anki in_anki--1"' : '';?>><?php echo $word['word'] . '</a>'. \WordAdmin\Loader\stringBetweenBrackets($word['reading']). $study_list ;?></span></td>  
                <td><?php echo $word['points'];?>
                 <!--(s/o <?php echo $word['points_sin_origen'];?> -   c/o <?php echo $word['points_con_origen'];?>)-->
                 </td>
                <td><?php echo $word['word_count'] . " (" . $word['tags_apparition_porcentage'] . ")";?></td>
                <td><?php echo $word['highest_priority'];?> (log: <?php echo $tmp_priority;?>)</td>
               
            </tr>
        <?php endforeach; ?>
        </tbody>


    </table>
</section>


<?php 
/*
echo \WordAdmin\Loader\templatePartLoadLib('jquery-local');
echo \WordAdmin\Loader\templatePartLoadLib('css-datatables-local');
echo \WordAdmin\Loader\templatePartLoadLib('js-datatables-local');


<script type="text/javascript">

$(document).ready( function () {
    
    $('#vocablist table').DataTable({
        "lengthMenu": [[50, 100, -1], [50, 100, "All"]],
        "ordering": false,
    });
    
} );
</script>

?>

