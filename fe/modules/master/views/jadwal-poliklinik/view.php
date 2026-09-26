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
            <td><?=$model->getAttributeLabel('hari');?></td>
            <td>
                <?=$model->hari;?>
            </td>
        </tr>
         <tr>
            <td><?=$model->getAttributeLabel('jam_mulai');?></td>
            <td>
                <?=$model->jam_mulai;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('jam_tutup');?></td>
            <td>
                <?=$model->jam_tutup;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('waktu_pelayanan');?></td>
            <td>
                <?=$model->waktu_pelayanan;?>
            </td>
        </tr>    
        <tr>
            <td><?=$model->getAttributeLabel('maxantiran_poli');?></td>
            <td>
                <?=$model->maxantiran_poli;?>
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
