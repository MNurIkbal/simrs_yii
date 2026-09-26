<?php
use Doco\components\DocoConstants;

?>

<table style="width:100%" border="1" cellpadding="5" cellspacing="1">
    <thead>
        <tr>
            <td>No</td>
            <th><?=\Yii::t("app", "No Pendaftaran");?></th>
            <th><?=\Yii::t("app", "Tanggal Pendaftaran");?></th>
            <th><?=\Yii::t("app", "Nama Pasien");?></th>
            <th><?=\Yii::t("app", "No Rekam Medik");?></th>
            <th><?=\Yii::t("app", "Jenis Kelamin");?></th>
            <th><?=\Yii::t("app", "Tanggal Lahir");?></th>
            <th><?=\Yii::t("app", "Umur");?></th>
            <th><?=\Yii::t("app", "Kelas Pelayanan");?></th>
            <th><?=\Yii::t("app", "Cara Bayar");?></th>
            <th><?=\Yii::t("app", "Penjamin");?></th>
            <th><?=\Yii::t("app", "Tanggal Operasi");?></th>
            <th><?=\Yii::t("app", "Ruangan Perujuk");?></th>
            <th><?=\Yii::t("app", "Nama Tindakan");?></th>
            <th><?=\Yii::t("app", "Nama Operasi");?></th>
            <th><?=\Yii::t("app", "Qty");?></th>
            <th><?=\Yii::t("app", "Cyto");?></th>
            <th><?=\Yii::t("app", "Penyulit");?></th>
            <th><?=\Yii::t("app", "Dokter");?></th>
            <th><?=\Yii::t("app", "Posisi");?></th>
            <th><?=\Yii::t("app", "Subtotal");?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($data as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '' ?></td>
                <td><?= isset($value['tanggal_pendaftaran']) ? date('d M Y', strtotime($value['tanggal_pendaftaran'])) : '' ?></td>
                <td><?= isset($value['nama_pasien']) ? $value['nama_pasien'] : '' ?></td>
                <td><?= isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '' ?></td>
                <td><?= isset($value['jenis_kelamin']) ? $value['jenis_kelamin'] : '' ?></td>
                <td><?= isset($value['tanggal_lahir']) ? date('d M Y', strtotime($value['tanggal_lahir'])) : '' ?></td>
                <td><?= isset($value['umur']) ? $value['umur'] : '' ?></td>
                <td><?= isset($value['kelas_pelayanan']) ? $value['kelas_pelayanan'] : '' ?></td>
                <td><?= isset($value['cara_bayar']) ? $value['cara_bayar'] : '' ?></td>
                <td><?= isset($value['penjamin']) ? $value['penjamin'] : '' ?></td>
                <td><?= isset($value['tanggal_operasi']) ? date('d M Y', strtotime($value['tanggal_operasi'])) : '' ?></td>
                <td><?= isset($value['ruang_perujuk']) ? $value['ruang_perujuk'] : '' ?></td>
                <td><?= isset($value['nama_tindakan']) ? $value['nama_tindakan'] : '' ?></td>
                <td><?= isset($value['nama_operasi']) ? $value['nama_operasi'] : '' ?></td>
                <td><?= isset($value['qty']) ? $value['qty'] : '' ?></td>
                <td><?= isset($value['cyto']) ? $value['cyto'] : '' ?></td>
                <td><?= isset($value['penyulit']) ? $value['penyulit'] : '' ?></td>
                <td><?= isset($value['dokter']) ? $value['dokter'] : '' ?></td>
                <td><?= isset($value['posisi']) ? $value['posisi'] : '' ?></td>
                <td><?= isset($value['subtotal']) ? $value['subtotal'] : '' ?></td>
        <?php
            $no++;
            endforeach;
        ?>
    </tbody>
</table>