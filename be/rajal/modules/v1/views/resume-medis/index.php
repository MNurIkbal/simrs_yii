<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-01-21 16:56:35
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-15 10:18:43
 */
use Doco\components\DocoHelpers;

?>
<style type="text/css">
    ul.dash {
        list-style: none;
        margin-left: 0;
        padding-left: 1em;
    }
    ul.dash > li:before {
        display: inline-block;
        content: "-";
        width: 1em;
        margin-left: -1em;
    }
    .text-center{
        text-align: center;
    }
    .head-title{
        margin-bottom: -5px;
    }
    .tbl{
        border-collapse: collapse;
    }
    .tbl-no-border th{
        border: 0px;
        padding: 5px;
    }
    .tbl-no-border td{
        border: 0px;
        padding: 5px;
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
<h3 class="head-title">Diagnosa</h3>
<hr>
<table class="tbl tbl-bordered" width="100%">
  <tr>
    <th>No</th>
    <th>Kelompok Diagnosa</th>
    <th>Kode Diagnosa</th>
    <th>Diagnosa Nama</th>
  </tr>
  <?php $counter=1; 
  if(count($diagnosa) > 0){
  foreach ($diagnosa as $value):
    $diagnosadata = json_decode($value['diagnosa_pasien'], true);
  ?>
  <tr>
    <td><?= $counter++ ?></td>
    <td><?= $value['kelompokdiagnosa_nama'] ?></td>
    <td><?= isset($diagnosadata['kode']) ? $diagnosadata['kode'] : null ?></td>
    <td><?= isset($diagnosadata['text']) ? $diagnosadata['text'] : null ?></td>
  </tr>
  <?php endforeach; }else{
    ?>
    <tr>
        <td class="text-center" colspan="4">Data Tidak Tersedia</td>
    </tr>
    <?php
  }?>
</table>
<br>
<h3 class="head-title">Catatan tambahan</h3>
<hr>
<table class="tbl tbl-bordered" style="width: 100%">
    <tr>
        <td style="height: 80px"></td>
    </tr>
</table>
<br>
<h3 class="head-title">Anamnesa dan Pemeriksaan Fisik</h3>
<hr>
<table class="tbl tbl-no-border" width="100%">
    <tr>
        <th width="25%">Tekanan Darah</th>
        <th width="25%">Pernafasan</th>
        <th width="25%">Berat Badan</th>
        <th width="25%">Tinggi Badan</th>
    </tr>
    <tr>
        <td class="text-center"><?=isset($fisik['tekanandarah']) ? $fisik['tekanandarah'].' MmHg' : '-'?></td>
        <td class="text-center"><?=isset($fisik['pernapasan']) ? $fisik['pernapasan'].' x/m' : '-'?></td>
        <td class="text-center"><?=isset($fisik['beratbadan_kg']) ? $fisik['beratbadan_kg'].' Kg' : '-'?></td>
        <td class="text-center"><?=isset($fisik['tinggibadan_cm']) ? $fisik['tinggibadan_cm'].' cm' : '-'?></td>
    </tr>
    <tr>
        <th><br>Nadi</th>
        <th><br>Suhu</th>
        <th><br>Nyeri</th>
        <th><br>Resiko Jatuh</th>
    </tr>
    <tr>
        <td class="text-center"><?=isset($fisik['detaknadi']) ? $fisik['detaknadi'].' MmHg' : '-'?></td>
        <td class="text-center"><?=isset($fisik['suhutubuh']) ? $fisik['suhutubuh'].' x/m' : '-'?></td>
        <td class="text-center"><?=isset($fisik['anamnesa']['is_nyeri']) ? $fisik['anamnesa']['is_nyeri'] ? \Yii::t('app', 'Ya') .', Skala '.$fisik['anamnesa']['skala_nyeri']  : \Yii::t('app', 'Tidak') : '-'?></td>
        <td class="text-center"><?=isset($fisik['anamnesa']['is_resikojatuh']) ? ($fisik['anamnesa']['is_resikojatuh']) ? Yii::t('app', 'Ya') : Yii::t('app', 'Tidak') : '-'?></td>
    </tr>
</table>
<br>
<h3 class="head-title">Terapi</h3>
<hr>
<h4 class="head-title">Catatan</h4>
<hr>
<table class="tbl tbl-bordered" style="width: 100%">
    <tr>
        <td style="height: 50px"></td>
    </tr>
</table>
<br>
<table class="tbl tbl-no-border" style="width: 100%">
    <tr>
        <td valign="top">
            <h4 class="head-title">Tindakan Medis</h4>
            <hr>
            <table class="tbl tbl-bordered" width="100%">
                <tr>
                    <th>No</th>
                    <th>Nama Tindakan / Paket</th>
                    <th>Jumlah</th>
                </tr>
                <?php $counter=1; 
                if(count($terapi['tindakan']) > 0){
                foreach ($terapi['tindakan'] as $value):
                $detailtindakan = !empty($value['daftartindakan_namapaket']) ? explode(',', $value['daftartindakan_namapaket']) : [];
                $detailtindakantxt = '';
                if(isset($detailtindakan[0])){
                    $detailtindakantxt .= "<ul class='dash'>";
                    foreach ($detailtindakan as $val) {
                        $detailtindakantxt .= "<li>".$val."</li>";
                    }
                    $detailtindakantxt .= "</ul>";
                }
                ?>
                <tr>
                    <td><?= $counter++ ?></td>
                    <td><?= $value['tindakan_obat'].' '.$detailtindakantxt ?></td>
                    <td><?= $value['qty']?></td>
                </tr>
                <?php endforeach; }else{
                ?>
                <tr>
                    <td class="text-center" colspan="4">Data Tidak Tersedia</td>
                </tr>
                <?php
                }?>
            </table>
        </td>
        <td valign="top">
        <h4 class="head-title">Pemakaian Alkes / Obat</h4>
        <hr>
        <table class="tbl tbl-bordered" width="100%">
            <tr>
                <th>No</th>
                <th>Nama Tindakan</th>
                <th>Obat / Alkes</th>
                <th>Jumlah</th>
            </tr>
            <?php $counter=1; 
            if(count($terapi['bmhp']) > 0){
            foreach ($terapi['bmhp'] as $value):
            ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= $value['tindakan'] ?></td>
                <td><?= $value['tindakan_obat'] ?></td>
                <td><?= $value['qty']?></td>
            </tr>
            <?php endforeach; }else{
            ?>
            <tr>
                <td class="text-center" colspan="4">Data Tidak Tersedia</td>
            </tr>
            <?php
            }?>
        </table>
        </td>
    </tr>
</table>
<br>
<h3 class="head-title">Penunjang</h3>
<hr>

<h4 class="head-title">Laboratorium</h4>
<hr>
<table class="tbl tbl-bordered" width="100%">
  <tr>
    <th>No</th>
    <th width="15%">Tanggal Pemeriksaan</th>
    <th>Jenis Pemeriksaan</th>
    <th>Nama Pemeriksaan</th>
  </tr>
  <?php $counter=1; 
  if(count($lab)){
  foreach ($lab as $value):
  ?>
  <tr>
    <td width="1"><?= $counter++ ?></td>
    <td><?= DocoHelpers::convDateTime($value['tgl_tindakan']) ?></td>
    <td><?= str_replace('_', ' ', $value['jenis']) ?></td>
    <td><?= $value['daftartindakan_nama'] ?></td>
  </tr>
  <?php endforeach; } else{
    ?>
    <tr>
        <td colspan="4" class="text-center">Data Tidak Tersedia</td>
    </tr>
    <?php
  }?>
</table><br>
<h4 class="head-title">Radiologi</h4>
<hr>
<table class="tbl tbl-bordered" width="100%">
  <tr>
    <th>No</th>
    <th width="15%">Tanggal Pemeriksaan</th>
    <th>Jenis Pemeriksaan</th>
    <th>Nama Pemeriksaan</th>
  </tr>
  <?php $counter=1; 
  if(count($radiologi)){
  foreach ($radiologi as $value):
  ?>
  <tr>
    <td width="1"><?= $counter++ ?></td>
    <td><?= DocoHelpers::convDateTime($value['tgl_tindakan']) ?></td>
    <td><?= str_replace('_', ' ', $value['jenis']) ?></td>
    <td><?= $value['daftartindakan_nama'] ?></td>
  </tr>
  <?php endforeach; } else{
    ?>
    <tr>
        <td colspan="4" class="text-center">Data Tidak Tersedia</td>
    </tr>
    <?php
  }?>
</table><br>
<h4 class="head-title">Bedah</h4>
<hr>
<table class="tbl tbl-bordered" width="100%">
  <tr>
    <th>No</th>
    <th width="15%">Tanggal Pemeriksaan</th>
    <th>Jenis Pemeriksaan</th>
    <th>Nama Pemeriksaan</th>
  </tr>
  <?php $counter=1; 
  if(count($bedah)){
  foreach ($bedah as $value):
  ?>
  <tr>
    <td width="1"><?= $counter++ ?></td>
    <td><?= DocoHelpers::convDateTime($value['tgl_tindakan']) ?></td>
    <td><?= str_replace('_', ' ', $value['jenis']) ?></td>
    <td><?= $value['daftartindakan_nama'] ?></td>
  </tr>
  <?php endforeach; } else{
    ?>
    <tr>
        <td colspan="4" class="text-center">Data Tidak Tersedia</td>
    </tr>
    <?php
  }?>
</table><br>
<h4 class="head-title">Rehab Medis</h4>
<hr>
<table class="tbl tbl-bordered" width="100%">
  <tr>
    <th>No</th>
    <th width="15%">Tanggal Pemeriksaan</th>
    <th>Jenis Pemeriksaan</th>
    <th>Nama Pemeriksaan</th>
  </tr>
  <tr>
    <td colspan="4" class="text-center">Data Tidak Tersedia</td>
  </tr>
</table><br>
<h3 class="head-title">Obat</h3>
<hr>
<table class="tbl tbl-bordered" width="100%">
  <tr>
    <th>No</th>
    <th width="15%">Racikan / Non Racikan</th>
    <th>R Ke-</th>
    <th>Nama Obat</th>
    <th>Satuan Kecil</th>
    <th>Signa</th>
    <th>Qty</th>
  </tr>
  <?php $counter=1; 
  foreach ($obat as $value):
  ?>
  <tr>
    <td><?= $counter++ ?></td>
    <td><?= $value['racikan_nama'] ?></td>
    <td><?= $value['rke'] ?></td>
    <td><?= $value['obatalkes_nama'] ?></td>
    <td><?= $value['satuan_kecil'] ?></td>
    <td><?= $value['signa_nama'] ?></td>
    <td><?=DocoHelpers::formatNumber($value['qty_reseptur']);?></td>
  </tr>
  <?php endforeach; ?>
</table><br>