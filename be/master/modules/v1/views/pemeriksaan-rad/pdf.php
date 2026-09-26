
<!-- Header -->
<h3><center><?= Yii::t('app', 'Pemeriksaan Radiologi') ?></center></h3>

<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Kode') ?></th>
            <th><?= Yii::t('app', 'Nama Pemeriksaan') ?></th>
            <th><?= Yii::t('app', 'Kelompok Pemeriksaan') ?></th>
            <th><?= Yii::t('app', 'Jenis Pemeriksaan') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($model)): ?>
            <?= $no = 1; ?>
            <?php foreach ($model as $index => $value): ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= !empty($value->pemeriksaanrad_kode) ? $value->pemeriksaanrad_kode : ''; ?></td>
                <td><?= !empty($value->daftarTindakan->daftartindakan_nama) ? $value->daftarTindakan->daftartindakan_nama : ''; ?></td>
                <td><?= !empty($value->kelompokPemeriksaanRad->nama_kelompok) ? $value->kelompokPemeriksaanRad->nama_kelompok : ''; ?></td>
                <td><?= !empty($value->jenisPemeriksaanRad->jenispemeriksaanrad_nama) ? $value->jenisPemeriksaanRad->jenispemeriksaanrad_nama : ''; ?></td>
            </tr>
            <?= $no++; ?>
            <?php endforeach ?>
        <?php endif ?>
    </tbody>
</table>