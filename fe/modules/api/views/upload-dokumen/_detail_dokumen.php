<?php

use app\components\DocoHelpers;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12" style="min-height: 350px">
            <?php
            if (in_array($type, $fileAction['detail'])) {
                echo DocoHelpers::previewImgFtp($konfigFtp, $path) ? DocoHelpers::previewImgFtp($konfigFtp, $path) : DocoHelpers::previewImg($pathOld);
            } else {
                echo "<iframe src='" . $path . "' width='90%'></iframe> ";
            }
            ?>
        </div>
    </div>
</div>
