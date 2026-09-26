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
    .tbl-bordered tr#colored {
        background-color: #fdfd96;
    }
</style>

<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Instalasi</th>
            <th>Ruangan</th>
            <th>Dokter</th>
            <th>Jumlah Pasien</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 0; foreach ($data as $value) : ?>
           <?php $no++; ?>
           <tr>
                <td><?= $no; ?></td>
                <td><?= $value['instalasi_nama']; ?></td>
                <td><?= $value['ruangan_nama']; ?></td>
                <td><?= $value['nama_dokter']; ?></td>
                <td><?= $value['jumlah_pasien']; ?></td>
           </tr>
        <?php endforeach; ?>
    </tbody>
</table>