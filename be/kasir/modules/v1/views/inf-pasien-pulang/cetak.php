<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-10-16 17:31:14
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-21 14:22:09
 */

use Doco\components\DocoHelpers;

$data['keyRj'] = 0;
$data['keyRi'] = 0;
$data['keyRd'] = 0;
$data['keyObat'] = 0;
$data['keyLab'] = 0;
$data['keyRadiologi'] = 0;
$data['keyGudang'] = 0;
$data['keyRehab'] = 0;
$data['keyRm'] = 0;
$data['keyKasir'] = 0;
$data['keyInformasi'] = 0;
$data['keyPendaftaran'] = 0;
$data['keyBedah'] = 0;
$dataTindakan = [];
for ($i=0; $i < count($data['tagihan']) ; $i++) {
  if ($data['tagihan'][$i]['instalasi_pelayanan'] == 'Rawat Jalan' || $data['tagihan'][$i]['instalasi_pelayanan'] == 'Rawat Darurat' || $data['tagihan'][$i]['instalasi_pelayanan'] == 'Rawat Inap') {
        $dataTindakan[str_replace(' ', '-', $data['tagihan'][$i]['ruangan_pelayanan'])][] = $data['tagihan'][$i];
  }
  switch ($data['tagihan'][$i]['is_obat']) {
    case true:
      $data['keyObat'] = 1;
      break;
    default:
        switch ($data['tagihan'][$i]['instalasi_pelayanan']) {
            case 'Rawat Jalan':
                $data['keyRj'] = 1;
                break;
            case 'Rawat Darurat':
                $data['keyRd'] = 1;
                break;
            case 'Rawat Inap':
                $data['keyRi'] = 1;
                break;
            case 'Radiologi':
                $data['keyRadiologi'] = 1;
                break;
            case 'Laboratorium':
                $data['keyLab'] = 1;
                break;
            case 'Gudang Farmasi':
                $data['keyGudang'] = 1;
                break;
            case 'Rehabilitasi Medik':
                $data['keyRehab'] = 1;
                break;
            case 'Rekam Medik':
                $data['keyRm'] = 1;
                break;
            case 'Kasir':
                $data['keyKasir'] = 1;
                break;
            case 'Informasi':
                $data['keyInformasi'] = 1;
                break;
            case 'Pendaftaran & Penjadwalan':
                $data['keyPendaftaran'] = 1;
                break;
            case 'Bedah Sentral':
                $data['keyBedah'] = 1;
                break;
            case 'Ambulan':
                $data['keyAmbulan'] = 1;
                break;
        }
  }
}

?>
<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    tr:nth-child(even) {
        background-color: white;
    }

    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        font-weight: bold;
    }

    td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    .heading {
        font-size: 14pt;
        font-weight: bold;
        text-align: center;
    }

    .noborder tr, .noborder th, .noborder td {
        border: 0px;
        font-size: 12px !important;
    }

    .row:before,
    .row:after {
        content: "";
        display: table;
        clear: both;
    }

    .col-print-1 {width:8%;  float:left;}
    .col-print-2 {width:16%; float:left;}
    .col-print-3 {width:25%; float:left;}
    .col-print-4 {width:33%; float:left;}
    .col-print-5 {width:42%; float:left;}
    .col-print-6 {width:50%; float:left;}
    .col-print-7 {width:58%; float:left;}
    .col-print-8 {width:66%; float:left;}
    .col-print-9 {width:75%; float:left;}
    .col-print-10 {width:83%; float:left;}
    .col-print-11 {width:92%; float:left;}
    .col-print-12 {width:100%; float:left;}

    .text-center {
        text-align: center;
    }
</style>
<div class="heading">Informasi Pasien</div>
<div class="row">
    <div class="col-print-4">
        <table class="table noborder" style="font-size: 11px">
            <tr>
                <td>Tanggal Pendaftaran</td>
                <td>:</td>
                <td><?= isset($data['pasien']['tglpasienpulang']) ? date('d M Y', strtotime($data['pasien']['tglpasienpulang'])) : '-' ?></td>  
            </tr>
            <tr>
                <td>No. Rekam Medik</td>
                <td>:</td>
                <td><?= isset($data['pasien']['no_rekam_medik']) ? $data['pasien']['no_rekam_medik'] : '-' ?></td>
            </tr>
            <tr>
                <td>No. Pendaftaran</td>
                <td>:</td>
                <td><?= isset($data['pasien']['no_pendaftaran']) ? $data['pasien']['no_pendaftaran'] : '-' ?></td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>:</td>
                <td><?= isset($data['pasien']['nama_pasien']) ? $data['pasien']['nama_pasien'] : '-' ?></td>
            </tr>
        </table>
    </div>
    <div class="col-print-4">
        <table class="table noborder">
            <tr>
                <td>Cara Bayar</td>
                <td>:</td>
                <td><?= isset($data['pasien']['carabayar_nama']) ? $data['pasien']['carabayar_nama'] : '-' ?></td>
            </tr>
            <tr>
                <td>Jenis Kasus Penyakit</td>
                <td>:</td>
                <td><?= isset($data['pasien']['jeniskasuspenyakit_nama']) ? $data['pasien']['jeniskasuspenyakit_nama'] : '-' ?></td>
            </tr>
            <tr>
                <td>Dokter</td>
                <td>:</td>
                <td><?= isset($data['pasien']['dokter']) ? $data['pasien']['dokter'] : '-' ?></td>
            </tr>
            <tr>
                <td>Ruangan</td>
                <td>:</td>
                <td><?= isset($data['pasien']['ruangan_nama']) ? $data['pasien']['ruangan_nama'] : '-' ?></td>
            </tr>
        </table>
    </div>
    <div class="col-print-4">
        <table class="table noborder">
            <tr>
                <td>Penjamin</td>
                <td>:</td>
                <td><?= isset($data['pasien']['penjamin_nama']) ? $data['pasien']['penjamin_nama'] : '-' ?></td>
            </tr>
            <tr>
                <td>Kelas Pelayanan</td>
                <td>:</td>
                <td><?= isset($data['pasien']['kelaspelayanan_nama']) ? $data['pasien']['kelaspelayanan_nama'] : '-' ?></td>
            </tr>
            <tr>
                <td>Status Bayar</td>
                <td>:</td>
                <td><?= isset($data['pasien']['status_bayar']) ? $data['pasien']['status_bayar'] : '-' ?></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
</div>
<br>
<?php 
if($dataTindakan):

  foreach ($dataTindakan as $key => $v) {
  ?>
  <div class="col-print-12">
    <div class="heading">Tindakan - <?= str_replace('-', ' ', $key) ?></div>
  <table class="table datatable-basic table-hover dataTable" id="tb-tindakan-inap" style="width: 100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th>Tanggal Tindakan</th>
                <th>Nama Tindakan</th>
                <th>Qty</th>
                <th>Tarif Satuan (Rp.)</th>
                <th>Tarif Cyto (Rp.)</th>
                <th>Jumlah Tarif (Rp.)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['tagihan'])):
                // Declare some variables
                $no = 1;
                $subtotal = 0;

                // Loop data
                foreach ($dataTindakan[$key] as $k => $value):
                    // Check value
                        if($value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total'])){
                            $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                        }
                        ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                            <td><?= $value['tindakan_obat_nama'] ?></td>
                            <td><?= $value['qty'] ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                        </tr>
                        <?php $no++;
                        $subtotal = $subtotal + $value['sub_total'];
                endforeach;
            else: ?>
                <p>Tidak ada data yang tersedia</p>
            <?php endif ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-center">Total</th>
                <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
            </tr>
        </tfoot>
    </table>
  </div>
  <br>
  <?php
  }
  ?>
<?php endif; ?>

<!-- Info obat -->
<?php if (!empty($data['obat'])): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Obat</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-obat" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th>Tanggal Order Obat</th>
                        <th>Nama Obat</th>
                        <th>Qty</th>
                        <th>Tarif Satuan (Rp.)</th>
                        <th>Jumlah Tarif (Rp.)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['obat'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['obat'] as $key => $value):
                            // Check value
                            if ($value['is_obat'] == true): ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info pemeriksaan lab -->
<?php if ($data['keyLab']==1): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Permeriksaan Laboratorium</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-tindakan-lab" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th>Tanggal Pemeriksaan</th>
                        <th>Nama Pemeriksaan</th>
                        <th>Qty</th>
                        <th>Tarif Satuan (Rp.)</th>
                        <th>Tarif Cyto (Rp.)</th>
                        <th>Jumlah (Rp.)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['tagihan'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['tagihan'] as $key => $value):
                            // Check value
                            if ($value['instalasi_pelayanan'] == 'Laboratorium' && $value['is_obat'] == false): 
                                if($value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total'])){
                                    $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                                }
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info pemeriksaan radiologi -->
<?php if ($data['keyRadiologi']==1): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Pemeriksaan Radiologi</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-tindakan-radiologi" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                      <th width="1">No</th>
                      <th>Tanggal Pemeriksaan</th>
                      <th>Nama Pemeriksaan</th>
                      <th>Qty</th>
                      <th>Tarif Satuan (Rp.)</th>
                      <th>Tarif Cyto (Rp.)</th>
                      <th>Jumlah (Rp.)</th>
                </thead>
                <tbody>
                    <?php if (!empty($data['tagihan'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['tagihan'] as $key => $value):
                            // Check value
                            if ($value['instalasi_pelayanan'] == 'Radiologi' && $value['is_obat'] == false): 
                                if($value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total'])){
                                    $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                                }
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info pemeriksaan gudang -->
<?php if ($data['keyGudang']==1): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Gudang Farmasi</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-gudang" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                      <th width="1">No</th>
                      <th>Tanggal Pemeriksaan</th>
                      <th>Nama Pemeriksaan</th>
                      <th>Qty</th>
                      <th>Tarif Satuan (Rp.)</th>
                      <th>Tarif Cyto (Rp.)</th>
                      <th>Jumlah (Rp.)</th>
                </thead>
                <tbody>
                    <?php if (!empty($data['tagihan'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['tagihan'] as $key => $value):
                            // Check value
                            if ($value['instalasi_pelayanan'] == 'Gudang Farmasi' && $value['is_obat'] == false): 
                                if($value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total'])){
                                    $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                                }
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info pemeriksaan rehab -->
<?php if ($data['keyRehab']==1): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Tindakan - Rehabilitasi Medik</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-rehab" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                      <th width="1">No</th>
                      <th>Tanggal Pemeriksaan</th>
                      <th>Nama Pemeriksaan</th>
                      <th>Qty</th>
                      <th>Tarif Satuan (Rp.)</th>
                      <th>Tarif Cyto (Rp.)</th>
                      <th>Jumlah (Rp.)</th>
                </thead>
                <tbody>
                    <?php if (!empty($data['tagihan'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['tagihan'] as $key => $value):
                            // Check value
                            if ($value['instalasi_pelayanan'] == 'Rehabilitasi Medik' && $value['is_obat'] == false): 
                                if($value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total'])){
                                    $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                                }
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info pemeriksaan rm -->
<?php if ($data['keyRm']==1): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Pemeriksaan Radiologi</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-rm" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                      <th width="1">No</th>
                      <th>Tanggal Pemeriksaan</th>
                      <th>Nama Pemeriksaan</th>
                      <th>Qty</th>
                      <th>Tarif Satuan (Rp.)</th>
                      <th>Tarif Cyto (Rp.)</th>
                      <th>Jumlah (Rp.)</th>
                </thead>
                <tbody>
                    <?php if (!empty($data['tagihan'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['tagihan'] as $key => $value):
                            // Check value
                            if ($value['instalasi_pelayanan'] == 'Rekam Medik' && $value['is_obat'] == false): 
                                if($value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total'])){
                                    $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                                }
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info pemeriksaan kasir -->
<?php if ($data['keyKasir']==1): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Kasir</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-kasir" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                      <th width="1">No</th>
                      <th>Tanggal Pemeriksaan</th>
                      <th>Nama Pemeriksaan</th>
                      <th>Qty</th>
                      <th>Tarif Satuan (Rp.)</th>
                      <th>Tarif Cyto (Rp.) </th>
                      <th>Jumlah (Rp.)</th>
                </thead>
                <tbody>
                    <?php if (!empty($data['tagihan'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['tagihan'] as $key => $value):
                            // Check value
                            if ($value['instalasi_pelayanan'] == 'Kasir' && $value['is_obat'] == false): 
                                if($value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total'])){
                                    $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                                }
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info pemeriksaan informasi -->
<?php if ($data['keyInformasi']==1): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Informasi</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-informasi" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                      <th width="1">No</th>
                      <th>Tanggal Pemeriksaan</th>
                      <th>Nama Pemeriksaan</th>
                      <th>Qty</th>
                      <th>Tarif Satuan (Rp.)</th>
                      <th>Tarif Cyto (Rp.)</th>
                      <th>Jumlah (Rp.)</th>
                </thead>
                <tbody>
                    <?php if (!empty($data['tagihan'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['tagihan'] as $key => $value):
                            // Check value
                            if ($value['instalasi_pelayanan'] == 'Informasi' && $value['is_obat'] == false): 
                                if($value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total'])){
                                    $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                                }
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info pemeriksaan pendaftaran -->
<?php if ($data['keyPendaftaran']==1): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Pendaftaran & Penjadwalan</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-pendaftaran" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                      <th width="1">No</th>
                      <th>Tanggal Pemeriksaan</th>
                      <th>Nama Pemeriksaan</th>
                      <th>Qty</th>
                      <th>Tarif Satuan (Rp.)</th>
                      <th>Tarif Cyto (Rp.)</th>
                      <th>Jumlah (Rp.)</th>
                </thead>
                <tbody>
                    <?php if (!empty($data['tagihan'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['tagihan'] as $key => $value):
                            // Check value
                            if ($value['instalasi_pelayanan'] == 'Pendaftaran & Penjadwalan' && $value['is_obat'] == false): 
                                if($value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total'])){
                                    $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                                }
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info pemeriksaan bedah -->
<?php if ($data['keyBedah']==1): ?>
    <div class="row">
        <div class="col-print-12">
            <div class="heading">Bedah Sentral</div>
            <table class="table datatable-basic table-hover dataTable" id="tb-bedah" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                      <th width="1">No</th>
                      <th>Tanggal Pemeriksaan</th>
                      <th>Nama Pemeriksaan</th>
                      <th>Qty</th>
                      <th>Tarif Satuan (Rp.)</th>
                      <th>Tarif Cyto (Rp.)</th>
                      <th>Jumlah (Rp.)</th>
                </thead>
                <tbody>
                    <?php if (!empty($data['tagihan'])):
                        // Declare some variables
                        $no = 1;
                        $subtotal = 0;

                        // Loop data
                        foreach ($data['tagihan'] as $key => $value):
                            // Check value
                            if ($value['instalasi_pelayanan'] == 'Bedah Sentral' && $value['is_obat'] == false): 
                                if( $value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total']) ){
                                    $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                                }
                                ?>

                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                    <td><?= $value['tindakan_obat_nama'] ?></td>
                                    <td><?= $value['qty'] ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                    <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                                </tr>
                                <?php $no++;
                                $subtotal = $subtotal + $value['sub_total'];
                            endif;
                        endforeach;
                    else: ?>
                        <p>Tidak ada data yang tersedia</p>
                    <?php endif ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-center">Total</th>
                        <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?><br>

<!-- Info tindakan ambulan -->
<?php if (!empty($data['ambulan'])): ?>
<div class="row">
    <div class="col-print-12">
        <div class="heading">Ambulan</div>
        <table class="table datatable-basic table-hover dataTable" id="tb-ambulan" style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                <th width="1">No</th>
                <th>Tanggal Tindakan</th>
                <th>Nama Tindakan</th>
                <th>Qty</th>
                <th>Tarif Satuan (Rp.)</th>
                <th>Tarif Cyto (Rp.)</th>
                <th>Jumlah Tarif (Rp.)</th>
            </thead>
            <tbody>
                <?php if (!empty($data['ambulan'])):
                    // Declare some variables
                    $no = 1;
                    $subtotal = 0;

                    // Loop data
                    foreach ($data['ambulan'] as $key => $value):
                        // Check value
                        if ($value['is_obat'] == false): 
                            if( $value['cyto_tindakan'] == 1 && ($value['tarif_satuan'] == $value['sub_total']) ){
                                $value['sub_total'] = ($value['tarif_satuan']+$value['tarifcyto_tindakan'])*$value['qty'];
                            }
                            ?>

                            <tr>
                                <td><?= $no ?></td>
                                <td><?= date('d F Y', strtotime($value['tgl_pelayanan'])) ?></td>
                                <td><?= $value['tindakan_obat_nama'] ?></td>
                                <td><?= $value['qty'] ?></td>
                                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_satuan']) ?></td>
                                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarifcyto_tindakan']) ?></td>
                                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['sub_total']) ?></td>
                            </tr>
                            <?php $no++;
                            $subtotal = $subtotal + $value['sub_total'];
                        endif;
                    endforeach;
                
                else: ?>
                    <p>Tidak ada data yang tersedia</p>
                <?php endif ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-center">Total</th>
                    <th style="text-align: right;"><?= DocoHelpers::formatNumber($subtotal) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endif; ?><br>
