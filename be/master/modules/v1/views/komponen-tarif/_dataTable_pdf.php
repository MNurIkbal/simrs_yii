<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    table {
        border-collapse: collapse;
    }
    .bg-inverse th, .td-inverse td {
        border: 1px solid #000000;
        padding: 10px;
        text-align: left;
    }
    .bg-inverse th {
        background-color: #eee;
    }
</style>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="0" style="font-size: 12px;">
                <thead>
                    <tr class="bg-inverse">
                        <th><?= Yii::t('app', 'No') ?></th>
                        <th><?= Yii::t('app', 'Kode Komponen') ?></th>
                        <th><?= Yii::t('app', 'Nama Komponen') ?></th>
                        <th><?= Yii::t('app', 'Nama Lainnya') ?></th>
                        <th><?= Yii::t('app', 'Persentasi Delegasi').' (%) ' ?></th>
                        <th><?= Yii::t('app', 'Status') ?></th>
                        <th><?= Yii::t('app', 'Catatan') ?></th>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach ($result as $index => $value): ?>
                        <tr class="td-inverse">
                            <td><?= $index + 1 ?></td>
                            <td><?= $value['komponentarif_kode'] ?></td>
                            <td><?= $value['komponentarif_nama'] ?></td>
                            <td><?= $value['komponentarif_namalainnya'] ?></td>
                            <td align="right"><?= str_replace('.', ',', $value['persen_delegasi']) ?></td>
                            <td><?= ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif' ?></td>
                            <td><?= $value['catatan'] ?></td>
                        </tr>
                        <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>