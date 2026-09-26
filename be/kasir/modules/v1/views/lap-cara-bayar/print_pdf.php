<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-10-29 14:00:57
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-29 14:07:04
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
</style>
<table width="100%" class="tbl-bordered">
  <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th width="1">No</th>
            <th><?= Yii::t('app', 'Tanggal Pembayaran') ?></th>
            <th><?= Yii::t('app', 'Nomor Pembayaran') ?></th>
            <th><?= Yii::t('app', 'Nomor Pendaftaran') ?></th>
            <th><?= Yii::t('app', 'Nomor Rekam Medik') ?></th>
            <th><?= Yii::t('app', 'Nama Pasien') ?></th>
            <th><?= Yii::t('app', 'Cara Bayar') ?></th>
            <th><?= Yii::t('app', 'Penjamin') ?></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
      <?php 
      $no = 1;
      foreach($data as $value):
      ?>
      <tr>
        <td><?=$no?></td>
        <td><?=date('d M Y H:i:s', strtotime($value['tgl_pembayaran']))?></td>
        <td><?=$value['no_pembayaran']?></td>
        <td><?=$value['no_pendaftaran']?></td>
        <td><?=$value['no_rekam_medik']?></td>
        <td><?=$value['nama_pasien']?></td>
        <td><?=$value['carabayar_nama']?></td>
        <td><?=$value['penjamin_nama']?></td>
      </tr>
      <?php 
      $no++;
      endforeach;
      ?>
    </tbody>  
</table>