<table border="1" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Barang</td>
            <td>Qty Pemesanan</td>
            <td>Qty Konversi</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            if (count($detail) > 0) :
                foreach ($detail as $value) :
        ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= isset($value['barang_nama']) ? $value['barang_nama'] : '' ?></td>
                        <td style="text-align: right;"><?= isset($value['jumlah_input']) && isset($value['satuan_besar'])  ? $value['jumlah_input']." ".$value['satuan_besar'] : '' ?></td>
                        <td style="text-align: right;"><?= isset($value['qty_pesan']) && isset($value['satuan_kecil'])  ? $value['qty_pesan']." ".$value['satuan_kecil'] : '' ?></td>
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
