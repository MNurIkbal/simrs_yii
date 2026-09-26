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
                        <th><?=\Yii::t("app", "Golongan Umur");?></th>
                        <th><?=\Yii::t("app", "Usia");?></th>
                        <th><?=\Yii::t("app", "Umur Minimal");?></th>
                        <th><?=\Yii::t("app", "Umur Maksimal");?></th>
                        <th><?=\Yii::t("app", "Status");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['golonganumur_nama'] ?></td>
                            <td><?= $value['golonganumur_namalainnya'] ?></td>
                            <td><?= $value['golonganumur_minimal'] ?></td>
                            <td><?= $value['golonganumur_maksimal'] ?></td>
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