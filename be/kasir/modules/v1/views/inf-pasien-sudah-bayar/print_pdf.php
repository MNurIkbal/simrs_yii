<?php

use Doco\components\DocoHelpers;

?>
<style>
    .tbl-header {
        border: 1px solid black;
        font-size: 12px;
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

    .border-bottom {
        border-bottom: 1px solid;
    }
</style>

<table width="100%" class="tbl-header">
    <tbody>
        <tr>
            <td style="width: 25%;">Tanggal Pendaftaran</td>
            <td style="width: 25%;">: <?= isset($detailPasien['tgl_pendaftaran']) ? date('d F Y', strtotime($detailPasien['tgl_pendaftaran'])) : '-' ?></td>
            <td style="width: 25%;">Kelas Pelayanan</td>
            <td style="width: 25%;">: <?= isset($detailPasien['kelaspelayanan_nama']) ? $detailPasien['kelaspelayanan_nama'] : '-' ?></td>
        </tr>
        <tr>
            <td style="width: 25%;">No Rekam Medik</td>
            <td style="width: 25%;">: <?= isset($detailPasien['no_rekam_medik']) ? $detailPasien['no_rekam_medik'] : '-'?></td>
            <td style="width: 25%;">Jenis Kasus Penyakit</td>
            <td style="width: 25%;">: <?= isset($detailPasien['jeniskasuspenyakit_nama']) ? $detailPasien['jeniskasuspenyakit_nama'] : '-'?></td>
        </tr>
        <tr>
            <td style="width: 25%;">No Pendaftaran</td>
            <td style="width: 25%;">: <?= isset($detailPasien['no_pendaftaran']) ? $detailPasien['no_pendaftaran'] : '-'?></td>
            <td style="width: 25%;">Dokter</td>
            <td style="width: 25%;">: <?= isset($detailPasien['pegawai_rd_rj']) ? $detailPasien['pegawai_rd_rj'] : '-'?></td>
        </tr>
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 25%;">: <?= isset($detailPasien['nama_pasien']) ? $detailPasien['nama_pasien'] : '-'?></td>
            <td style="width: 25%;">Ruangan</td>
            <td colspan="2"  style="width: 25%;">: <?= isset($detailPasien['ruangan_nama']) ? $detailPasien['ruangan_nama'] : '-' ?></td>
        </tr>
        <tr>
            <td style="width: 25%;">Cara Bayar</td>
            <td style="width: 25%;">: <?= isset($detailPasien['carabayar_nama']) ? $detailPasien['carabayar_nama'] : '-'  ?></td>
            <td style="width: 25%;">Status Bayar</td>
            <td style="width: 25%;">: <?= isset($detailPasien['status_bayar']) ? $detailPasien['status_bayar'] : '-'?></td>
        </tr>
        <tr>
            <td style="width: 25%;">Penjamin</td>
            <td style="width: 25%;">: <?= isset($detailPasien['penjamin_nama']) ? $detailPasien['penjamin_nama'] : '-'  ?></td>
        </tr>
    </tbody>
</table><hr>
<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <td colspan="2" style="font-size:13px;"><strong>Riwayat Pembayaran</strong></td>
        </tr>
        <tr>
            <td>Total Tagihan</td>
            <td><?= DocoHelpers::formatNumber($detailPasien['total_tagihan'])   ?></td>
        </tr>
        <tr>
            <td>Total Uang Muka</td>
            <td><?= DocoHelpers::formatNumber($detailPasien['total_uang_muka'])   ?></td>
        </tr>
        <tr>
            <td>Subsidi Asuransi</td>
            <td><?= DocoHelpers::formatNumber($detailPasien['subsidi_asuransi'])   ?></td>
        </tr>
        <tr>
            <td>Total Sudah Dibayarkan</td>
            <td><?= DocoHelpers::formatNumber($detailPasien['total_sudah_dibayarkan'])   ?></td>
        </tr>
        <tr>
            <td>Total Sisa Tagihan</td>
            <td><?= DocoHelpers::formatNumber($detailPasien['total_sisa_tagihan'])   ?></td>
        </tr>
        <tr>
            <td>Biaya Administrasi</td>
            <td><?= DocoHelpers::formatNumber($detailPasien['biaya_administrasi'])   ?></td>
        </tr>
        <tr>
            <td>Pembulatan</td>
            <td><?= DocoHelpers::formatNumber($detailPasien['pembulatan'])   ?></td>
        </tr>
    </thead>
</table><br>

<?php if($detailtindakan) : ?>
    <?php foreach($detailtindakan as $key => $value) : ?>
        <table width="100%" class="tbl-bordered" >
            <thead>
                <tr><th colspan="9" >Tindakan <?= str_replace('-', ' ', $key) ?></th></tr>
                <tr>
                    <th>No</th>
                    <th>Tanggal Tindakan</th>
                    <th>Nama Tindakan</th>
                    <th>Qty</th>
                    <th>Tarif Satuan</th>
                    <th>Tarif Cyto</th>
                    <th>Tarif Diskon</th>
                    <th>Tarif Dijamin</th>
                    <th>Tarif Dibayarkan</th>
                </tr>
            </thead>
            <tbody>
                <?php $totalCito = $totalDiskon = $totalDijamin = $totalDibayar = 0; $counter = 1; ?>
                <?php foreach ($value as $val) : 
                    $totalCito += $val['tarif_cyto']; 
                    $totalDiskon += $val['tarif_diskon'];
                    $totalDijamin += $val['tarif_dijamin'];
                    $totalDibayar += $val['tarif_dibayarkan'];
                    ?>
                    <tr>
                        <td class="center" style="width:5%"><?= $counter++ ?></td>
                        <td><?= date('d F Y H:i:s', strtotime($val['tgl_pelayanan'])) ?></td>
                        <td style="width:25%"><?= $val['tindakan_obat_nama']  ?></td>
                        <td class="number" style="width:5%"><?= $val['qty']  ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($val['tarif_satuan']) ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($val['tarif_cyto']) ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($val['tarif_diskon']) ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($val['tarif_dijamin']) ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($val['tarif_dibayarkan']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="font-weight: bold;text-align:right;background-color:#FCF3CF" class="border-bottom"><b>Total Tindakan <?= str_replace('-', ' ', $key) ?></b></td>
                    <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalCito) ?></b></td>
                    <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDiskon) ?></b></td>
                    <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDijamin) ?></b></td>
                    <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDibayar) ?></b></td>
                </tr>
            </tfoot>
        </table>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($detailobat): ?>
    <br>
    <table width="100%" class="tbl-bordered">
        <thead>
            <tr><th colspan="8">Obat</th></tr>
            <tr>
                <th>No</th>
                <th>Tanggal Order Obat</th>
                <th>Nama Obat</th>
                <th>Qty</th>
                <th>Tarif Satuan</th>
                <th>Tarif Diskon</th>
                <th>Tarif Dijamin</th>
                <th>Tarif Dibayarkan</th>
            </tr>
        </thead>
        <tbody>
            <?php $counter = 1; $totalDiskon = $totalDijamin = $totalDibayar = 0; foreach ($detailobat as $value): ?>
            <?php 
                $totalDiskon += $value['tarif_diskon'];
                $totalDijamin += $value['tarif_dijamin'];
                $totalDibayar += $value['tarif_dibayarkan']; 
            ?>
            <tr>
                <td class="center" style="width:5%"><?= $counter++ ?></td>
                <td><?= date('d F Y  H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
                <td style="width:25%"><?= $value['tindakan_obat_nama']  ?></td>
                <td style="width:5%" class="number"><?= $value['qty']  ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_satuan'])  ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_diskon']) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_dijamin']) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_dibayarkan']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="font-weight: bold;text-align:right;background-color:#FCF3CF" class="border-bottom">Total Obat</td>
                <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDiskon) ?></b></td>
                <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDijamin) ?></b></td>
                <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDibayar) ?></b></td>
            </tr>
        </tfoot>
</table>
<?php endif; ?>

<?php if ($detaillab): ?>
    <br>
    <table width="100%" class="tbl-bordered">
        <thead>
            <tr><th colspan="10">Pemeriksaan Laboratorium</th></tr>
            <tr>
                <th>No</th>
                <th>Tanggal Pemeriksaan</th>
                <th>Nama Pemeriksaan</th>
                <th>Qty</th>
                <th>Tarif Satuan</th>
                <th>Cito</th>
                <th>Tarif Cito</th>
                <th>Tarif Diskon</th>
                <th>Tarif Dijamin</th>
                <th>Tarif Dibayarkan</th>
            </tr>
        </thead>
        <tbody>
            <?php $totalCyto = $totalDiskon = $totalDijamin = $totalDibayar = 0; $counter = 1; foreach ($detaillab as $value): ?>
            <?php  
                $totalCyto += $value['tarif_cyto'];
                $totalDiskon += $value['tarif_diskon'];
                $totalDijamin += $value['tarif_dijamin'];
                $totalDibayar += $value['tarif_dibayarkan']; 
            ?>
            <tr>
                <td class="center" style="width:5%"><?= $counter++ ?></td>
                <td><?= date('d F Y  H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
                <td style="width:25%"><?= $value['tindakan_obat_nama']  ?></td>
                <td class="number" style="width:5%"><?= $value['qty']  ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_satuan'])  ?></td>
                <td style="width:5%"><?= $value['tarif_cyto'] ? 'Ya' : 'Tidak' ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_cyto'])   ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_diskon']) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_dijamin']) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_dibayarkan']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="font-weight: bold;text-align:right;background-color:#FCF3CF" class="border-bottom">Total Pemeriksaan Laboratorium</td>
                <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalCyto)   ?></b></td>
                <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDiskon) ?></b></td>
                <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDijamin) ?></b></td>
                <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDibayar) ?></b></td>
            </tr>
        </tfoot>
  </table>
<?php endif; ?>

<?php if ($detailradiologi): ?>
    <br>
    <table width="100%" class="tbl-bordered">
        <thead>
            <tr><th colspan="10">Pemeriksaan Radiologi</th></tr>
            <tr>
                <th>No</th>
                <th>Tanggal Pemeriksaan</th>
                <th>Nama Pemeriksaan</th>
                <th>Qty</th>
                <th>Tarif Satuan</th>
                <th>Cito </th>
                <th>Tarif Cito</th>
                <th>Tarif Diskon</th>
                <th>Tarif Dijamin</th>
                <th>Tarif Dibayarkan</th>
            </tr>
        </thead>
        <tbody>
            <?php $totalCyto = $totalDiskon = $totalDijamin = $totalDibayar = 0; $counter = 1; foreach ($detailradiologi as $value): ?>
            <?php  
                $totalCyto += $value['tarif_cyto'];
                $totalDiskon += $value['tarif_diskon'];
                $totalDijamin += $value['tarif_dijamin'];
                $totalDibayar += $value['tarif_dibayarkan']; 
            ?>
            <tr>
                <td class="center" style="width:5%"><?= $counter++ ?></td>
                <td><?= date('d F Y  H:i:s', strtotime($value['tgl_pelayanan'])) ?></td>
                <td style="width:25%"><?= $value['tindakan_obat_nama']  ?></td>
                <td class="number" style="width:5%"><?= $value['qty']  ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_satuan'])  ?></td>
                <td style="width:5%"><?= $value['tarif_cyto'] ? 'Ya' : 'Tidak' ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_cyto'])   ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_diskon']) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_dijamin']) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif_dibayarkan']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tr>
            <td colspan="6" style="font-weight: bold;text-align:right;background-color:#FCF3CF" class="border-bottom">Total Pemeriksaan Radiologi</td>
            <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalCyto)   ?></b></td>
            <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDiskon) ?></b></td>
            <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDijamin) ?></b></td>
            <td class="number border-bottom" style="background-color:#FCF3CF"><b><?= DocoHelpers::formatNumber($totalDibayar) ?></b></td>
        </tr>
    </table>
<?php endif; ?>

