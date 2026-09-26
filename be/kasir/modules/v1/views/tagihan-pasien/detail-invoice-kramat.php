<?php 
   use yii\helpers\ArrayHelper;
   use Doco\components\DocoHelpers;
   
   $grandTotal = 0;
   $_mappObatAlkes = [
		'Drugs & Consumables' => 'Obat Alkes',
		'kelompok_obat' => 'Obat Alkes',
		'kelompok_paket' => 'Paket',
		'kelompok_paket_mcu' => 'Paket MCU',
		'Consultation' => 'Konsultasi'
	];
?>

<style>
   body { 
      font-size: 12px;
      letter-spacing: 1px;
      font-family: "Arial, Helvetica, sans-serif";
   }
   .header {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11px;
      letter-spacing: 1px;
      text-align: center;
   }

   .tabel_data {
      font-family: Arial, Helvetica, sans-serif;
      font-size:12px;
   }

   .tabel_data_numeric {
      font-family: Arial, Helvetica, sans-serif;
      font-size:12px;
      text-align: right;
   }

   .tabel_data_group {
      font-family: Arial, Helvetica, sans-serif;
      font-size:12px;
      font-weight:bold;
   }

   .tabel_data_group_numeric {
      font-family: Arial, Helvetica, sans-serif;
      font-size:12px;
      font-weight:bold;
      text-align: right;
   }

   .tabel_header {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 13px;
   }

   hr.new1 {
      border: none;
      height: 2px;
      color: #333;
      background-color: #333;
   }

</style>

<table border="0" style="width:100%;" cellspacing="1">
   <tbody>
      <?php 
      $biayaAdmin = ArrayHelper::getValue($biaya_admin, 'biaya_admin', 0);
      $penjamin_id = ArrayHelper::getValue($biaya_admin, 'penjamin_id', 0);
      $nominPayer = ArrayHelper::getValue($biaya_admin, 'nominPayer', 0);
      $nominPatient = ArrayHelper::getValue($biaya_admin, 'nominPatient', 0);
      if($biayaAdmin > 0)  : 
         $grandTotal += $biayaAdmin;
       ?>
         <tr>
            <td colspan="9" class="tabel_data_group">
               <strong>BIAYA ADMINISTRASI</strong>
            </td>
         </tr>
         <tr>
            <td class="tabel_data" style="width: 10%"><?= date('d/m/Y', strtotime($tgl_stopakomodasi)) ?></td>
            <td class="tabel_data" style="width: 10%"></td>
            <td class="tabel_data" style="width: 10%"></td>
            <td class="tabel_data" style="width: 15%"></td>
            <td class="tabel_data" style="width: 10%"></td>
            <td class="tabel_data" style="width: 17%"><?= $tindakan_admin ?></td>
            <td class="tabel_data_numeric" style="width: 5%">1</td>
            <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
            <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
         </tr>
      <?php endif; ?>
      <?php 
      if(!empty($dataRoomRent)) : ?>
         <?php foreach ($dataRoomRent as $key => $ruangan) : ?>
         <tr>
            <td colspan="9" class="tabel_data_group">
               <strong><?= strtoupper($key) ?></strong>
            </td>
         </tr>
         <?php foreach ($ruangan as $keyKelompok => $kelompok) : ?>
            <tr>
               <td colspan="9" class="tabel_data_group">
                  <strong><?= strtoupper($keyKelompok) ?></strong>
               </td>
            </tr>
            <?php $totalAkomodasi = 0; foreach ($kelompok as $value) : ?>
               <?php 
                  $qty = ArrayHelper::getValue($value, 'qty', 1);
                  $tglPelayanan = ArrayHelper::getValue($value, 'tgl_pelayanan');
                  $dokter = ArrayHelper::getValue($value, 'dokter');
                  $tindakan = ArrayHelper::getValue($value, 'tindakan_obat');
                  $tindakanKode = ArrayHelper::getValue($value, 'tindakan_obat_kode');
                  $noBed = ArrayHelper::getValue($value, 'no_bed');
                  $kelas = ArrayHelper::getValue($value, 'kelas');
                  $kamar = ArrayHelper::getValue($value, 'kamar');
                  $hargaSatuan = ArrayHelper::getValue($value, 'harga_satuan', 0);
                  $tarif = ArrayHelper::getValue($value, 'total_amount', 0);
                  $tarifDijamin = ArrayHelper::getValue($value, 'tarif_dijamin', 0);
                  $tarifDibayarkan = ArrayHelper::getValue($value, 'tarif_dibayarkan', 0);
                  $grandTotal += $tarif;
               ?>
               <tr>
                  <td class="tabel_data" style="width: 10%"><?= date('d/m/Y', strtotime($tglPelayanan)) ?></td>
                  <td class="tabel_data" style="width: 10%"><?= $kamar . '/' . $noBed ?></td>
                  <td class="tabel_data" style="width: 10%"><?= $kelas ?></td>
                  <td class="tabel_data" style="width: 15%"><?= $dokter ?></td>
                  <td class="tabel_data" style="width: 10%"><?= $tindakanKode ?></td>
                  <td class="tabel_data" style="width: 17%"><?= $tindakan ?></td>
                  <td class="tabel_data_numeric" style="width: 5%"><?= $qty ?></td>
                  <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($hargaSatuan) ?></td>
                  <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($tarif) ?></td>
               </tr>
            <?php $totalAkomodasi += $tarif; endforeach; ?>
            <tr>
               <td colspan="8" class="tabel_data"><b>SUB TOTAL <?= strtoupper($keyKelompok) ?> </b></td>
               <td class="tabel_data_numeric"><b><?= DocoHelpers::formatNumber($totalAkomodasi) ?></b></td>
            </tr>
            <tr>
               <td colspan="9">&nbsp;</td>
            </tr>
         <?php endforeach; ?>
         <?php endforeach; ?>
      <?php endif; ?>
      <?php foreach ($dataTindakan as $ruangan => $kelompok) : ?>
         <tr>
            <td colspan="9">&nbsp;</td>
         </tr>
         <tr>
            <td colspan="9" class="tabel_data_group">
               <strong><?= strtoupper($ruangan) ?></strong>
            </td>
         </tr>
         <?php foreach ($kelompok as $key => $value) : 
            $key = isset($_mappObatAlkes[$key]) ? $_mappObatAlkes[$key] : $key; ?>
         <tr>
            <td colspan="9" class="tabel_data_group">
               <strong><?= strtoupper($key) ?></strong>
            </td>
         </tr>
         <?php 
            $no = 1; 
            $total = 0;
            foreach ($value as $val) : 
               $qty = ArrayHelper::getValue($val, 'qty', 1);
               $tindakan = ArrayHelper::getValue($val, 'tindakan_obat');
               $tglPelayanan = ArrayHelper::getValue($val, 'tgl_pelayanan');
               $dokter = ArrayHelper::getValue($val, 'dokter');
               $kelas = ArrayHelper::getValue($val, 'kelas');
               $kamar = ArrayHelper::getValue($val, 'kamar');
               $tindakanKode = ArrayHelper::getValue($val, 'tindakan_obat_kode');
               $tarifDijamin = ArrayHelper::getValue($val, 'tarif_dijamin', 0);
               $tarifDibayarkan = ArrayHelper::getValue($val, 'tarif_dibayarkan', 0);
               $tarifDiskon = ArrayHelper::getValue($val, 'tarif_diskon', 0);
               $hargaSatuan = ArrayHelper::getValue($val, 'harga_satuan', 0);
               $tarif = ArrayHelper::getValue($val, 'tarif', 0);
               $grandTotal += $tarif;
         ?>
         <tr>
            <td class="tabel_data"><?= date('d/m/Y', strtotime($tglPelayanan)) ?></td>
            <td class="tabel_data"><?= $kamar ?></td>
            <td class="tabel_data"><?= $kelas ?></td>
            <td class="tabel_data"><?= $dokter ?></td>
            <td class="tabel_data"><?= $tindakanKode ?></td>
            <td class="tabel_data"><?= $tindakan ?></td>
            <td class="tabel_data_numeric"><?= $qty; ?></td>
            <td class="tabel_data_numeric"><?= DocoHelpers::formatNumber($hargaSatuan) ?></td>
            <td class="tabel_data_numeric"><?= DocoHelpers::formatNumber($tarif) ?></td>
         </tr>
         <?php 
            $total += $tarif;
            $no++; 
         endforeach;
         ?>
         <tr>
            <td colspan="8" class="tabel_data"><b>SUB TOTAL <?= strtoupper($key) ?> </b></td>
            <td class="tabel_data_numeric"><b><?= DocoHelpers::formatNumber($total) ?></b></td>
         </tr>
         <tr>
            <td colspan="9">&nbsp;</td>
         </tr>
         <?php endforeach; ?>
         <?php endforeach; ?>
   </tbody>
</table>

<hr class="new1">
<table border="0" style="width:100%;" cellspacing="1">
<tfoot>
    <tr>
        <td class="tabel_data">Grand Total</td>
        <td class="tabel_data_group_numeric">
        <?= DocoHelpers::formatNumber(ceil($grandTotal)) ?> </td>
    </tr>
</tfoot>
</table>
<hr class="new1">

