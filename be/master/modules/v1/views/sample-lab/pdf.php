<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th><?= Yii::t('app', 'No') ?></th>
            <th><?= Yii::t('app', 'Kode') ?></th>
            <th><?= Yii::t('app', 'Nama sample') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($model)): ?>
            <?= $no = 1; ?>
            <?php foreach ($model as $index => $value): ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $value->kode_sample ?></td>
                <td><?= $value->nama_sample ?></td>
            </tr>
            <?= $no++; ?>
            <?php endforeach ?>
        <?php endif ?>
    </tbody>
</table>