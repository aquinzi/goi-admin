
<?php 

require_once 'part-head.php';
?>

<style>
#wordList ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
#wordList ul li {
    margin: 2em 0;
    border-bottom: 1px solid darkcyan;
    padding: 1em 0;
}

#wordList ul li .word-actions {
    margin-top: 1em;
}

</style>


<?php

if ($template['step'] == "initial")  {
    require 'review-part-index.php';
}
if ($template['step'] == "queried")  {
    require 'review-part-list.php';
}
?>



<?php require_once 'part-foot.php';?>