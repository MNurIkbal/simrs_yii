<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<center><h5 class="modal-title"><?=$title;?></h5></center> 
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="1">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Nama Layarantrian");?></th>
                        <th><?=\Yii::t("app", "Jenis Layarantrian");?></th>
                        <th><?=\Yii::t("app", "Latar Belakang Layarantrian");?></th>
                        <th><?=\Yii::t("app", "Status");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($detail as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['layarantrian_nama'] ?></td>
                            <td><?= $value['jenisantrian_id'] ?></td>
                            <td><?= $value['layarantrian_latarbelakang'] ?></td>
                            <td><?= ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif' ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
</div>