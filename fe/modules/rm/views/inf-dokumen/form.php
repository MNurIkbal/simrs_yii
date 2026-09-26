<?php
// Author : Budi
// Date : 16 Januari 2018
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$form = ActiveForm::begin([
'options' => [
    'class' => 'horizontal-form',
    'id' => 'ajax-form',
    'enableClientValidation' => false,
    'validateOnSubmit' => true,
    ]
]);

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <fieldset class="content-group">
    <legend class="text-bold"><?= Yii::t('fe', 'Data Pasien') ?></legend>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label"><?= Yii::t('fe', 'Tanggal Rekam Medik') ?></label>
                    <?= $form->field($model, 'tglrekammedis')->input('', [
                        'placeholder' => Yii::t('fe', 'Tanggal Rekam Medik'), 
                        'class' => 'form-control pickadate'])->label(false);
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label"><?= Yii::t('fe', 'No Rekam Medik') ?></label>
                    <div class="input-group">
                        <?= Html::dropDownList('DokRekamMedisForm[pasien_id]', $model->pasien_id, ArrayHelper::map($pasien, 'pasien_id', 'no_rekam_medik'), [
                                'class' => 'select2 autoNoRm',
                                'prompt' => Yii::t('fe', 'No Rekam Medik')
                            ]) 
                        ?>
                        <?= Html::hiddenInput('DokRekamMedisForm[pasien_id]', '', ['class' => 'pasien_id']); ?>
                        <span class="input-group-addon">
                            <?php
                                echo Html::a('<i class="fa fa-list-ul"></i>
                                    <i class="fa fa-search"></i>',
                                    $module.'search', [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal_backdrop'
                                ]);
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label"><?= Yii::t('fe', 'Nama Pasien') ?></label>
                    <div class="input-group">
                        <?= Html::dropDownList('DokRekamMedisForm[nama_pasien]', $model->pasien_id, ArrayHelper::map($pasien, 'pasien_id', 'nama_pasien'), [
                                'class' => 'select2 autoNoRm',
                                'prompt' => Yii::t('fe', 'Nama Pasien')
                            ]) 
                        ?>
                        <?= Html::hiddenInput('DokRekamMedisForm[nama_pasien]', '', ['class' => 'nama_pasien']); ?>
                        <span class="input-group-addon">
                            <?php
                                echo Html::a('<i class="fa fa-list-ul"></i>
                                    <i class="fa fa-search"></i>',
                                    $module.'search', [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal_backdrop'
                                ]);
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label"><?= Yii::t('fe', 'Nomor Rak') ?></label>
                    <?= $form->field($model, 'lokasirak_id')->dropDownList(ArrayHelper::map($lokasirak, 'lokasirak_id', 'lokasirak_nama'), [
                            'class' => 'form-control',
                            'prompt' => Yii::t('fe', 'Nomor Rak'),
                        ])->label(false)
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label"><?= Yii::t('fe', 'Nomor Sub Rak') ?></label>
                    <?= $form->field($model, 'subrak_id')->dropDownList(ArrayHelper::map($lokasisubrak, 'subrak_id', 'subrak_nama'), [
                            'class' => 'form-control',
                            'prompt' => Yii::t('fe', 'Nomor Sub Rak'),
                        ])->label(false)
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label"><?= Yii::t('fe', 'Warna Dokumen') ?></label>
                    <?= $form->field($model, 'warnadokrm_id')->dropDownList(ArrayHelper::map($warnadok, 'warnadokrm_id', 'warnadokrm_namawarna'), [
                            'class' => 'form-control',
                            'prompt' => Yii::t('fe', 'Warna Dokumen'),
                        ])->label(false)
                    ?>
                </div>
            </div>
        </div>
    </fieldset>
</div>

<div class="modal-footer">
    <?=Html::submitButton('<i class="fa fa-floppy-o"></i> Simpan', ['class' => 'btn bg-teal']); ?>
    <?=Html::button('<i class="fa fa-times"></i> Kembali', ['class' => 'btn btn-fire-brick', 'data-dismiss' => 'modal']); ?>
</div>

<?php ActiveForm::end() ?>
<?php $this->registerJs('
$("#ajax-form").docoForm("submit",{
    success : function(data) {
        if (data.status == 200)
            this.formInput[0].reset();
            table.draw();
    }
});
$(".pickadate").pickadate({
    format: "dd mmm yyyy",
});
', View::POS_END, 'b-index') ?>