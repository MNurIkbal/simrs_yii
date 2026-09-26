<?php 
use Doco\components\DocoHelpers;

?>

<style>
.header {
    font-family: Tahoma;
    font-size: 11px;
    letter-spacing: 1px;
}

.tabel_data {
    font-family: Tahoma;
    font-size:12px;
}

.tabel_data_numeric {
    font-family: Tahoma;
    font-size:12px;
    text-align: right;
}

.tabel_data_group {
    font-family: Tahoma;
    font-size:12px;
    font-weight:bold;
}

.tabel_data_group_numeric {
    font-family: Tahoma;
    font-size:12px;
    font-weight:bold;
    text-align: right;
}

.tabel_header {
    font-family: Tahoma;
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

<hr class="new1">
<table align="center" style="width:100%;">
    <thead>
        <tr>
            <td class="header">Tanggal</td>
            <td class="header">Kamar</td>
            <td class="header">Kelas</td>
            <td class="header">Dokter</td>
            <td class="header">Kode Biaya</td>
            <td class="header">Nama Biaya</td>
            <td class="header">Qty</td>
            <td class="header">Harga</td>
            <td class="header">Subtotal</td>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<hr class="new1"> <br>

<table align="center" border="0" style="width:100%;" cellspacing="1">
    <tbody >
        <?php foreach ($data as $key => $value) : ?>
            <tr>
                <td colspan="9" class="tabel_data_group"><?= $key ?></td>
            </tr>
            <?php $total = 0; foreach ($value as $val) : 
                $tempatTidur = !empty($val['tempat_tidur']) ? $val['tempat_tidur'] : ''; 
                $kelaspelayanan = !empty($val['kelaspelayanan_nama']) ? $val['kelaspelayanan_nama'] : '-';
                $tgl_pelayanan = !empty($val['tgl_pelayanan']) ? date('d-m-y', strtotime($val['tgl_pelayanan'])) : '-';
                $dok_admisi = !empty($val['dok_admisi']) ? $val['dok_admisi'] : '';
                $kode = !empty($val['kode']) ? $val['kode'] : '';
                $tindakan = !empty($val['tindakan_obat_nama']) ? $val['tindakan_obat_nama'] : '';
                ?>
                <tr>
                    <td class="tabel_data"><?= $tgl_pelayanan ?></td>
                    <td class="tabel_data"><?= $tempatTidur ?></td>
                    <td class="tabel_data"><?= $kelaspelayanan ?></td>
                    <td class="tabel_data"><?= $dok_admisi ?></td>
                    <td class="tabel_data"><?= $kode ?></td>
                    <td class="tabel_data"><?= $tindakan ?></td>
                    <td class="tabel_data"><?= $val['qty'] ?></td>
                    <td class="tabel_data_numeric"><?= DocoHelpers::formatNumber($val['tarif_satuan']) ?></td>
                    <td class="tabel_data_numeric"><?= DocoHelpers::formatNumber($val['jumlah_tarif']) ?></td>
                </tr>
                <?php $total = $total + $val['jumlah_tarif']; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="8" class="tabel_data_group">Sub Total <?= $key ?></td>
                <td class="tabel_data_group_numeric">
                    <?= DocoHelpers::formatNumber($total) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" class="tabel_data_group">Grand Total</td>
            <td class="tabel_data_group_numeric">
            <?= DocoHelpers::formatNumber($grandTotal) ?> </td>
        </tr>
    </tfoot>
</table>
