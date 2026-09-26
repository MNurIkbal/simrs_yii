<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12" style="min-height: 350px">
            <?php 
            if ( in_array($type, ['jpg', 'png']) ) {
                echo DocoHelpers::previewImg($path);
            } else {
                echo "<iframe src='".$path."' width='90%'></iframe> ";
            }
            ?>
        </div>
    </div>
</div>