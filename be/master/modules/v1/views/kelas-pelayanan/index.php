<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Jenis Kelas</td>
            <td>Kelas Pelayanan</td>
            <td>Nama lainnya</td>
            <td>Satatus</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($detail as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['jenisKelas']['jeniskelas_nama']) 
                        ? $value['jenisKelas']['jeniskelas_nama'] : '' ?></td>
                <td><?= isset($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '' ?></td>
                <td><?= isset($value['kelaspelayanan_namalainnya']) ? $value['kelaspelayanan_namalainnya'] : '' ?></td>
                <td><?= !empty($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ?></td>
            </tr>
        <?php
            $no++;
            endforeach;

        ?>
    </tbody>
</table>
