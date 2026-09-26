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
                        <th><?= Yii::t('app', 'Nomor SK') ?></th>
                        <th><?= Yii::t('app', 'Nama Perda') ?></th>
                        <th><?= Yii::t('app', 'Nama Lainnya') ?></th>
                        <th><?= Yii::t('app', 'Tanggal Berlaku') ?></th>
                        <th><?= Yii::t('app', 'Detail') ?></th>
                        <th><?= Yii::t('app', 'Ditetapkan Oleh') ?></th>
                        <th><?= Yii::t('app', 'Status') ?></th>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach ($result as $index => $value): ?>
                        <tr class="td-inverse">
                            <td><?= $index + 1 ?></td>
                            <td><?= $value['perda_no'] ?></td>
                            <td><?= $value['perdanama_sk'] ?></td>
                            <td><?= $value['nama_lainnya'] ?></td>
                            <td><?= date('d M Y', strtotime($value['perda_tgl'])) ?></td>
                            <td><?= $value['perda_tentang'] ?></td>
                            <td><?= $value['ditetapkan_oleh'] ?></td>
                            <td><?= ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif' ?></td>
                        </tr>
                        <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>