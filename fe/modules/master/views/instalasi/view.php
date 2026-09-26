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
            <td><?=$model->getAttributeLabel('instalasi_nama');?></td>
            <td>
                <?=$model->instalasi_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('instalasi_namalainnya');?></td>
            <td>
                <?=$model->instalasi_namalainnya;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('instalasi_singkatan');?></td>
            <td>
                <?=$model->instalasi_singkatan;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('instalasi_lokasi');?></td>
            <td>
                <?=$model->instalasi_lokasi;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('instalasirujukaninternal');?></td>
            <td>
                <?=$model->instalasirujukaninternal;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('instalasi_adakamar');?></td>
            <td>
                <?=$model->instalasi_adakamar;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('instalasi_image');?></td>
            <td>
                <?=$model->instalasi_image;?>
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
