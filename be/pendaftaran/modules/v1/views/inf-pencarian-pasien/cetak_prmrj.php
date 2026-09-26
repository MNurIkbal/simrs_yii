<?php

use Doco\components\DocoHelpers;
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
<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th>No.</th>
            <th><?=\Yii::t("app", "Tanggal Berkunjung");?></th>
            <th><?=\Yii::t("app", "Poli/IGD");?></th>
            <th><?=\Yii::t("app", "Nama Dokter");?></th>
            <th><?=\Yii::t("app", "Diagnosa");?></th>
            <th><?=\Yii::t("app", "Obat-obatan / Jenis Pemeriksaan");?></th>
            <th><?=\Yii::t("app", "Tindak Lanjut");?></th>
            <th><?=\Yii::t("app", "Anamnesa (S)");?></th>
            <th><?=\Yii::t("app", "Temuan Klinis (O)");?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        foreach($data as $value):
            $diagnosa = (array) json_decode($value['a_diag_utama']);
        ?>
        <tr>
            <td><?= $no; ?></td>
            <td><?= date('d M Y', strtotime($value['tgl_pendaftaran'])); ?></td>
            <td><?= $value['ruangan_nama']; ?></td>
            <td><?= $value['nama_dokter']; ?></td>
            <td><?= $diagnosa['text']; ?></td>
            <td><?= $value['obat_tindakan']; ?></td>
            <td><?= $value['tindak_lanjut']; ?></td>
            <td><?= $value['anamnesa']; ?></td>
            <td><?= $value['object']; ?></td>
        </tr>
        <?php 
        $no++;
        endforeach;
        ?>
    </tbody>
</table>