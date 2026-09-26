<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-05-02 16:56:00
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-18 14:23:54
 */
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
    h2.headertext {
      text-align: center;
    }
    td.bold {
        font-weight:bold
    }
</style>
<h2 class="headertext"><?=Yii::t('app', 'INFORMASI DATA PASIEN')?></h2>
<br>
<table width="100%" cellpadding="10" class="tabel" style="font-size: 12">
    <tbody>
        <?php if(array_key_exists('no_rekam_medik',$header)): ?>
            <tr>
                <td class="bold"><?= Yii::t('app', 'No Rekam Medik') ?></td>
                <td>:</td>
                <td><?=$header['no_rekam_medik']?></td>
            </tr>
        <?php endif; ?>
        <?php if (array_key_exists('nama_pasien', $header)) : ?>
            <tr>
                <td class="bold"><?= Yii::t('app', 'Nama pasien') ?></td>
                <td>:</td>
                <td><?=$header['nama_pasien']?></td>
            </tr>
        <?php endif; ?>
        <?php if (array_key_exists('alamat_pasien', $header)) : ?>
            <tr>
                <td class="bold"><?= Yii::t('app', 'Alamat') ?></td>
                <td>:</td>
                <td><?=$header['alamat_pasien']?></td>
            </tr>
        <?php endif; ?>
        <?php if (array_key_exists('jenis_kelamin', $header)) : ?>
            <tr>
                <td class="bold"><?= Yii::t('app', 'Jenis kelamin') ?></td>
                <td>:</td>
                <td><?=$header['jenis_kelamin']?></td>
            </tr>
        <?php endif; ?>
        <?php if (array_key_exists('propinsi_nama', $header)) : ?>
            <tr>
                <td class="bold"><?= Yii::t('app', 'Provinsi') ?></td>
                <td>:</td>
                <td><?=$header['propinsi_nama']?></td>
            </tr>
        <?php endif; ?>
        <?php if (array_key_exists('kabupaten_nama', $header)) : ?>
            <tr>
                <td class="bold"><?= Yii::t('app', 'Kabupaten') ?></td>
                <td>:</td>
                <td><?=$header['kabupaten_nama']?></td>
            </tr>
        <?php endif; ?>
        <?php if (array_key_exists('kecamatan_nama', $header)) : ?>
            <tr>
                <td class="bold"><?= Yii::t('app', 'Kecamatan') ?></td>
                <td>:</td>
                <td><?=$header['kecamatan_nama']?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<br>
<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Tgl. Rekam Medik') ?></th>
            <th><?= Yii::t('app', 'No Rekam Medik') ?></th>
            <th><?= Yii::t('app', 'Nama Pasien') ?></th>
            <th><?= Yii::t('app', 'Jenis Kelamin') ?></th>
            <th><?= Yii::t('app', 'Tgl. Lahir') ?></th>
            <th><?= Yii::t('app', 'NIK') ?></th>
            <th><?= Yii::t('app', 'Alamat') ?></th>
            <th><?= Yii::t('app', 'Propinsi') ?></th>
            <th><?= Yii::t('app', 'Kabupaten') ?></th>
            <th><?= Yii::t('app', 'Kecamatan') ?></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
        <?php 
        $no = 1;
        foreach($data as $value):
        ?>
        <tr>
            <td><?=$no?></td>
            <td><?=DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_rekam_medik'])), true, false)?></td>
            <td><?=$value['no_rekam_medik']?></td>
            <td><?=$value['nama_pasien']?></td>
            <td><?=$value['jenis_kelamin']?></td>
            <td><?=DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])), true, false)?></td>
            <td><?=$value['no_identitas_pasien']?></td>
            <td><?=$value['alamat_pasien']?></td>
            <td><?=$value['propinsi_nama']?></td>
            <td><?=$value['kabupaten_nama']?></td>
            <td><?=$value['kecamatan_nama']?></td>
        </tr>
        <?php 
        $no++;
        endforeach;
        ?>
    </tbody>
</table>