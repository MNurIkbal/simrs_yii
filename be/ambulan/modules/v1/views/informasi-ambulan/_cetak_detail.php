<?php
use Doco\components\DocoHelpers;
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

<table width="100%" class="tbl-bordered" style="font-size: 11px;">
    <thead>
       <tr>
            <th>No</th>
            <th>Tanggal Pemakaian</th>
            <th>Tanggal Kembali</th>
            <th>Nomor Pemesanan</th>
            <th>Nama Pemesanan</th>
            <th>Supir</th>
            <th align="right">Jarak Pemakaian (Km)</th>
            <th align="right">Nominal Tagihan (Rp.)</th>
            <th align="right">Total (Rp.)</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($data as $key => $value) : 
            $total = $value['biaya_tambahan'] + $value['nominal_tagihan']; 
        ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $no++ ?></td>
                <td><?= !empty($value['tgl_pemakaiandari']) ? date('d-M-Y', strtotime($value['tgl_pemakaiandari'])) : '-' ?></td>
                <td><?= !empty($value['tgl_realisasikembali']) ? date('d-M-Y', strtotime($value['tgl_realisasikembali'])) : '-' ?></td>
                <td><?= $value['no_pesanambulan'] ?></td>
                <td><?= $value['nama_pemesan'] ?></td>
                <td align="center"><?= !empty($value['supir']) ? $value['supir'] : '-'; ?></td>
                <td align="right"><?= !empty($value['jarak_pemakaian']) ? DocoHelpers::formatNumber($value['jarak_pemakaian']) : 0; ?></td>
                <td align="right"><?= !empty($value['biaya_tambahan']) ? DocoHelpers::formatNumber($value['biaya_tambahan']) : 0; ?></td>
                <td align="right"><?= DocoHelpers::formatNumber($total) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
