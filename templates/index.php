<?php require_once 'part-head.php';?>

<?php
if ($template['step'] == "finished")  {
    echo '<p>Guardados!</p>';
    $template['step'] = "collect";
}
if ($template['step'] == "finished-add")  {
    echo '<p>Guardados! <a href="/added?src=' . rawurlencode($template['formValues']['source']) .'">Ver listado por el origen</a></p>';
    $template['step'] = "add";
}


if ($template['step'] == "add")   {
    require 'index-part-add.php';
}


?>

<?php require_once 'part-foot.php';?>