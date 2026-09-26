
<h4 align="center">PEMESANAN OBAT ALKES</h4>
<table>
    <tr>
        <td><b>Tanggal pemesanan</b></td>
        <td>:</td>
        <td><?= $header['tanggal_pemesanan'] ?></td>
        <td><b>Nomer pemesanan</b></td>
        <td>:</td>
        <td><?= $header['no_pemesanan'] ?></td>
    </tr>
    <tr>
        <td><b>Tanggal minta dikirim</b></td>
        <td>:</td>
        <td><?= $header['tanggal_minta'] ?></td>
        <td><b>Ruangan Tujuan</b></td>
        <td>:</td>
        <td><?= $header['ruangan_tujuan'] ?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><b>Ruangan Pemesan</b></td>
        <td>:</td>
        <td><?=$header['ruangan_pemesan']?></td>
    </tr>
</table>
<br>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Obat Alkes</th>
            <th>Qty Pemesanan</th>
            <th>Qty Konversi</th>
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
                <td><?= isset($value['obatalkes_nama']) ? $value['obatalkes_nama'] : '' ?></td>
                <td><?= isset($value['qty_besar']) ? $value['qty_besar'] : '' ?> <?= isset($value['satuan_besar']) ? $value['satuan_besar'] : '' ?></td>
                <td><?= isset($value['qty_kecil']) ? $value['qty_kecil'] : '' ?> <?= isset($value['satuan_kecil']) ? $value['satuan_kecil'] : '' ?></td>
            </tr>
        <?php
            $no++;
            }
        ?>
    </tbody>
</table>
<br>
<br>
Catatan : <?= $header['keterangan_pesan'] ?>
