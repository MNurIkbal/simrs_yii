<?php

/**
 * @Author: Sigit
 * @Date:   2019-03-25 15:49:59
 */

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

<table class="tbl-bordered" style="width:100%">
    <thead>
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th><?= \Yii::t("app", "Kode Kegiatan") ?></th>
            <th><?= \Yii::t("app", "Nama Kegiatan") ?></th>
            <th><?= \Yii::t("app", "Nama Lainnya") ?></th>
            <th><?= \Yii::t("app", "Keterangan") ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            foreach ($data as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $value['jeniskegiatantindakan_kode'] ?></td>
                <td><?= $value['jeniskegiatantindakan_nama'] ?></td>
                <td><?= $value['jeniskegiatan_namalainnya'] ?></td>
                <td><?= $value['jeniskegiatan_keterangan'] ?></td>
            </tr>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
</table>