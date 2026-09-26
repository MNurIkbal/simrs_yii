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
            <td><?=$model->getAttributeLabel('pendkualifikasi_kode');?></td>
            <td>
                <?=$model->pendkualifikasi_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('pendkualifikasi_nama');?></td>
            <td>
                <?=$model->pendkualifikasi_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('pendkualifikasi_namalainnya');?></td>
            <td>
                <?=$model->pendkualifikasi_namalainnya;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('pendkualifikasi_keterangan');?></td>
            <td>
                <?=$model->pendkualifikasi_keterangan;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('jmlkeblaki');?></td>
            <td>
                <?=$model->jmlkeblaki;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('jmlkebperempuan');?></td>
            <td>
                <?=$model->jmlkebperempuan;?>
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
