<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-20 17:29:28
 */
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
    .tbl-bordered tr#colored {
        background-color: #fdfd96;
    }
</style>

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No</th>
            <th>Tanggal Permintaan</th>
            <th>Menu</th>
            <th>Jenis Diet</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Diagnosa</th>
            <th>Alergi</th>
            <th>Penjamin</th>
            <th style="text-align: right;">Jumlah</th>
       </tr>
    </thead>
    <tbody>
        <?php $counter = 1; foreach($data as $value) : ?>
           <tr>
                <td><?= $counter++; ?></td>
                <td><?= DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_permintaanmakan'])), false, false); ?></td>
                <td><?= $value['makanandiet_nama']; ?></td>
                <td><?= !empty($value['jenisdiet_nama']) ? $value['jenisdiet_nama'] : '-' ?></td>
                <td><?= $value['jenis_kelamin']; ?></td>
                <td><?= date('d M Y',strtotime($value['tanggal_lahir'])); ?></td>
                <td><?= $value['diagnosa']; ?></td>
                <td><?= !empty($value['riwayat_alergi']) ? $value['riwayat_alergi'] : '-' ?></td>
                <td><?= $value['penjamin_nama']; ?></td>
                <td style="text-align: right;"><?= $value['jumlah']; ?></td>
           </tr>
        <?php endforeach; ?>
    </tbody>
    <?php if (!empty($data)): ?>
    <tfoot>
        <tr>
            <th colspan="2">Jumlah</th>
            <th style="text-align: right;"><?= $count_makanan ?></th>
            <th style="text-align: right;"><?= $count_jenis ?></th>
            <th style="text-align: right;"></th>
            <th style="text-align: right;"></th>
            <th style="text-align: right;"><?= $count_diagnosa ?></th>
            <th style="text-align: right;"><?= $count_alergi ?></th>
            <th style="text-align: right;"></th>
            <th style="text-align: right;"><?= $count_jumlah ?></th>
        </tr>
    </tfoot>
    <?php endif ?>
</table>