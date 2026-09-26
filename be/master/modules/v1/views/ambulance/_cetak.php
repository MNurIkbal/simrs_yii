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

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No.</th>
            <th>Nomor Polisi</th>
            <th>Jenis Ambulan</th>
            <th>Merek</th>
            <th>Status</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($data as $key => $value) : ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $no++ ?></td>
                <td><?= $value['no_polisi'] ?></td>
                <td><?= $value['is_emergency'] ?></td>
                <td><?= $value['barang_merk'] ?></td>
                <td><?= $value['stat_ambulan'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
