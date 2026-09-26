<?php
    $diagnosa = json_decode($selected,true);
?>
    <p style="text-align:center" style="width: 100%">
<?php
    foreach ($data as $val) :
?>
    <input type="checkbox"  <?= in_array($val->sebabdiagnosa_id, $diagnosa) 
        ? 'checked="checked"' : null ?>/> <?= $val->sebabdiagnosa_nama ?>
<?php
    endforeach;
?>
    </p>

