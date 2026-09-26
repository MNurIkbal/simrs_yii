<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-11-02 10:26:14
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-11-02 10:35:59
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
            <th><?= Yii::t('app', 'Kode Obat Alkes') ?></th>
            <th><?= Yii::t('app', 'Nama Obat Alkes') ?></th>
            <th><?= Yii::t('app', 'Qty') ?></th>
            <th><?= Yii::t('app', 'Satuan Besar') ?></th>
            <th><?= Yii::t('app', 'Qty') ?></th>
            <th><?= Yii::t('app', 'Satuan Kecil') ?></th>
            <th><?= Yii::t('app', 'Keterangan') ?></th>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
      <?php 
      $no = 1;
      foreach($data as $value):
      ?>
      <tr>
        <td><?=$no?></td>
        <td><?= !empty($value['obatalkes_kode']) ? $value['obatalkes_kode'] : "-" ?></td>
        <td><?=$value['obatalkes_nama']?></td>
        <td><?=$value['jumlah_input']?></td>
        <td><?=$value['satuanbesar_nama']?></td>
        <td><?=$value['qty_satuanpakai']?></td>
        <td><?=$value['satuankecil_nama']?></td>
        <td><?=$value['ket_obatpakai']?></td>
      </tr>
      <?php 
      $no++;
      endforeach;
      ?>
    </tbody>  
</table>