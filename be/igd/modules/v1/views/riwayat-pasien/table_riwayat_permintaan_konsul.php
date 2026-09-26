<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-10 13:57:59
 */
?>

<table id="tabel-riwayat-visite-dokter" border="1" cellpadding="0" cellspacing="0" style="width:100%;">
    <thead>
        <tr class="bg-inverse">
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Tgl Permintaan Konsul') ?></th>
            <th><?= Yii::t('app', 'No RM / No Pendaftaran') ?></th>
            <th><?= Yii::t('app', 'Nama Pasien') ?></th>
            <th><?= Yii::t('app', 'Jenis Kelamin') ?></th>
            <th><?= Yii::t('app', 'Dokter DPJP') ?></th>
            <th><?= Yii::t('app', 'Cara Bayar / Penjamin') ?></th>
            <th><?= Yii::t('app', 'Hak Kelas / Kelas Saat Ini') ?></th>
            <th><?= Yii::t('app', 'Nama Ruangan / No. Kamar - No Bed') ?></th>
            <th><?= Yii::t('app', 'Jenis Konsul') ?></th>
            <th><?= Yii::t('app', 'Dokter Konsul') ?></th>
            <th><?= Yii::t('app', 'Status') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            foreach ($data_permintaan_konsul as $value) {
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $value['tanggal_permintaan'].'<br>'.$value['waktu'] ?></td>
                <td><?= $value['no_rekam_medik'].' / '.$value['no_pendaftaran'] ?></td>
                <td><?= $value['nama_pasien'] ?></td>
                <td><?= $value['jenis_kelamin'] ?></td>
                <td><?= $value['dok_dpjp'] ?></td>
                <td><?= $value['carabayar_nama'].' / '.$value['penjamin_nama'] ?></td>
                <td><?= $value['kls_hak'].' / '.$value['kls_rawat'] ?></td>
                <td><?= $value['ruangan_nama'].' / '.$value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'] ?></td>
                <td><?= $value['jenis_konsul_nama'] ?></td>
                <td><?= $value['dok_konsul'] ?></td>
                <td><?= $value['status_konsul_nama'] ?></td>
            </tr>
        <?php
                $no++;
            }
        ?>
    </tbody>
</table>