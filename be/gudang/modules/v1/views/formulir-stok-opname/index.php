<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Barang</td>
            <td>Tanggal Kadaluarsa</td>
            <td>Stok Sistem</td>
            <td>Stok Fisik</td>
            <td>Kondisi</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            if (count($detail)) :
                foreach ($detail as $value) :
        ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= $value['barang_nama'] ?></td>
                        <td><?= isset($value['tglkadaluarsa']) ? date('d-m-Y',strtotime($value['tglkadaluarsa'])) : '' ?></td>
                        <td><?= $value['stok_sistem'] ?></td>
                        <td></td>
                        <td></td>
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