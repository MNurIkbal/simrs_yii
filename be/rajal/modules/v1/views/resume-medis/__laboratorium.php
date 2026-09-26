<?php

use Doco\components\DocoHelpers;

?>
<style type="text/css">
    table,
    li,
    p {
        font-size: 9px !important;
        margin: 0px;
        line-height: 2;
    }

    .text-center {
        text-align: center;
    }

    table {
        border-collapse: collapse;
    }

    .tbl-no-border td,
    .tbl-no-border th {
        border: 0px;
        padding: 5px;
    }

    .tbl-bordered td,
    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<table class="tbl tbl-bordered" width="100%">
    <tr>
        <th>No</th>
        <th width="15%">Tanggal Pemeriksaan</th>
        <th>Jenis Pemeriksaan</th>
        <th>Nama Pemeriksaan</th>
    </tr>
    <?php
    if (!empty($data)) :
        foreach ($data as $index => $record) :
    ?>
            <tr>
                <td width="1"><?= $index + 1 ?></td>
                <td><?= DocoHelpers::convDateTime($record['tgl_tindakan']) ?></td>
                <td><?= str_replace('_', ' ', $record['jenis']) ?></td>
                <td><?= $record['daftartindakan_nama'] ?></td>
            </tr>
        <?php
        endforeach;
    else :
        ?>
        <tr>
            <td colspan="4">Data tidak tersedia</td>
        </tr>
    <?php
    endif;
    ?>
</table>