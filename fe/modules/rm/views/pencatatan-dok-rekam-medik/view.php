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
            <td><?=$model->getAttributeLabel('row_id');?></td>
            <td>
                <?=$model->pasien_m['no_rekam_medik'];?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('nomorprimer');?></td>
            <td>
                <?=$model->nomorprimer;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('nomorsekunder');?></td>
            <td>
                <?=$model->nomorsekunder;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('nomortertier');?></td>
            <td>
                <?=$model->nomortertier;?>
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
