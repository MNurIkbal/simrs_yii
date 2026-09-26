<?php 
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers; 
?>
<style>
   body { 
      font-size: 12px;
      letter-spacing: 2px;
      font-family: "Arial, Helvetica, sans-serif";
   }
   .tbl-bordered {
      border-collapse: collapse;
      /*border: 1px solid black;*/
      font-size: 12px;
      letter-spacing: 2px;
      font-family: "Arial, Helvetica, sans-serif";
   }
   .tbl-bordered thead td {
      border-top: 1px solid black;
      border-bottom: 1px solid black;
      font-family: "Arial, Helvetica, sans-serif";
      text-align: center;
      letter-spacing: 2px;
   }
   .tbl-bordered tbody td {
      /*border: 1px solid black;*/
      font-family: "Arial, Helvetica, sans-serif";
      letter-spacing: 2px;
   }

   .tbl-bordered tfoot td {
      padding: 3px;
      letter-spacing: 2px;
   }
   .tbl-alamat {
      border-collapse: collapse;
      /*border: 1px solid black;*/
      font-size: 12px;
      font-family: "Arial, Helvetica, sans-serif";
      letter-spacing: 2px;
   }
   .tabel-header {
      font-family: "Arial, Helvetica, sans-serif";
      letter-spacing: 2px;
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
</style>

<table width="100%" class="tbl-bordered">
   <thead>
      <tr>
         <td style="width: 50%;">TRANSAKSI</td>
         <td style="width: 15%;">QTY</td>
         <td style="width: 35%;">JUMLAH</td>
      </tr>
   </thead>
   <tbody>
   <?php foreach ($dataTindakan as $kRuangan => $ruangan) : ?>
      <?php foreach ($ruangan as $kKelompok => $kelompok) : ?>
      <tr>
         <td colspan="3"><b><?= strtoupper($kKelompok) ?></b></td>
      </tr>
      <?php foreach ($kelompok as $val) : 
         $tindakan = ArrayHelper::getValue($val, 'tindakan_obat');
         $qty = ArrayHelper::getValue($val, 'qty', 1);
         $jumlahTarif = ArrayHelper::getValue($val, 'tarif', 0);
      ?>
         <tr>
            <td style="width: 50%;"><?= $tindakan ?></td>
            <td class="number" style="width: 15%;"><?= $qty ?></td>
            <td class="number" style="width: 35%;"><?= DocoHelpers::formatNumber($jumlahTarif) ?></td>
         </tr>
      <?php endforeach; ?>
      <tr>
         <td>&nbsp;</td>
      </tr>
      <?php endforeach; ?>
      <?php endforeach; ?>
   </tbody>
</table>

