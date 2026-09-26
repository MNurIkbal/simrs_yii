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
            <th rowspan="2">Tanggal</th>
            <th colspan="3">Pasien</th>
            <th rowspan="2">Jumlah</th>
            <th colspan="5">Pasien Keluar</th>
            <th rowspan="2">Jumlah</th>
            <th rowspan="2">Pasien Akhir</th>
        </tr>
        <tr>
            <th>Awal</th>
            <th>Masuk</th>
            <th>Pindahan</th>
            <th>Keluar Hidup</th>
            <th>Dipindahkan</th>
            <th>Jumlah</th>
            <th>< 48 Jam</th>
            <th>> 48 Jam</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $value) : ?>
           <tr>
                <td><?= $value['tanggal']; ?></td>
                <td><?= $value['awal']; ?></td>
                <td><?= $value['masuk']; ?></td>
                <td><?= $value['pindahan']; ?></td>
                <td><?= $value['jml_234']; ?></td>
                <td><?= $value['klr_hidup']; ?></td>
                <td><?= $value['dipindahkan']; ?></td>
                <td><?= $value['meninggal_jml']; ?></td>
                <td><?= $value['meninggal_kur48jam']; ?></td>
                <td><?= $value['meninggal_leb48jam']; ?></td>
                <td><?= $value['jml_678']; ?></td>
                <td><?= $value['pasien_akhir']; ?></td>
           </tr>
        <?php endforeach; ?>
    </tbody>
</table>