<?php 

require_once 'part-head.php';


if (\in_array($template['step'], ['initial', 'view-ankied', 'view-softdeleted', 'chk-source'])){
    require 'admin-part-list.php';
}

if ($template['step'] == "view")  {
    require 'admin-part-view.php';
}

if ($template['step'] == "studylist")  {
    require 'admin-part-studylist.php';
}

if ($template['step'] == 'edit-log')  {
    require 'admin-part-log.php';
}

if ($template['step'] == 'save-log')  {
    require 'admin-part-view.php';
}

?>


<?php require_once 'part-foot.php';?>