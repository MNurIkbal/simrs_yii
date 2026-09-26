<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

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
			<div class="form-control-static"> <?php echo $data['data']['kategoritindakan_nama'] ?> </div>
		</div>
	</div>
	<div class="form-group" style="margin-bottom: 0 !important;">
		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Nama Tindakan')?></label>
		<div class="col-sm-8">
			<div class="form-control-static"> <?php echo $data['data']['daftartindakan_nama'] ?> </div>
		</div>
	</div>

	<table id="inf-tarif-pelayanan" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th><?=\Yii::t("fe", "Nama Komponen");?></th>
                <th><?=\Yii::t("fe", "Tarif");?></th>
            </tr>
        </thead>
        <tbody>
        	<?php foreach($data['query'] as $row): ?>
        		<tr>
        			<td><?php echo $row['komponentarif_nama']; ?></td>
        			<td><?php echo $row['harga_tariftindakan']; ?></td>
        		</tr>
    	<?php endforeach; ?>
        <tr style="background-color: #e6e6e6;font-weight: 900;">
            <td>TOTAL</td>
            <td><?php echo $data['total']; ?></td>
        </tr>
        </tbody>
    </table>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>