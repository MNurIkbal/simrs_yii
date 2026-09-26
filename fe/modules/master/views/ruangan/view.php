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
            <td><?=$model->getAttributeLabel('ruangan_nama');?></td>
            <td>
                <?=$model->ruangan_nama;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('ruangan_namalainnya');?></td>
            <td>
                <?=$model->ruangan_namalainnya;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('ruangan_jenispelayanan');?></td>
            <td>
                <?=$model->ruangan_jenispelayanan;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('ruangan_singkatan');?></td>
            <td>
                <?=$model->ruangan_singkatan;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('ruangan_fasilitas');?></td>
            <td>
                <?=$model->ruangan_fasilitas;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('ruangan_lokasi');?></td>
            <td>
                <?=$model->ruangan_lokasi;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('ruangan_image');?></td>
            <td>
                <?=$model->ruangan_image;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('ruangan_urutan');?></td>
            <td>
                <?=$model->ruangan_urutan;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('ruangan_filesuara');?></td>
            <td>
                <?=$model->ruangan_filesuara;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('estimasipelayanan');?></td>
            <td>
                <?=$model->estimasipelayanan;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('image_mobile');?></td>
            <td>
                <?=$model->image_mobile;?>
            </td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('kode_ruanganpoli');?></td>
            <td>
                <?=$model->kode_ruanganpoli;?>
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
