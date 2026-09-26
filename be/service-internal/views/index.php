<?php
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
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
   <thead>
      <tr>
         <th>No.</th>
         <th>Tanggal Masuk</th>
         <th>Tanggal Keluar</th>
         <th>Pasien</th>
         <th>No SEP</th>
         <th>Penjamin</th>
         <th>Ruangan</th>
         <th>Dokter Penanggung Jawab</th>
         <th>Diagnosa Utama</th>
         <th>Diagnosa Penyerta</th>
         <th>Tindakan</th>
         <th>Tagihan RS</th>
         <th>Tarif Inacbg</th>
         <th>Persentase (%)</th>
         <th>Status</th>
         <th>Status Periksa</th>
      </tr>
   </thead>
   <tbody>
      <?php $no = 1; foreach ($data as $key => $value) : 
         $tanggalLahir = ArrayHelper::getValue($value, 'tanggal_lahir');
         $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
         $tglPasienPulang = ArrayHelper::getValue($value, 'tglpasienpulang');
         $setDiagnosaTindakan = ArrayHelper::getValue($value, 'set_diagnosatindakan');
         $setDiagnosaPenyerta = ArrayHelper::getValue($value, 'set_diagnosapenyerta');
         $tarifInacbg = ArrayHelper::getValue($value, 'tarif_inacbg', 0);
         $tagihanRs = ArrayHelper::getValue($value, 'tagihan_rs', 0);
         $persentase = ($tarifInacbg == 0) ? 0 : ceil(($tagihanRs/$tarifInacbg) * 100);
         if(!empty($tanggalLahir)) {
            $tanggalLahir = date('d-M--Y', strtotime($tanggalLahir));
         }
         if(!empty($tglPendaftaran)) {
            $tglPendaftaran = date('d-M-Y', strtotime($tglPendaftaran));
         }
         if(!empty($tglPasienPulang)) {
            $tglPasienPulang = date('d-M-Y', strtotime($tglPasienPulang));
         }

         $listDiagnosaTindakan = $listDiagnosaPenyerta = [];
         $diagnosaTindakanNama = '';
         $diagnosaPenyertaNama = '';
         
         if(!empty($setDiagnosaTindakan)) {
               $listDiagnosaTindakan = json_decode($setDiagnosaTindakan, true);
               if(!empty($listDiagnosaTindakan)) {
                  foreach ($listDiagnosaTindakan as $k => $v) {
                     $kode = ArrayHelper::getValue($v, 'kode');
                     $text = ArrayHelper::getValue($v, 'text');
                     if(!empty($kode) && !empty($text)) {
                        $diagnosaTindakanNama .= $kode.' - '.$text.' ';
                     }
                  }
               }
         }
         
         if(!empty($setDiagnosaPenyerta)) {
               $listDiagnosaPenyerta = json_decode($setDiagnosaPenyerta, true);
               if(!empty($listDiagnosaPenyerta)) {
                  foreach ($listDiagnosaPenyerta as $k => $v) {
                     $kode = ArrayHelper::getValue($v, 'kode');
                     $text = ArrayHelper::getValue($v, 'text');
                     if(!empty($kode) && !empty($text)) {
                        $diagnosaPenyertaNama .= $kode.' - '.$text.' ';
                     }
                  }
               }
         }
      ?>
         <tr>
               <td><?= $no++ ?></td>
               <td><?= $tglPendaftaran ?></td>
               <td><?= $tglPasienPulang ?></td>
               <td><?= ArrayHelper::getValue($value, 'nama_pasien').'<br/>Tanggal Lahir : '.$tanggalLahir.'<br/>No Pendaftaran : '.ArrayHelper::getValue($value, 'no_pendaftaran').'<br/>No Rekam Medik : '.ArrayHelper::getValue($value, 'no_rekam_medik') ?></td>
               <td><?= ArrayHelper::getValue($value, 'nosep') ?></td>
               <td><?= ArrayHelper::getValue($value, 'penjamin_nama') ?></td>
               <td><?= 'Ruangan : '. ArrayHelper::getValue($value, 'ruangan_nama').'<br/> Kamar/Bed : '.ArrayHelper::getValue($value, 'kamarruangan_nokamar').'/'.ArrayHelper::getValue($value, 'no_tempattidur').'<br/>Hak Kelas : '.ArrayHelper::getValue($value, 'hak_kelas') ?></td>
               <td><?= ArrayHelper::getValue($value, 'dokter_dpjp') ?></td>
               <td><?= ArrayHelper::getValue($value, 'set_diagnosautama') ?></td>
               <td><?= $diagnosaPenyertaNama ?></td>
               <td><?= $diagnosaTindakanNama ?></td>
               <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'tagihan_rs', 0)) ?></td>
               <td style="text-align: right;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($value, 'tarif_inacbg', 0)) ?></td>
               <td style="text-align: right;"><?= $persentase ?></td>
               <td><?= ArrayHelper::getValue($value, 'status_monitor') ?></td>
               <td><?= ArrayHelper::getValue($value, 'status_periksa') ?></td>
         </tr>
      <?php endforeach ?>
   </tbody>
</table>