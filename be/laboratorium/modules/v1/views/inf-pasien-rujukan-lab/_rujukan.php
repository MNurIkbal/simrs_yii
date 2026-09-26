<?php 
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers; ?>

<style>
   .tbl-header {
        border: 1px solid black;
    }

    .tbl-header td {
        padding: 3px;
    }
    
    .tbl-footer {
        border-collapse: collapse;
        font-size: 12px;
    }
   .tbl-bordered {
      border-collapse: collapse;
      font-size: 12px;
   }

   .tbl-bordered thead th {
      border: 1px solid black;
      padding: 5px;
   }
   .tbl-bordered tbody td {
      border: 1px solid black;
      padding: 5px;
   }

   .tbl-bordered tfoot td {
      padding: 3px;
   }

   .footer  {
      padding: 3px;
   }

   .number {
      text-align: right
   }
   .center {
      text-align: center
   }
   .box {
   width: 200px;
   border: 1px solid;
   padding: 10px;
   margin: 0;
   }
   .tbl-summary thead tr th:nth-child(2) {
      border-bottom: 1px solid !important;
   }
   .tbl-summary tbody tr:nth-child(4) th {
      border-bottom: 1px solid !important;
   }
   .tbl-summary tbody tr:nth-child(4) td {
      border-bottom: 1px solid !important;
   }
   .tbl-summary tbody tr:nth-child(6) td {
      border-bottom: 1px solid !important;
   }
   .header-box {
      background-color: #ffffff;
      filter: alpha(opacity=40);
      opacity: 0.95;
      border:1px solid;
   }
   .border-bottom {
      border-bottom: 1px solid;
   }
</style>

<table width="100%" class="tbl-bordered">
   <thead>
      <tr>
         <th style="width:5%">No.</th>
         <th style="width:55%">Jenis Pemeriksaan</th>
         <th style="width:20%">Diagnosa</th>
         <th style="width:20%">Dokter Pengirim</th>
      </tr>
   </thead>
   <tbody>
      <?php
         $no = 1;
         foreach ($detail as $value) : 
            $pemeriksaan = ArrayHelper::getValue($value, 'pemeriksaanlab_nama');
            $diagnosa = ArrayHelper::getValue($value, 'diagnosa_dirujuk');
            $diagnosa = !empty($diagnosa) ? json_decode($diagnosa, true) : [];
            $diagnosa = !empty($diagnosa['nama']) ? $diagnosa['nama'] : '';
            $dokter = ArrayHelper::getValue($value, 'dokter_perujuk_nama');
         ?>
         <tr>
            <td style="width:2px;text-align:center"> <?= $no ?></td>
            <td><?= $pemeriksaan ?></td>
            <td><?= $diagnosa ?></td>
            <td><?= $dokter ?></td>
         </tr>
      <?php $no++; endforeach; ?>
   </tbody>
</table>
