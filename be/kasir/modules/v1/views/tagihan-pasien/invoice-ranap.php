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
         <th style="width:55%">Service(s)</th>
         <th style="width:20%">Payer Amount</th>
         <th style="width:20%">Patient Amount</th>
      </tr>
   </thead>
   <tbody>
      <?php
         $no = 1; 
         $total = 0; 
         $subTotalPayer = $subTotalPatient = 0;
         $biayaAdmin = ArrayHelper::getValue($biaya_admin, 'biaya_admin', 0);
         $nominPayer = ArrayHelper::getValue($biaya_admin, 'nominPayer', 0);
         $nominPatient = ArrayHelper::getValue($biaya_admin, 'nominPatient', 0);
         $admAsuransi = ArrayHelper::getValue($additionalPembayaran, 'adm_asuransi', []);
         $nominalDiskon = ArrayHelper::getValue($admAsuransi, 'nominal_diskon', 0);
			$discDijamin = ArrayHelper::getValue($admAsuransi, 'dijamin', 0);
         $discPasien = ArrayHelper::getValue($admAsuransi, 'harusbayar', 0);
         if($discDijamin > 0) {
            $discDijamin = $nominalDiskon;
         }
         if($discPasien > 0) {
            $discPasien = $nominalDiskon;
         }
         if ($biayaAdmin > 0) :
            $subTotalPayer += $nominPayer;
            $subTotalPatient += $nominPatient;
      ?>
         <tr>
            <td style="width:2px;text-align:center"> <?= $no++ ?></td>
            <td><?= $tindakan_admin ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($nominPayer) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($nominPatient) ?></td>
         </tr>
      <?php
         endif;
         foreach ($dataTindakan as $value) : 
            $totalPayer = $totalPatient = 0;
            $kelompok = ArrayHelper::getValue($value, 'kelompoktindakan_nama', 0);
            $tarifDibayarkan = ArrayHelper::getValue($value, 'tarif_dibayarkan', 0);
            $tarifDijamin = ArrayHelper::getValue($value, 'tarif_dijamin', 0);
            $subTotal = ArrayHelper::getValue($value, 'sub_total', 0);
            $total += $subTotal;
            $subTotalPayer += $tarifDijamin; 
            $subTotalPatient += $tarifDibayarkan;
      ?>
         <tr>
            <td style="width:2px;text-align:center"> <?= $no++ ?></td>
            <td>
               <?= $kelompok ?>
            </td>
            <td class="number"><?= DocoHelpers::formatNumber($tarifDijamin) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($tarifDibayarkan) ?></td>
         </tr>
      <?php endforeach; ?>
   </tbody>
</table>

<?php 
$diskonPasien = ArrayHelper::getValue($dataDiskon, 'diskonPasien', 0); 
$diskonPayer = ArrayHelper::getValue($dataDiskon, 'diskonPayer', 0);
$payerAmount = $subTotalPayer + $totalPembulatanPayer - $discDokterPayer;
$pasienAmount = ($subTotalPatient - $discDokterPasien - $uangMuka - $sisaTagihan) + $totalPembulatanPasien;
?>

<table width="100%" class="tbl-bordered">
   <tr>
      <td colspan="4" class="number" style="width:60%">Total Amount :</td>
      <td class="number border-bottom" style="width:20%"><?= DocoHelpers::formatNumber($subTotalPayer) ?></td>
      <td class="number border-bottom" style="width:20%"><?= DocoHelpers::formatNumber($subTotalPatient) ?></td>
   </tr>
   <tr>
      <td colspan="4" class="number">Discount :</td>
      <td class="number" style="width:20%"><?= DocoHelpers::formatNumber($diskonPayer) ?></td>
      <td class="number" style="width:20%"><?= DocoHelpers::formatNumber($diskonPasien) ?></td>
   </tr>
   <tr>
      <td colspan="4" class="number">DP :</td>
      <td class="number">0</td>
      <td class="number"><?= DocoHelpers::formatNumber($uangMuka) ?></td>
   </tr>
   <tr>
      <td colspan="4" class="number">DEBT :</td>
      <td class="number">0</td>
      <td class="number"><?= DocoHelpers::formatNumber($sisaTagihan) ?></td>
   </tr>
   <tr>
      <td colspan="4" class="number">Rounded Bill Amount :</td>
      <td class="number"><?= DocoHelpers::formatNumber($totalPembulatanPayer) ?></td>
      <td class="number"><?= DocoHelpers::formatNumber($pembulatan) ?></td>
   </tr>
   <tr>
      <td colspan="4" class="number">Amount :</td>
      <td class="number border-bottom"><?= DocoHelpers::formatNumber($payerAmount) ?></td>
      <td class="number border-bottom"><?= DocoHelpers::formatNumber($pasienAmount) ?></td>
   </tr>
   <tr>
      <td colspan="4" class="number">Total amount cover by payer :</td>
      <td class="number"><?= DocoHelpers::formatNumber($payerAmount) ?></td>
      <td class="number"><?= DocoHelpers::formatNumber($pasienAmount ) ?></td>
   </tr>
   <tr>
      <td>&nbsp;</td>
   </tr>
   <?php if ($dijamin > 0) : ?>
      <tr>
         <td colspan="6" class="border-bottom">
            Payer Amount in Words : <?= DocoHelpers::terbilangToEnglish($dijamin) ?> Rupiahs
         </td>
      </tr>
   <?php endif; ?>

   <?php if ($pasienAmount > 0) : ?>
      <tr>
         <td colspan="6" class="border-bottom">
            Patient Amount in Words : <?= DocoHelpers::terbilangToEnglish($pasienAmount) ?> Rupiahs
         </td>
      </tr>
   <?php endif; ?>

   <?php if ($totalTunai > 0) : ?>
      <tr>
         <td colspan="6">Received payment from : <?= $namaPasien ?> </td>
      </tr>
      <tr>
         <td colspan="5">Cash</td>
         <td class="number"><?= DocoHelpers::formatNumber($totalTunai) ?></td>
      </tr>
   <?php endif; ?>

   <?php if (!empty($listMetode)) :
      foreach ($listMetode as $value) :
         $metodeBayar =  ArrayHelper::getValue($value, 'metode_bayar');
         $noKartu =  ArrayHelper::getValue($value, 'no_kartu');
         $totalDibayar =  ArrayHelper::getValue($value, 'total_dibayar', 0);
         $metode = preg_replace("/^\w+ - /", '', $metodeBayar);
   ?>
      <tr>
         <td colspan="5"><?= $noKartu ?> - <?= $metode ?></td>
         <td class="number"><?= DocoHelpers::formatNumber($totalDibayar) ?></td>
      </tr>
   <?php
         endforeach;
      endif; 
   ?>
   
   <?php if (!empty($listPayer)) :
      foreach ($listPayer as $value) :
         $penjaminNama =  ArrayHelper::getValue($value, 'penjamin_nama');
         $totalDijamin =  ArrayHelper::getValue($value, 'total_dijamin', 0);
         $nama = preg_replace("/^\w+ - /", '', $penjaminNama);
   ?>
      <tr>
         <td colspan="5">by Payer : <?= $nama ?></td>
         <td class="number"><?= DocoHelpers::formatNumber($totalDijamin - $discDokterPayer) ?></td>
      </tr>
   <?php
         endforeach;
      endif; 
   ?>
   <tr>
      <td colspan="5"></td>
      <td class="border-bottom"></td>
   </tr>
   <tr>
      <td colspan="5" class="number"><strong>Ending Balance :</strong></td>
      <td class="number"><?= DocoHelpers::formatNumber($totalKembalian) ?></td>
   </tr>
</table>


