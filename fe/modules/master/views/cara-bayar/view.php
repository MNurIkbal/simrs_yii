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
            <td><?=$model->getAttributeLabel('carabayar_nama');?></td>
            <td>
                <?=$model->carabayar_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('carabayar_namalainnya');?></td>
            <td>
                <?=$model->carabayar_namalainnya;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('');?></td>
            <td>
                <?=$model->metode_pembayaran;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('carabayar_loket');?></td>
            <td>
                <?=$model->carabayar_loket;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('carabayar_singkatan');?></td>
            <td>
                <?=$model->carabayar_singkatan;?>
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
