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
            <th>Kamar</th>
            <th>Ruangan</th>
            <th>Kelas Pelayanan</th>
            <th>Klasifikasi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($data as $id => $value) : ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= !empty($value['kamarruangan_nokamar']) ? $value['kamarruangan_nokamar'] : '' ?></td>
                    <td><?= !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '' ?></td>
                    <td><?= !empty($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '' ?></td>
                    <td><?= !empty($value['klasifikasikamar_nama']) ? $value['klasifikasikamar_nama'] : '' ?></td>
                </tr>
        <?php $no++; ?>
        <?php endforeach; ?>
    </tbody>
</table>