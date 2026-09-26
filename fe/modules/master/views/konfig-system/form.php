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
    <?=$form
        ->field($model, 'propinsi_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($propinsi, 'propinsi_id', 'propinsi_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih')
        ]);
    ?>
    <?=$form
        ->field($model, 'kabupaten_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($kabupaten, 'kabupaten_id', 'kabupaten_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih')
        ]);
    ?>
    <?=$form
        ->field($model, 'kecamatan_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($kecamatan, 'kecamatan_id', 'kecamatan_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih')
        ]);
    ?>
    <?=$form
        ->field($model, 'kelurahan_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($kelurahan, 'kelurahan_id', 'kelurahan_nama'), [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih')
        ]);
    ?>
    <?=$form->field($model, 'tahunprofilrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('tahunprofilrs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'kodejenisrs_profilrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('kodejenisrs_profilrs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'jenisrs_profilrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('jenisrs_profilrs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'statusrsswasta', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('statusrsswasta'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'namakepemilikanrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('namakepemilikanrs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'kodestatuskepemilikanrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('kodestatuskepemilikanrs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'statuskepemilikanrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('statuskepemilikanrs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'pentahapanakreditasrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('pentahapanakreditasrs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'statusakreditasrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('statusakreditasrs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'nokode_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('nokode_rumahsakit'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'nama_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('nama_rumahsakit'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'kelas_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('kelas_rumahsakit'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'namadirektur_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('namadirektur_rumahsakit'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'alamatlokasi_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('alamatlokasi_rumahsakit'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'nomor_suratizin', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('nomor_suratizin'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'tgl_suratizin', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('tgl_suratizin'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'oleh_suratizin', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('oleh_suratizin'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'sifat_suratizin', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('sifat_suratizin'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'masaberlakutahun_suratizin', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('masaberlakutahun_suratizin'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'motto', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('motto'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'visi', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('visi'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'no_faksimili', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('no_faksimili'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'logo_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('logo_rumahsakit'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'path_logorumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('path_logorumahsakit'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'npwp', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('npwp'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'tahun_diresmikan', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('tahun_diresmikan'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'khususuntukswasta', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('khususuntukswasta'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'website', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('website'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'email', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('email'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'no_telp_profilrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('no_telp_profilrs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'negara', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('negara'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'tglakreditasi', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('tglakreditasi'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'akreditasirs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('akreditasirs'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'tglregistrasi', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('tglregistrasi'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'notelphumas', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('notelphumas'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'luastanah', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('luastanah'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'luasbangunan', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('luasbangunan'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'ppkpelayanan', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('ppkpelayanan'),'class' => 'form-control input-sm']); ?>
    <!--?=$form
        ->field($model, 'is_active', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($options['status'], [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
            'options' => [$model->is_active => ['selected' => true]]
        ]);
    ?-->
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            table.draw();
        }
    });
</script>