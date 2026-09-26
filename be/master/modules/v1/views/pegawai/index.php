<style type="text/css" media="print">
    h3 {
        text-align: center;
    }
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
<table border="1" cellpadding="1" cellspacing="1" style="width:100%" class="tbl-bordered">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Pegawai</td>
            <td>NIP</td>
            <td>Jabatan</td>
            <td>Pangkat</td>
            <td>Pegawai Aktif</td>
            <td>Satu Sehat Practitioner ID</td>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($result)): ?>
            <?= $no = 1; ?>
            <?php foreach ($result as $index => $value): ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= !empty($value["nama_pegawai"]) ? $value["nama_pegawai"] : ''; ?></td>
                <td><?= !empty($value["nomorindukpegawai"]) ? $value["nomorindukpegawai"] : ''; ?></td>
                <td><?= !empty($value["jabatan_nama"]) ? $value["jabatan_nama"] : ''; ?></td>
                <td><?= !empty($value["pangkat_nama"]) ? $value["pangkat_nama"] : ''; ?></td>
                <td><?= $value["is_active"] ? "Aktif" : "Tidak Aktif"; ?></td>
                <td><?= !empty($value["satusehat_pegawai_id"]) ? $value["satusehat_pegawai_id"] : ''; ?></td>
            </tr>
            <?= $no++; ?>
            <?php endforeach ?>
        <?php endif ?>
    </tbody>
</table>