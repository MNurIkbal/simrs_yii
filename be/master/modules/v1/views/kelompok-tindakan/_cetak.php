<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<center><h5 class="modal-title"><?=$title;?></h5></center> 
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table class="tbl-bordered" width="100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Kode Kelompok");?></th>
                        <th><?=\Yii::t("app", "Nama Kelompok");?></th>
                        <th><?=\Yii::t("app", "Nama Lainnya");?></th>
                        <th><?=\Yii::t("app", "Cyto");?></th>
                        <th><?=\Yii::t("app", "Diskon");?></th>
                        <th><?=\Yii::t("app", "Status");?></th>
                        <th><?=\Yii::t("app", "Catatan");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['kelompoktindakan_kode'] ?></td>
                            <td><?= $value['kelompoktindakan_nama'] ?></td>
                            <td><?= $value['kelompoktindakan_namalainnya'] ?></td>
                            <td><?= $value['kelompoktindakan_persencyto'] ?></td>
                            <td><?= $value['kelompoktindakan_persendiskon'] ?></td>
                            <td><?= ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif' ?></td>
                            <td><?= $value['catatan'] ?></td>
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