<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-10-29 14:29:41
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-19 23:45:50
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
            <th><?= Yii::t('app', 'Tanggal Closing') ?></th>
            <th><?= Yii::t('app', 'Nomor Closing') ?></th>
            <th><?= Yii::t('app', 'Pegawai Closing') ?></th>
            <th><?= Yii::t('app', 'Shift') ?></th>
            <th><?= Yii::t('app', 'Instalasi Akhir') ?></th>
            <th><?= Yii::t('app', 'Ruangan Akhir') ?></th>
            <th><?= Yii::t('app', 'Total Closing (Rp.)') ?></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
      <?php 
      $no = 1;
      $totalClosing = 0;
      foreach($data as $value):
      ?>
      <tr>
        <td><?=$no?></td>
        <td><?=date('d M Y', strtotime($value['tgl_closingkasir']))?></td>
        <td><?=$value['no_closingkasir']?></td>
        <td><?=$value['nama_pegawai']?></td>
        <td><?=$value['shift_nama']?></td>
        <td><?=$value['instalasi_nama']?></td>
        <td><?=$value['ruangan_nama']?></td>
        <td style="text-align: right;"><?=DocoHelpers::formatNumber($value['nilai_closingtransaksi'])?></td>
      </tr>
      <?php 
      $no++;
      $totalClosing += $value['nilai_closingtransaksi'];
      endforeach;
      ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">Total</td>
            <td><b><?=DocoHelpers::formatNumber($totalClosing)?></b></td>
        </tr>
    </tfoot>
</table>