<?php

/**
 * @Author: afil
 * @Date:   2018-01-03 14:05:59
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-03 14:37:08
 */
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <table class="table table-bordered table-condensed table-striped table-hover">
        <tr>
            <td><?=$model->getAttributeLabel('jeniskasuspenyakit_nama');?></td>
            <td>
                <?=$model->jeniskasuspenyakit_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('jeniskasuspenyakit_namalainnya');?></td>
            <td>
                <?=$model->jeniskasuspenyakit_namalainnya;?>
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <?=Html::button('Kembali',[
        'class' => 'btn btn-default btn-md',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
