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
            <td><?=$model->getAttributeLabel('propinsi_nama');?></td>
            <td>
                <?=$model->propinsi_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('propinsi_namalainnya');?></td>
            <td>
                <?=$model->propinsi_namalainnya;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('kode_propinsi');?></td>
            <td>
                <?=$model->kode_propinsi;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('longitude');?></td>
            <td>
                <?=$model->longitude;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('latitude');?></td>
            <td>
                <?=$model->latitude;?>
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
