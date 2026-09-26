<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Barang</td>
            <td>Qty</td>
            <td>Satuan Besar</td>
            <td>Qty</td>
            <td>Satuan Kecil</td>
            <td>Keterangan</td>
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
                        <td><?= $value['text'] ?></td>
                        <td><?= $value['jumlah_input'] ?></td>
                        <td><?= $value['satuanbesar_nama'] ?></td>
                        <td><?= $value['permintaan'] ?></td>
                        <td><?= $value['satuankecil_nama'] ?></td>
                        <td><?= $value['keterangan'] ?></td>
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