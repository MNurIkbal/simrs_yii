<?php

/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-12-14 16:08:18
 * @Last Modified by:   iqbal
 * @Last Modified time: 2018-12-14 16:08:18
 */

?>

<?php 
if(count($data) > 0):
?>
    <ul>
<?php 
    foreach ($data as $key => $value):
?>
    <li><?= $value ?></li>
<?php
    endforeach;
?>
    </ul>
<?php
endif;
?>
