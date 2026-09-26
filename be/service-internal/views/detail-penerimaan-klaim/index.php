<?php
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
?>
<style type="text/css">
   tr, th {
      font-family: Arial;
   }

   tr, td {
      font-family: Arial;
   }

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

<?php if($caraBayarId == DocoConstants::CARA_BAYAR_BPJS) : ?>
<table width="100%" class="tbl-bordered">
   <thead>
      <tr>
         <th>No.</th>
         <th>No Pengajuan</th>
         <th>No. SEP</th>
         <th>Tanggal Verifikasi</th>
         <th>Tagihan Rumah Sakit</th>
         <th>Tagihan Diajukan</th>
         <th>Tagihan Disetujui</th>
      </tr>
   </thead>
   <tbody>
      <?php $no = 1; 
         foreach ($data as $key => $value) : ?>
         <tr>
            <td style="text-align: center;"><?= $no++ ?></td>
            <td><?= ArrayHelper::getValue($value, 'no_pengajuanklaim') ?></td>
            <td><?= ArrayHelper::getValue($value, 'no_sep') ?></td>
            <td><?= ArrayHelper::getValue($value, 'tgl_verifikasi') ?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'tagihan_rs', 0)) ?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_pengajuan', 0)) ?></td>
            <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_terbayar', 0)) ?></td>
         </tr>
      <?php endforeach ?>
   </tbody>
</table>

<?php else : ?>
   <table width="100%" class="tbl-bordered">
      <thead>
         <tr>
            <th>No.</th>
            <th>No Pengajuan</th>
            <th>Total Pengajuan</th>
            <th>Telah Bayar</th>
            <th>Pembayaran</th>
            <th>Sisa Piutang</th>
         </tr>
      </thead>
      <tbody>
         <?php $no = 1; 
            foreach ($data as $key => $value) : ?>
            <tr>
               <td style="text-align: center;"><?= $no++ ?></td>
               <td><?= ArrayHelper::getValue($value, 'no_pengajuanklaim') ?></td>
               <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_pengajuan', 0)) ?></td>
               <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_terbayar', 0)) ?></td>
               <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'pembayaran', 0)) ?></td>
               <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'total_sisapiutang', 0)) ?></td>
            </tr>
         <?php endforeach ?>
      </tbody>
   </table>
<?php endif; ?>


