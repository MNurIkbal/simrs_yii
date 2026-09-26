<?php 
   use yii\helpers\ArrayHelper;
   use Doco\components\DocoHelpers;
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
      padding: 3px;
   }
   .tbl-bordered tbody td {
      border: 1px solid black;
      padding: 3px;
   }
   .number {
      text-align: right
   }
   .center {
      text-align: center
   }
   .left {
      text-align: left
   }
   .border-bottom {
      border-bottom: 1px solid;
   }
</style>

<?php $granTotalPayer = $granTotalPatient = $totalDiskonPenjamin = $totalDiskonPasien = 0; ?>

<?php if (!empty($dataRoomRent)) : ?>
   <table width="100%" class="tbl-bordered">
      <thead>
         <tr>
            <th width="5%">No </th>
            <th width="10%">From Date</th>
            <th width="10%">To Date</th>
            <th width="15%">Bed Type</th>
            <th width="20%">Bed No</th>
            <th width="5%">Qty</th>
            <th width="15%">Price</th>
            <th width="15%">Payer Amount</th>
            <th width="15%">Patient Amount</th>
         </tr>
         <?php foreach ($dataRoomRent as $key => $ruangan) : ?>
         <tr>
            <th colspan="9" style="text-align: left;background-color: #D5D8DC;"><strong><?= $key ?></strong></th>
         </tr>
         <tbody>
            <?php foreach ($ruangan as $keyKelompok => $kelompok) : ?>
            <tr>
               <td colspan="9" style="text-align: left;background-color: #F7DC6F;"><strong><?= $keyKelompok ?></strong></td>
            </tr>
            <?php 
               $no = 1;
               $isDijamin = $dijamin > 0 ? true : false;
               $totalPayer = $totalPatient = 0;
               foreach ($kelompok as $value) :
                  $isDiskon = ArrayHelper::getValue($value, 'is_diskon', false);
                  $qty = ArrayHelper::getValue($value, 'qty', 1);
                  $min = ArrayHelper::getValue($value, 'min', 0);
                  $max = ArrayHelper::getValue($value, 'max', 0);
                  $noBed = ArrayHelper::getValue($value, 'no_bed');
                  $kelas = ArrayHelper::getValue($value, 'kelas');
                  $kamar = ArrayHelper::getValue($value, 'kamar');
                  $hargaSatuan = ArrayHelper::getValue($value, 'harga_satuan', 0);
                  $tarifDijamin = ArrayHelper::getValue($value, 'tarif_dijamin', 0);
                  $tarifDibayarkan = ArrayHelper::getValue($value, 'tarif_dibayarkan', 0);
                  $tarifDiskon = ArrayHelper::getValue($value, 'tarif_diskon', 0);
                  $diskonPatient = ArrayHelper::getValue($value, 'diskon_pasien', 0);
                  $diskonPenjamin = ArrayHelper::getValue($value, 'diskon_payer', 0);
                  $totalDiskonPenjamin += $diskonPenjamin;
                  $totalDiskonPasien += $diskonPatient;
                  $nominPayer = ($jenis_invoice == $invoice_pasien) ? 0 : $tarifDijamin;
                  $nominPatient = ($jenis_invoice == $invoice_penjamin) ? 0 : $tarifDibayarkan;
                  $totalPayer += $nominPayer;
                  $totalPatient += $nominPatient;
            ?>
            <tr>
               <td class="center"><?= ($isDiskon) ? null : $no++; ?></td>
               <td><?= date('d/m/Y', strtotime($min)) ?></td>
               <td><?= date('d/m/Y', strtotime($max)) ?></td>
               <td><?= $kelas ?></td>
               <td><?= $kamar ?> - <?= $noBed ?></td>
               <td class="number"><?= ($isDiskon) ? null : DocoHelpers::formatNumber($qty) ?></td>
               <td class="number"><?= ($isDiskon) ? null : DocoHelpers::formatNumber($hargaSatuan) ?></td>
               <td class="number"><?= DocoHelpers::formatNumber($nominPayer) ?></td>
               <td class="number"><?= DocoHelpers::formatNumber($nominPatient) ?></td>
            </tr>
            <?php 
               endforeach;
               $granTotalPayer += $totalPayer;
               $granTotalPatient += $totalPatient;
            ?>
            <tr>
               <td colspan="7" class="number"><b>Total Tagihan <?= $keyKelompok ?></b></td>
               <td class="number">
                  <b><?=  DocoHelpers::formatNumber($totalPayer) ?></b>
               </td>
               <td class="number">
                  <b><?= DocoHelpers::formatNumber($totalPatient) ?></b>
               </td>
            </tr>
         <?php endforeach; ?>
         </tbody>
         <?php endforeach; ?>
      </thead>
   </table>
   <br>
<?php endif; ?>

<table width="100%" class="tbl-bordered">
   <thead>
      <tr>
         <th width="5%"><strong>No</strong> </th>
         <th><strong>Trans Date</strong></th>
         <th><strong>Service(s)</strong></th>
         <th><strong>Doctor</strong></th>
         <th width="5%"><strong>Qty</strong></th>
         <th><strong>Price</strong></th>
         <th><strong>Cyto</strong></th>
         <th><strong>Penyulit</strong></th>
         <th><strong>Payer Amount</strong></th>
         <th><strong>Patient Amount</strong></th>
      </tr>
   </thead>
   <tbody>
      <?php 
         $biayaAdmin = ArrayHelper::getValue($biaya_admin, 'biaya_admin', 0);
         $penjamin_id = ArrayHelper::getValue($biaya_admin, 'penjamin_id', 0);
         $nominPayer = ArrayHelper::getValue($biaya_admin, 'nominPayer', 0);
         $nominPatient = ArrayHelper::getValue($biaya_admin, 'nominPatient', 0);
         if($biayaAdmin > 0)  : 
            $granTotalPayer += $nominPayer;
            $granTotalPatient += $nominPatient;
         ?>
         <tr>
            <td colspan="10" style="text-align: left;background-color: #F7DC6F;">
               <strong>Biaya Administrasi</strong>
            </td>
         </tr>
         <tr>
            <td class="center">1</td>
            <td><?= date('d/m/Y', strtotime($tgl_pembayaran)) ?></td>
            <td><?= $tindakan_admin ?></td>
            <td></td>
            <td class="number">1</td>
            <td class="number"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
            <td class="number">0</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($nominPayer) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($nominPatient) ?></td>
         </tr>
      <?php endif; ?>
      <?php foreach ($dataTindakan as $ruangan => $kelompok) : ?>
      <tr>
         <td colspan="10" style="text-align: left;background-color: #D5D8DC;">
            <strong><?= $ruangan ?></strong>
         </td>
      </tr>
      <?php foreach ($kelompok as $key => $value) : ?>
      <tr>
         <td colspan="10" style="text-align: left;background-color: #F7DC6F;">
            <strong><?= $key ?></strong>
         </td>
      </tr>
      <?php 
         $no = 1; 
         $isDijamin = $dijamin > 0 ? true : false;
         $totalPayer = $totalPatient = $totalDiskon = 0;
         foreach ($value as $val) : 
            $isDiskon = ArrayHelper::getValue($val, 'is_diskon', false);
            $qty = ArrayHelper::getValue($val, 'qty', 1);
            $tindakanObat = ArrayHelper::getValue($val, 'tindakan_obat');
            $remarks = isset($val['remarks']) ? (" - ".substr($val['remarks'], 1, -1)) : '';
            $tindakanObat = $tindakanObat . $remarks;
            $tglPelayanan = ArrayHelper::getValue($val, 'tgl_pelayanan');
            $dokter = ArrayHelper::getValue($val, 'dokter');
            $tarifDijamin = ArrayHelper::getValue($val, 'tarif_dijamin', 0);
            $tarifDibayarkan = ArrayHelper::getValue($val, 'tarif_dibayarkan', 0);
            $tarifDiskon = ArrayHelper::getValue($val, 'tarif_diskon', 0);
            $diskonPatient = ArrayHelper::getValue($val, 'diskon_pasien', 0);
            $diskonPenjamin = ArrayHelper::getValue($val, 'diskon_payer', 0);
            $totalDiskonPenjamin += $diskonPenjamin;
            $totalDiskonPasien += $diskonPatient;
            $hargaSatuan = ArrayHelper::getValue($val, 'harga_satuan', 0);
            $tarif = ArrayHelper::getValue($val, 'tarif', 0);
            $tarifCito = ArrayHelper::getValue($val, 'tarifcyto_tindakan', 0);
            $tarifPenyulit = ArrayHelper::getValue($val, 'tarifpenyulit_tindakan', 0);
            $cito = ArrayHelper::getValue($val, 'cyto_tindakan', false);
            $cito = ($cito) ? 'Yes' : 'No';
            $tarif = $qty * $hargaSatuan;
            $nominPayer = ($jenis_invoice == $invoice_pasien) ? 0 : $tarifDijamin;
            $nominPatient = ($jenis_invoice == $invoice_penjamin) ? 0 : $tarifDibayarkan;
            $totalPayer += $nominPayer;
            $totalPatient += $nominPatient;
            $totalDiskon += $tarifDiskon;
      ?>
      <tr>
         <td class="center"><?= ($isDiskon) ? null : $no++; ?></td>
         <td><?= date('d/m/Y', strtotime($tglPelayanan)) ?></td>
         <td><?= $tindakanObat ?></td>
         <td><?= $dokter ?></td>
         <td class="number"><?= ($isDiskon) ? null : $qty ?></td>
         <td class="number"><?= ($isDiskon) ? null : DocoHelpers::formatNumber($hargaSatuan) ?></td>
         <td class="number"><?= ($isDiskon) ? null : DocoHelpers::formatNumber($tarifCito) ?></td>
         <td class="number"><?= ($isDiskon) ? null : DocoHelpers::formatNumber($tarifPenyulit) ?></td>
         <td class="number"><?= DocoHelpers::formatNumber($nominPayer) ?></td>
         <td class="number"><?= DocoHelpers::formatNumber($nominPatient) ?></td>
      </tr>
      <?php 
      endforeach;
      $granTotalPayer += $totalPayer;
      $granTotalPatient += $totalPatient; 
      ?>
      <tr>
         <td colspan="8" class="number"><b>Total Tagihan <?= $key ?> </b></td>
         <td class="number"><b><?= DocoHelpers::formatNumber($totalPayer) ?></b></td>
         <td class="number"><b><?= DocoHelpers::formatNumber($totalPatient) ?></b></td>
      </tr>
      <?php endforeach; ?>
      <?php endforeach; ?>
      <?php 
         if($granTotalPayer > 0) {
            $discDokterPasien = 0;
         }

         // $subTotalPayer = $granTotalPayer + $totalDiskonPenjamin;
         // $subTotalPasien = $granTotalPatient + $totalDiskonPasien;
         $subTotalPayer = $granTotalPayer;
         $subTotalPasien = $granTotalPatient;
         $totalAmountPasien = ($granTotalPatient + $totalPembulatanPasien) - $uangMuka - $sisaTagihan - $discDokterPasien;
         $totalAmountPayer = ($granTotalPayer + $totalPembulatanPayer) - $discDokterPayer;

         // $amountPayer = $granTotalPayer - $discDokterPayer - $diskonPayer + $totalPembulatanPayer;
         // $amountPatient = ($granTotalPatient - $discDokterPasien - $diskonPasien - $uangMuka - $sisaTagihan) + $totalPembulatanPasien;
      ?>
      <tr>
         <td colspan="8" class="number"><b>Sub Total : </b></td>
         <td class="number">
            <b><?= ($jenis_invoice != $invoice_pasien) ? DocoHelpers::formatNumber($subTotalPayer) : 0 ?></b>
         </td>
         <td class="number">
            <b><?= ($jenis_invoice != $invoice_penjamin) ? DocoHelpers::formatNumber($subTotalPasien) : 0 ?></b>
         </td>
      </tr>
      <tr>
         <td colspan="8" class="number"><b>Discount : </b></td>
         <td class="number">
            <b><?= ($jenis_invoice != $invoice_pasien) ? DocoHelpers::formatNumber(/* $totalDiskonPenjamin +  */$discDokterPayer) : 0 ?></b>
         </td>
         <td class="number">
            <b>
               <?= ($jenis_invoice != $invoice_penjamin) ? DocoHelpers::formatNumber(/* $totalDiskonPasien +  */$discDokterPasien) : 0 ?>
            </b>
         </td>
      </tr>
      <tr>
         <td colspan="8" class="number"><b>DP : </b></td>
         <td class="number">
            <b>0</b>
         </td>
         <td class="number">
            <b><?= ($jenis_invoice == $invoice_penjamin) ? 0 : DocoHelpers::formatNumber($uangMuka) ?></b>
         </td>
      </tr>
      <tr>
         <td colspan="8" class="number"><b>DEBT : </b></td>
         <td class="number">
            <b>0</b>
         </td>
         <td class="number">
            <b><?= ($jenis_invoice == $invoice_penjamin) ? 0 : DocoHelpers::formatNumber($sisaTagihan) ?></b>
         </td>
      </tr>
      <tr>
         <td colspan="8" class="number"><b>Rounded Bill Amount : </b></td>
         <td class="number">
            <b><?= ($jenis_invoice == $invoice_pasien) ? 0 : DocoHelpers::formatNumber($totalPembulatanPayer) ?></b>
         </td>
         <td class="number">
            <b><?= ($jenis_invoice == $invoice_penjamin) ? 0 : DocoHelpers::formatNumber($totalPembulatanPasien) ?></b>
         </td>
      </tr>
      <tr>
         <td colspan="8" class="number"><b>Total Amount : </b></td>
         <td class="number">
            <b><?= ($jenis_invoice == $invoice_pasien) ? 0 : DocoHelpers::formatNumber($totalAmountPayer/* $amountPayer + $diskonPayer */) ?></b>
         </td>
         <td class="number">
            <b><?= ($jenis_invoice != $invoice_penjamin) ? DocoHelpers::formatNumber($totalAmountPasien) : 0 ?></b>
         </td>
      </tr>
   </tbody>
</table>

