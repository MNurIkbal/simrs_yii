<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-06 14:19:44
 */

use Doco\components\DocoHelpers;
?>

<table id="tabel-tindakan" border="1" cellpadding="0" cellspacing="0" style="width:100%;">
    <thead>
        <tr class="bg-inverse">
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Tanggal Tindakan') ?></th>
            <th><?= Yii::t('app', 'Nama Tindakan / Paket') ?></th>
            <th><?= Yii::t('app', 'Dokter Pemeriksa') ?></th>
            <th><?= Yii::t('app', 'Dokter Delegasi') ?></th>
            <th><?= Yii::t('app', 'Perawat 1') ?></th>
            <th><?= Yii::t('app', 'Perawat 2') ?></th>
            <th><?= Yii::t('app', 'Qty') ?></th>
            <th><?= Yii::t('app', 'Implementasi') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $no = 1;
        $temp_instruksi_id = [];
        foreach ($data_tindakan as $value) {
            if ($value['tipe_pelayanan'] == 'TINDAKAN') {
    ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_tindakan'])), false, true) ?></td>
            <td><?= $value['tindakan_obat'] ?></td>
            <td><?= $value['dokter_pemeriksa'] ?></td>
            <td><?= $value['dokter_delegasi'] ?></td>
            <td><?= $value['perawat_1'] ?></td>
            <td><?= $value['perawat_2'] ?></td>
            <td><?= $value['qty'] ?></td>
            <td><?= $value['stat_implementasi'] ?></td>
        </tr>
    <?php
            } else {
                if (in_array($value['instruksitindakan_id'], $temp_instruksi_id)) {
    ?>
        <tr>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"><?= '- '.$value['daftartindakan_nama'] ?></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
        </tr>
    <?php
                } else {
                    $temp_instruksi_id[] = $value['instruksitindakan_id'];
    ?>
        <tr>
            <td style="border-bottom: none;"><?= $no ?></td>
            <td style="border-bottom: none;"><?= DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_tindakan'])), false, true) ?></td>
            <td style="border-bottom: none;"><?= $value['tindakan_obat'].' :' ?></td>
            <td style="border-bottom: none;"><?= $value['dokter_pemeriksa'] ?></td>
            <td style="border-bottom: none;"><?= $value['dokter_delegasi'] ?></td>
            <td style="border-bottom: none;"><?= $value['perawat_1'] ?></td>
            <td style="border-bottom: none;"><?= $value['perawat_2'] ?></td>
            <td style="border-bottom: none;"><?= $value['qty'] ?></td>
            <td style="border-bottom: none;"><?= $value['stat_implementasi'] ?></td>
        </tr>
        <tr>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"><?= '- '.$value['daftartindakan_nama'] ?></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
            <td style="border-top: none; border-bottom: none;"></td>
        </tr>
    <?php                
                }
            }
            $no++;
        }
    ?>
    </tbody>
</table>