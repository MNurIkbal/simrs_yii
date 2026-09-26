<?php 
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
?>
<style>
   body { 
      font-size: 12px;
      letter-spacing: 1px;
      font-family: "Arial, Helvetica, sans-serif";
   }
   .tbl-bordered {
      border-collapse: collapse;
      /*border: 1px solid black;*/
      font-size: 12px;
      letter-spacing: 1px;
      font-family: "Arial, Helvetica, sans-serif";
   }
   .tbl-bordered thead td {
      border-top: 1px solid black;
      border-bottom: 1px solid black;
      font-family: "Arial, Helvetica, sans-serif";
      text-align: center;
      letter-spacing: 1px;
   }
   .tbl-bordered tbody td {
      /* border: 1px solid black; */
      font-family: "Arial, Helvetica, sans-serif";
      letter-spacing: 1px;
   }

   .tbl-bordered tfoot td {
      padding: 3px;
      letter-spacing: 1px;
   }
   .tbl-alamat {
      border-collapse: collapse;
      /*border: 1px solid black;*/
      font-size: 12px;
      font-family: "Arial, Helvetica, sans-serif";
      letter-spacing: 1px;
   }
   .tabel-header {
      font-family: "Arial, Helvetica, sans-serif";
      letter-spacing: 1px;
   }
   .tbl-alamat tr td {
      /*border: 1px solid black;*/
      font-family: "Arial, Helvetica, sans-serif";
      letter-spacing: 2px;
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
   .tbl-footer {
      font-family: "Arial, Helvetica, sans-serif";
      border-bottom: 2px solid black;
   }
</style>

<table width="100%" class="tbl-bordered" style="width:100%; border-collapse: collapse;">
   <thead>
      <tr>
         <td class="center">KAMAR</td>
         <td class="center">KELAS</td>
         <td class="center">TT</td>
         <td class="center">DARI</td>
         <td class="center">SAMPAI</td>
         <td class="center">HARI</td>
         <td class="center">TARIF/HARI</td>
         <td class="center">BIAYA</td>
      </tr>
   </thead>
   <tbody>
      <?php $totalAkom = 0; if(!empty($detailAkomodasi)) : ?>
      <tr>
         <td colspan="8">&nbsp;</td>
      </tr>
      <?php $totalKamar = 0; 
      foreach ($detailAkomodasi as $key => $value) : 
         $lamaRawat = ArrayHelper::getValue($value, 'lama_rawat', 0);
         $tarifKamar = ArrayHelper::getValue($value, 'tarif_kamar', 0);
         $totalAkomodasi = $lamaRawat * $tarifKamar;
         $totalKamar += $totalAkomodasi;
         $kamar = ArrayHelper::getValue($value, 'kamar', '-');
         $kelas = ArrayHelper::getValue($value, 'kelas', '-');
         $noBed = ArrayHelper::getValue($value, 'tempat_tidur', '-');
         $tglMasuk = ArrayHelper::getValue($value, 'tgl_masuk');
         $tglKeluar = ArrayHelper::getValue($value, 'tgl_keluar');
         if(!empty($tglMasuk)) {
            $tglMasuk = date('d/m/Y', strtotime($tglMasuk));
         }
         if(!empty($tglKeluar)) {
            $tglKeluar = date('d/m/Y', strtotime($tglKeluar));
         }
      ?>
         <tr>
            <td style="width:25%;"><?= $kamar ?></td>
            <td style="width:15%;text-align: center;"><?= $kelas ?></td>
            <td style="width:5%;"><?= $noBed ?></td>
            <td style="width:15%;"><?= $tglMasuk ?></td>
            <td style="width:15%;"><?= $tglKeluar ?></td>
            <td style="text-align: right;width:10%;text-align: center;"><?= $lamaRawat ?></td>
            <td style="text-align: right;width:15%;"><?= DocoHelpers::formatNumber($tarifKamar) ?></td>
            <td style="text-align: right;width:10%;"><?= DocoHelpers::formatNumber($totalAkomodasi) ?></td>
         </tr>
         <?php endforeach; ?>
         <tr>
            <td colspan="8">&nbsp;</td>
         </tr>
         <tr>
            <td colspan="7" style="border-top:2px solid black;">KAMAR PERAWATAN</td>
            <td style="text-align: right;border-top:2px solid black;"><?= DocoHelpers::formatNumber($totalKamar) ?></td>
         </tr>
         <?php $totalAkom = $totalKamar; ?>
         <tr>
            <td colspan="8">&nbsp;</td>
         </tr>
      <?php endif; ?>
      <?php
         $subTotal = 0;
         foreach ($dataTindakan as $value) :
            $kelompok = ArrayHelper::getValue($value, 'kelompok');
            $totalTarif = ArrayHelper::getValue($value, 'sub_total', 0);
            $subTotal += $totalTarif;
      ?>
         <tr>
            <td colspan="7"><?= strtoupper($kelompok) ?></td>
            <td style="text-align: right; width: 35%;"><?= DocoHelpers::formatNumber($totalTarif) ?></td>
         </tr>
      <?php
         endforeach;
      ?>
   </tbody>
</table>
