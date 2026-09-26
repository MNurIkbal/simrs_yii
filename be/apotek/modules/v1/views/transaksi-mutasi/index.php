<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Obat Alkes</th>
            <th>Qty</th>
            <th>Satuan Pemesanan</th>
            <th>Qty</th>
            <th>Satuan Besar</th>
            <th>Qty</th>
            <th>Satuan Kecil</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($detail as $value) {
                $id_satuan_besar = $value['satuanbesar_id'];
                $id_satuan_pemesanan = $value['satuan_pemesanan'];
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= !empty($value['obatalkes_namalain']) ? $value['obatalkes_namalain'] : '' ?></td>
                <td><?= ($id_satuan_besar == $id_satuan_pemesanan) ? $value['jumlah_input'] : $value['jumlah_pesan'] ?></td>
                <td><?= ($id_satuan_besar == $id_satuan_pemesanan) ? $value['satuanbesar_nama'] : $value['satuankecil_nama'] ?></td>
                <td><?= !empty($value['jumlah_input']) ? $value['jumlah_input'] : 0 ?></td>
                <td><?= !empty($value['satuanbesar_nama']) ? $value['satuanbesar_nama'] : '' ?></td>
                <td><?= !empty($value['jumlah_pesan']) ? $value['jumlah_pesan'] : 0 ?></td>
                <td><?= !empty($value['satuankecil_nama']) ? $value['satuankecil_nama'] : '' ?></td>
            </tr>
        <?php
            $no++;
            }
        ?>
    </tbody>
</table>