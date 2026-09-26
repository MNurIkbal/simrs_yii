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
            <td><?=$model->getAttributeLabel('kelompokremunerasi_urutan');?></td>
            <td>
                <?=$model->kelompokremunerasi_urutan;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('kelompokremunerasi_kode');?></td>
            <td>
                <?=$model->kelompokremunerasi_kode;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('kelompokremunerasi_nama');?></td>
            <td>
                <?=$model->kelompokremunerasi_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('kelompokremunerasi_desc');?></td>
            <td>
                <?=$model->kelompokremunerasi_desc;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('kelompokremunerasi_singkatan');?></td>
            <td>
                <?=$model->kelompokremunerasi_singkatan;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('kelompokremunerasi_rate');?></td>
            <td>
                <?=$model->kelompokremunerasi_rate;?>
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
