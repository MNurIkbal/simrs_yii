<style>
    table {
        border-collapse: collapse;
    }

    table, td, th {
        border: 1px solid black;
    }
</style>
<table width="100%">
    <tr>
        <th>No</th>
        <th>Nama Ruangan</th>
        <th>Hari</th>
        <th>Shift</th>
        <th>Waktu Pelayanan</th>
        <th>Status</th>
        <?php if ($konfigKuota['kuota_antrian'] == $konfigKuotaPoli): ?>
        <th>Kuota Offline</th>
        <th>Kuota Online</th>
        <?php endif ?>
    </tr>
    <?php $no = 1;
        foreach ($detail as $key => $value) { ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $value['ruangan_nama'] ?></td>
                <td><?= $value['hari_nama'] ?></td>
                <td><?= $value['shift_nama'] ?></td>
                <td><?= $value['jam_mulai'].' - '.$value['jam_tutup'] ?></td>
                <td><?= $value['is_active'] == true ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif') ?></td>
                <?php if ($konfigKuota['kuota_antrian'] == $konfigKuotaPoli): ?>
                <td><?= $value['maxantrian_poli'] ?></td>
                <td><?= $value['kuota_online'] ?></td>
                <?php endif ?>
            </tr>
    <?php $no++;
        } ?>
</table>