<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Barang</td>
            <td>Kondisi</td>
            <td>Stok Sistem</td>
            <td>Stok Fisik</td>
            <td>Selisih</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            $totalSs = $totalSf = $totalSeS = 0;
            foreach ($detail as $value):
                $totalSs += $value['volume_sistem'];
                $totalSf += $value['volume_fisik'];
                $totalSeS += $value['jmlselisihstok'];
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $value['barang_nama'] ?></td>
                <td><?= $value['kondisibarang'] ?></td>
                <td><?= $value['volume_sistem'] ?></td>
                <td><?= $value['volume_fisik'] ?></td>
                <td><?= $value['jmlselisihstok'] ?></td>
            </tr>
        <?php
            $no++;
            endforeach;
        ?>
        <tr>
            <th colspan="5" class="text-right">Total Stok Sistem</th>
            <th class="text-right"><?= $totalSs ?></th>
        </tr>
        <tr>
            <th colspan="5" class="text-right">Total Stok Fisik</th>
            <th class="text-right"><?= $totalSf ?></th>
        </tr>
        <tr>
            <th colspan="5" class="text-right">Total Selisih Stok</th>
            <th class="text-right"><?= $totalSeS ?></th>
        </tr>
        <tr>
            <th colspan="5" class="text-right">Total Hargga Netto Stok Sistem</th>
            <th class="text-right"><?= $header['totalharga_sistem'] ?></th>
        </tr>
        <tr>
            <th colspan="5" class="text-right">Total Harga Netto Stok Fisik</th>
            <th class="text-right"><?= $header['totalharga_fisik'] ?></th>
        </tr>
        <tr>
            <th colspan="5" class="text-right">Total Selisih harga Netto Stok</th>
            <th class="text-right"><?= $header['totalharga_sistem'] - $header['totalharga_fisik'] ?></th>
        </tr>
    </tbody>
</table>