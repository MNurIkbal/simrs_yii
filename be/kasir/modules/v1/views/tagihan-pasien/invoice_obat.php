<?php 
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
?>
<style>
    .tbl-header {
        border: 1px solid black;
    }

    .tbl-header td {
        padding: 3px;
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
    /*.tbl-summary thead tr th:nth-child(2) {
        border-bottom: 1px solid !important;
    }*/
    /*.tbl-summary tbody tr:nth-child(4) th {
        border-bottom: 1px solid !important;
    }*/
    .tbl-summary tbody tr:nth-child(3) td {
        border-bottom: 1px solid !important;
    }
    /*.tbl-summary tbody tr:nth-child(6) td {
        border-bottom: 1px solid !important;
    }*/
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

<?php 
$diskonPasien = ArrayHelper::getValue($dataDiskon, 'diskonPasien', 0); 
$diskonPayer = ArrayHelper::getValue($dataDiskon, 'diskonPayer', 0);
$payerAmount = $dijamin + $totalPembulatanPayer;
$pasienAmount = $totalDitagihkan;
?>

<table width="100%" class="tbl-bordered">
   <thead>
      <tr>
         <th style="width:5%">No.</th>
         <th style="width:50%">Item Name</th>
         <th style="width:5%">Qty</th>
         <th style="width:5%">UOM</th>
         <th style="width:25%">Total Amount</th>
      </tr>
   </thead>
   <?php if(!empty($dataTindakan)) : ?>
   <tbody>
      <?php
         $no = 1; 
         $total = $totalDiskon = 0;
         $endingBalance = $totalKembalian;
         $biayaAdmin = ArrayHelper::getValue($biaya_admin, 'biaya_admin', 0);
         foreach ($dataTindakan as $key => $value) :
            $tarif = ArrayHelper::getValue($value, 'tarif', 0);
            $tarifDiskon = ArrayHelper::getValue($value, 'tarif_diskon', 0);
            $total += $tarif;
            $totalDiskon += $tarifDiskon;
      ?>
         <tr>
            <td style="text-align: center;"> <?= $no++ ?></td>
            <td><?= ArrayHelper::getValue($value, 'obatalkes_nama') ?></td>
            <td class="number"><?= ArrayHelper::getValue($value, 'qty', 1) ?></td>
            <td><?= ArrayHelper::getValue($value, 'uom') ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($tarif) ?></td>
         </tr>
         <?php endforeach; ?>
   </tbody>
   <tfoot>
      <tr>
         <td colspan="4" class="number">Total Amount:</td>
         <td class="number"><?= DocoHelpers::formatNumber($total) ?></td>
      </tr>
      <tr>
         <td colspan="4" class="number">Discount :</td>
         <td class="number"><?= DocoHelpers::formatNumber($totalDiskon) ?></td>
      </tr>
      <tr>
         <td colspan="4" class="number">Administration Fee :</td>
         <td class="number border-bottom"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
      </tr>
      <tr>
         <td colspan="4" class="number">Rounded Bill Amount :</td>
         <td class="number"><?= DocoHelpers::formatNumber($pembulatan + $totalPembulatan) ?></td>
      </tr>
      <tr>
         <td colspan="4" class="number">Grand Total :</td>
         <td class="number border-bottom"><?= DocoHelpers::formatNumber($total + $pembulatan + $totalPembulatan) ?></td>
      </tr>
      <tr>
         <td colspan="5" class="border-bottom">Patient Amount in Words : <?= DocoHelpers::terbilangToEnglish($total + $pembulatan + $totalPembulatan) ?> Rupiah</td>
      </tr>
      <tr>
         <td colspan="4">Received payment from : <?= $namaPasien ?> </td>
      </tr>
      <tr>
         <td colspan="4" class="number"><strong>Ending Balance :</strong></td>
         <td class="number"><?= DocoHelpers::formatNumber($endingBalance) ?></td>
      </tr>
   </tfoot>
    <?php endif; ?>
</table>



