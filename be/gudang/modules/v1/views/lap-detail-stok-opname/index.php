<?php
    use Doco\components\DocoHelpers;
?>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Tanggal stok opname</td>
            <td>Nomor stok opname</td>
            <td>Nama barang</td>
            <td>Jumlah sistem</td>
            <td>Jumlah fisik</td>
            <td>Harga netto sistem</td>
            <td>Harga netto fisik</td>
            <td>Selisih jumlah</td>
            <td>Selisih harga netto</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            $totalHarga = 0;
            if (count($detail)) :
                foreach ($detail as $value) :
                    $harga_fisik = $value['volume_fisik'] * $value['harganetto'];
                    $harga_sistem = $value['volume_sistem'] * $value['harganetto'];
                    $selisih_harga = $value['jmlselisihstok'] * $value['harganetto'];
        ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= date('d-M-Y',strtotime($value['tglstokopname'])) ?></td>
                        <td><?= $value['nostokopname'] ?></td>
                        <td><?= $value['barang_nama'] ?></td>
                        <td><?= $value['volume_sistem'] ?></td>
                        <td><?= $value['volume_fisik'] ?></td>
                        <td><?= "Rp. " . number_format($harga_sistem, 0, ',', '.'); ?></td>
                        <td><?= "Rp. " . number_format($harga_fisik, 0, ',', '.'); ?></td>
                        <td><?= $value['jmlselisihstok'] ?></td>
                        <td><?= "Rp. " . number_format($selisih_harga, 0, ',', '.'); ?></td>
                    </tr>
        <?php
                $no++;
                endforeach;
            else :
        ?>
            <tr>
                <td colspan="4" style="text-align: center;">Data kosong</td>
            </tr>
        <?php
            endif;
        ?>
    </tbody>
</table>