<table width="100%" border=1 cellpadding="1" cellspacing="0" style="font-family: Tahoma;">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Obat Alkes</th>
            <th>Qty Pesan</th>
            <th>Satuan</th>
            <th>Qty Kirim</th>
            <th>Satuan</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php foreach ($detail as $row): ?>
            <?php
                $qty_konversi = $row['jumlah_pesan'] /  ($row['qty_besar'] > 0 ? $row['qty_besar'] : 1);
                $qty_konversi = $qty_konversi > 0 ? $qty_konversi : 1;
             ?>
            <tr>
                <td style="text-align: center"><?= $no ?></td>
                <td><?= $row['obatalkes_nama'] ?></td>
                <td><?= $row['qty_besar'] ?></td>
                <td><?= $row['satuan_besar'] ?></td>
                <td><?= empty($row['jumlah_mutasi']) ? "-" : $row['jumlah_mutasi'] / $qty_konversi ?></td>
                <td><?= empty($row['satuan_kirim']) ? "-" : $row['satuan_besar'] ?></td>
            </tr>
            <?php $no++ ?>
        <?php endforeach; ?>
    </tbody>

</table>