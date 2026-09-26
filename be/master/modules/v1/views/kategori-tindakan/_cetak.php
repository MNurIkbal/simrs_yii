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
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table border="1" class="tbl-bordered">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Kode Kategori");?></th>
                        <th><?=\Yii::t("app", "Nama Kategori");?></th>
                        <th><?=\Yii::t("app", "Nama Lainnya");?></th>
                        <th><?=\Yii::t("app", "Status Aktif");?></th>
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
                            <td><?= $value['kategori_kode'] ?></td>
                            <td><?= $value['kategoritindakan_nama'] ?></td>
                            <td><?= $value['kategoritindakan_namalainnya'] ?></td>
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