
<!--
@author Randy Vianda Putra
@todo Cetak Master Kamar
@copyright 21 Mei 2018 aweutist
-->
<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<table class="tbl-bordered" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Ruangan</th>
            <th>Kelas Pelayanan</th>
            <th>Jenis Kasus Penyakit</th>
            <th>Nama Kamar</th>
            <th>Jenis Kamar</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($detail as $id => $value) : ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '' ?></td>
                    <td><?= !empty($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '' ?></td>
                    <td><?= !empty($value['jeniskasuspenyakit_nama']) ? $value['jeniskasuspenyakit_nama'] : '' ?></td>
                    <td><?= !empty($value['kamarruangan_nokamar']) ? $value['kamarruangan_nokamar'] : '' ?></td>
                    <td><?= !empty($value['jenis_kamar']) ? $value['jenis_kamar'] : '' ?></td>
                    <td><?= !empty($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ?></td>
                </tr>
        <?php $no++; ?>
        <?php endforeach; ?>
    </tbody>
</table>