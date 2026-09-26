<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 16:17:17
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-05-23 13:18:04
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<table id="table-riwayat" class="table table-striped table-condensed table-hover">
    <thead class="text-center">
        <tr class="bg-inverse">
            <th rowspan="2"><?= Yii::t('fe', 'Tanggal kunjungan / no. pendaftaran') ?></th>
            <th rowspan="2"><?= Yii::t('fe', 'Ruangan / Kamar') ?></th>
            <th rowspan="2"><?= Yii::t('fe', 'Dokter DPJP') ?></th>
            <th rowspan="2"><?= Yii::t('fe', 'Pelayanan pasien') ?></th>
            <th rowspan="2"><?= Yii::t('fe', 'Penunjang') ?></th>
            <th rowspan="2"><?= Yii::t('fe', 'Cara keluar') ?></th>
        </tr>
    </thead>
    <tbody> 
    </tbody>
</table>

<?php
    $norm = $data_pasien['no_rekam_medik'];
    $this->registerJs("
        var tabel = $('#table-riwayat').docoTabel({
            filter: true,
            sorting: [[0, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            fixedColumns: {
                leftColumns: 1
            },
            ajax: baseUrl+'rajal/riwayat-pasien/get-data?norm='+'$norm',
            columns: [
                {title: '".(\Yii::t('fe', 'Jenis Layar Antrian'))."', data: 'jenisantrian_id', visible: false},
                {title: '".(\Yii::t('fe', 'Jenis Layar Antrian'))."', data: 'lookup_m.lookup_name', searchable: false},
                {title: '".(\Yii::t('fe', 'Nama Layar Antrian'))."', data: 'layarantrian_nama'},
                {title: '".(\Yii::t('fe', 'Latar Belakang'))."',  data: 'layarantrian_latarbelakang'},
                {title: '".(\Yii::t('fe', 'Status'))."',  data: 'is_active'},
            ],
        });
    ");
?>