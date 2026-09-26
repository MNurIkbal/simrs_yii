<?php
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
            <td><?=$model->getAttributeLabel('namaperujuk');?></td>
            <td>
                <?=$model->namaperujuk;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('spesialis');?></td>
            <td>
                <?=$model->spesialis;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('alamatlengkap');?></td>
            <td>
                <?=$model->alamatlengkap;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('notelp');?></td>
            <td>
                <?=$model->notelp;?>
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
