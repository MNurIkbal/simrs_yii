<?php 
use Doco\components\DocoHelpers;

?>

<style>
body {
    letter-spacing: 1px;
    font-family: "Arial, Helvetica, sans-serif";
}
.header {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    letter-spacing: 1px;
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

/*body,td,input,select {
    font-family: Tahoma;
    font-size: 13px;
    color: #000000;
}*/

hr.new1 {
    border: none;
    height: 2px;
    color: #333;
    background-color: #333;
}
</style>

<table align="center" border="0" style="width:100%;" cellspacing="1">
    <tbody>
    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>
    <?php if(!empty($data_akomodasi)) : ?>
        <?php foreach ($data_akomodasi as $ruangan => $value) : ?>
            <tr>
                <td colspan="9" class="tabel_data_group">
                    <strong><?= strtoupper($ruangan) ?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="9" class="tabel_data_group">
                    <strong>AKOMODASI SEMENTARA</strong>
                </td>
            </tr>
        <?php
            foreach ($value as $values) : 
                $tarif_tindakan = isset($values['tarif_tindakan']) ? $values['tarif_tindakan'] : 0;
                $tarif_satuan = isset($values['tarif_satuan']) ? $values['tarif_satuan'] : 0;
                $tarifcyto_tindakan = isset($values['tarifcyto_tindakan']) ? $values['tarifcyto_tindakan'] : 0;
                $tgl_pelayanan = !empty($values['tgl_tindakan']) ? date('d/m/Y', strtotime($values['tgl_tindakan'])) : '-';
                $daftartindakan_nama = !empty($values['daftartindakan_nama']) ? $values['daftartindakan_nama'] : ''; 
                $ruangan_nama = isset($values['ruangan_nama']) ? $values['ruangan_nama'] : ''; 
                $kelaspelayanan_nama = isset($values['kelaspelayanan_nama']) ? $values['kelaspelayanan_nama'] : ''; 
                $tempatTidur = $ruangan_nama;
                $qty = isset($values['qty_tindakan']) ? $values['qty_tindakan'] : ''; 
                $daftartindakan_kode = isset($values['daftartindakan_kode']) ? $values['daftartindakan_kode'] : '';
                $dokter_nama = isset($values['dokter_nama']) ? $values['dokter_nama'] : '';
        ?>
            <tr>
                <td class="tabel_data" style="width: 10%"><?= $tgl_pelayanan ?></td>
                <td class="tabel_data" style="width: 10%"><?= $tempatTidur ?></td>
                <td class="tabel_data" style="width: 10%"><?= $kelaspelayanan_nama ?></td>
                <td class="tabel_data" style="width: 15%"><?= $dokter_nama ?></td>
                <td class="tabel_data" style="width: 10%"><?= $daftartindakan_kode ?></td>
                <td class="tabel_data" style="width: 17%"><?= $daftartindakan_nama ?></td>
                <td class="tabel_data" style="width: 5%"><?= $qty ?></td>
                <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($tarif_satuan) ?></td>
                <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($tarif_tindakan) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
        <tr>
            <td colspan="8" class="tabel_data_group"><b>TOTAL AKOMODASI SEMENTARA : </b></td>
            <td class="tabel_data_group_numeric"><b><?= DocoHelpers::formatNumber($total_akomodasi) ?></b></td>
        </tr>
        <tr>
            <td colspan="9">&nbsp;</td>
        </tr>
    <?php endif; ?>
    <?php if($biayaAdmin > 0) : ?>
        <tr>
            <td colspan="9" class="tabel_data_group">
                <!-- ini penyesuaian  -->
                <strong>BIAYA ADMINISTRASI</strong>
            </td>
        </tr>
        <tr>
            <td class="tabel_data" style="width: 10%"><?= $tgl_pulang ?></td>
            <td class="tabel_data" style="width: 10%">&nbsp;</td>
            <td class="tabel_data" style="width: 10%">&nbsp;</td>
            <td class="tabel_data" style="width: 15%">&nbsp;</td>
            <td class="tabel_data" style="width: 10%">&nbsp;</td>
            <td class="tabel_data" style="width: 17%"><?= !empty($data_admin) ? $data_admin : ''  ?></td>
            <td class="tabel_data" style="width: 5%">1</td>
            <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
            <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
        </tr>
        <tr>
            <td colspan="9">&nbsp;</td>
        </tr>
        <?php endif; ?>
        <?php foreach ($data as $ruangan => $kelompok) : ?>
            <tr>
                <td colspan="9" class="tabel_data_group"><?= strtoupper($ruangan) ?></td>
            </tr>
            <?php foreach ($kelompok as $key => $value) : ?>
            <tr>
                <td colspan="9" class="tabel_data_group">
                    <strong><?= strtoupper($key) ?></strong>
                </td>
            </tr>
            <?php 
            $total = 0; 
            foreach ($value as $val) : 
                $tempatTidur = !empty($val['kamar']) ? $val['kamar'] : ''; 
                $kelaspelayanan = !empty($val['kelaspelayanan_nama']) ? $val['kelaspelayanan_nama'] : '-';
                $tgl_pelayanan = !empty($val['tgl_pelayanan']) ? date('d/m/Y', strtotime($val['tgl_pelayanan'])) : '-';
                $dok_admisi = !empty($val['dok_admisi']) ? $val['dok_admisi'] : '';
                $kode = !empty($val['kode']) ? $val['kode'] : '';
                $tindakan = !empty($val['tindakan_obat_nama']) ? $val['tindakan_obat_nama'] : '';
                ?>
                <tr>
                    <td class="tabel_data" style="width: 10%"><?= $tgl_pelayanan ?></td>
                    <td class="tabel_data" style="width: 10%"><?= $tempatTidur ?></td>
                    <td class="tabel_data" style="width: 10%"><?= $kelaspelayanan ?></td>
                    <td class="tabel_data" style="width: 15%"><?= $dok_admisi ?></td>
                    <td class="tabel_data" style="width: 10%"><?= $kode ?></td>
                    <td class="tabel_data" style="width: 17%"><?= $tindakan ?></td>
                    <td class="tabel_data" style="width: 5%"><?= !empty($val['qty']) ? $val['qty'] : 0; ?></td>
                    <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($val['tarif_satuan']) ?></td>
                    <td class="tabel_data_numeric" style="width: 12%"><?= DocoHelpers::formatNumber($val['jumlah_tarif']) ?></td>
                </tr>
                <?php $total = $total + $val['jumlah_tarif']; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="8" class="tabel_data_group">SUB TOTAL <?= strtoupper($key) ?></td>
                <td class="tabel_data_group_numeric">
                    <?= DocoHelpers::formatNumber($total) ?></td>
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
            <td colspan="8" class="tabel_data">GRAND TOTAL</td>
            <td class="tabel_data_group_numeric">
            <?= DocoHelpers::formatNumber($grandTotal) ?> </td>
        </tr>
    </tfoot>
</table>
<hr class="new1">
