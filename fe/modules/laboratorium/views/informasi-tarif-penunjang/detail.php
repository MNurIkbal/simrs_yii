<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <table class="table table-bordered table-condensed table-striped table-hover">
        <tr>
            <td><?= \Yii::t('fe', 'Kelompok Pemeriksaan') ?></td>
            <td>
                <?=$attributes['query']['nama_kelompok'];?>
            </td>
        </tr>
        <tr>
            <td><?= \Yii::t('fe', 'Jenis Pemeriksaan') ?></td>
            <td>
                <?=$attributes['query'][$jenisPemeriksaan];?>
            </td>
        </tr>
        <tr>
            <td><?= \Yii::t('fe', 'Nama Tindakan') ?></td>
            <td>
                <?=$attributes['query']['daftartindakan_nama'];?>
            </td>
        </tr>
    </table>
    <br>
    <table class="table table-bordered table-condensed table-striped table-hover">
        <thead>
            <tr>
                <th><?= \Yii::t('fe', 'Nama Komponen') ?></th>
                <th><?= \Yii::t('fe', 'Tarif') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attributes['detail'] as $key => $value) : ?>
            <tr>
                <td><?= $value['komponentarif_nama'] ?></td>
                <td><?= number_format($value['harga_tariftindakan'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong><?= number_format($attributes['total'], 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="modal-footer">
    <?=Html::button('Kembali',[
        'class' => 'btn btn-default btn-md',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
