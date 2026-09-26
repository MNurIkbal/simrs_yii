<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Barang</td>
            <td>Qty</td>
            <td>Satuan Kecil</td>
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
                        <td><?= isset($value['barang_nama']) ? $value['barang_nama'] : '' ?></td>
                        <td><?= isset($value['jmlterima']) ? $value['jmlterima'] : '' ?></td>
                        <td><?= isset($value['satuanunit_nama']) ? $value['satuanunit_nama'] : '' ?></td>
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