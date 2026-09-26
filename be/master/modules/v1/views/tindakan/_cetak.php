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
<table class="tbl-bordered" style="width:100%">
    <thead>
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th><?=\Yii::t("app", "Kode Tindakan");?></th>
            <th><?=\Yii::t("app", "Nama Tindakan");?></th>
            <th><?=\Yii::t("app", "Nama Lainnya");?></th>
            <th><?=\Yii::t("app", "Nama Kategori");?></th>
            <th><?=\Yii::t("app", "Nama Kelompok");?></th>
            <th><?=\Yii::t("app", "Nama Kegiatan");?></th>
            <th><?=\Yii::t("app", "Group INA CBGS");?></th>
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
                <td><?= $value['daftartindakan_kode'] ?></td>
                <td><?= $value['daftartindakan_nama'] ?></td>
                <td><?= $value['daftartindakan_namalainnya'] ?></td>
                <td><?= $value['kategoritindakan']['kategoritindakan_nama'] ?></td>
                <td><?= $value['kelompoktindakan']['kelompoktindakan_nama'] ?></td>
                <td><?= $value['jeniskegiatantindakan']['jeniskegiatantindakan_nama'] ?></td>
                <td><?= ($value['groupinacbg']) ? $value['groupinacbg']['groupinacbg_nama'] : '' ?></td>
                <td><?= ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif' ?></td>
                <td><?= $value['catatan'] ?></td>
            </tr>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
</table>