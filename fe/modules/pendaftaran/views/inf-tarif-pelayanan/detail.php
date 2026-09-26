<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
?>
<style type="text/css">
    .r-text-align{
        text-align: -webkit-right;
    }
</style>
<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>

<div class="modal-body">
    <div class="form-group" style="margin-bottom: 0 !important;">
        <label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Kategori Tindakan')?></label>
        <div class="col-sm-8">
            <div class="form-control-static"><?php echo $header['kategoritindakan_nama'] ?></div>
        </div>
    </div>
    <div class="form-group" style="margin-bottom: 0 !important;">
        <label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Nama Tindakan')?></label>
        <div class="col-sm-8">
            <div class="form-control-static"><?php echo $header['daftartindakan_nama'] ?></div>
        </div>
    </div>

    <table id="inf-tarif-pelayanan" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th><?=\Yii::t("fe", "Nama Komponen");?></th>
                <th class="r-text-align"><?=\Yii::t("fe", "Tarif (Rp)");?></th>
            </tr>
        </thead>
        <tbody>
        <?php $total = 0; ?>
        <?php foreach($data as $value): ?>
            <tr>
                <td><?php echo $value['komponentarif_nama']; ?></td>
                <td align="right"><?php echo str_replace('Rp.', '', DocoHelpers::rupiahDisplay($value['harga_tariftindakan']) ); ?></td>
            </tr>
            <?php $total += $value['harga_tariftindakan']; ?>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th class="r-text-align"><?php echo str_replace('Rp.', '', DocoHelpers::rupiahDisplay($total) ); ?></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
