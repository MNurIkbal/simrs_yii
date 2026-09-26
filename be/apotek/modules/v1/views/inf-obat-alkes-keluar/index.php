<?php $isKonversi = ($statusmutasiId == 400); ?>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Obat Alkes</td>
            <td>Qty Pemesanan</td>
            <td>Qty Konversi</td>
            <td>Qty Terima</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($detail as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '' ?></td>
                <td><?= isset($value['qty_besar']) && isset($value['satuan_besar']) ? $value['qty_besar']." ".$value['satuan_besar'] : '' ?></td>
                <td><?= isset($value['jumlah_pesan']) && isset($value['satuan_kecil']) && $isKonversi ? $value['jumlah_pesan']." ".$value['satuan_kecil'] : '&nbsp;-' ?></td>
                <td><?= isset($value['jumlah_input_mutasi']) && isset($value['satuan_besar']) ? $value['jumlah_input_mutasi']." ".$value['satuan_besar'] : '&nbsp;-' ?></td>
            </tr>
        <?php
            $no++;
            endforeach;

        ?>
    </tbody>
</table>