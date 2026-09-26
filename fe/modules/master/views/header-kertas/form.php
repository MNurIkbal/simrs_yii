<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master Header', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL,
    'showError'=>true],
]); 
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                </div>
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'save',
                    'back'
                ]);?>
            </div>
            <div class="panel-body">
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Pengaturan Dokumen</b></h6>
                        </div>
                        <div class="panel-body">
                            <?=$form->field($model, 'kode_header', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('kode_header'),'class' => 'form-control input-sm']); ?>
                            <?=$form->field($model, 'nama_header', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('nama_header'),'class' => 'form-control input-sm']); ?>
                            <?=$form->field($model, 'kertas_id', ['labelOptions' => ['class' => 'text-left']])->dropDownList(ArrayHelper::map($api['response']['jenis_kertas'], 'kertas_id', 'kertas_nama'), ['prompt' => 'Pilih', 'class' => 'form-control input-sm']); ?>
                            <?=$form->field($model,'flag_berulang')->checkbox()?>
                            <?=$form->field($model, 'profilrs_id', ['labelOptions' => ['class' => 'text-left']])->dropDownList(ArrayHelper::map($api['response']['jenis_header'], 'profilrs_id', 'nama_rumahsakit'), ['prompt' => 'Pilih', 'class' => 'form-control input-sm']); ?>
                            <div class="panel panel-info profilrs">
                                <div class="panel-heading">
                                    <h6 class="panel-title">Format Header<a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <p>Nama Rumah Sakit : <span class="form-control-static" id="nama_rumahsakit"></span></p>
                                    <p>Kelas Sakit : <span class="form-control-static" id="kelas_rumahsakit"></span></p>
                                    <p>Alamat Rumah Sakit : <span class="form-control-static" id="alamatlokasi_rumahsakit"></span></p>
                                    <p>No. Fax Rumah Sakit: <span class="form-control-static" id="no_faksimili"></span></p>
                                    <p>Website Rumah Sakit : <span class="form-control-static" id="website"></span></p>
                                    <p>Email Rumah Sakit : <span class="form-control-static" id="email"></span></p>
                                    <p>No. Telepon Rumah Sakit : <span class="form-control-static" id="no_telp_profilrs"></span></p>
                                    <p>Provinsi : <span class="form-control-static" id="propinsi_id"></span></p> 
                                    <p>Kecamatan : <span class="form-control-static" id="kecamatan_id"></span></p>
                                    <p>Kabupaten : <span class="form-control-static" id="kabupaten_id"></span></p>
                                    <p>Kelurahan : <span class="form-control-static" id="kelurahan_id"></span></p>
                                    <p>Negara : <span class="form-control-static" id="negara"></span></p>
                                </div>
                            </div>
                            <?= $form->field($model, 'template_header[old]')->hiddenInput(['value' => $template_header])->label(false); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Konten</b></h6>
                        </div>
                        <div class="panel-body">
                            <?= $form->field($model, 'template_header[new]')
                            ->widget(CKEditor::className(), 
                                [
                                'options' => ['value' => $template_header], 
                                'preset' => 'custom',
                                'clientOptions' => [
                                    'extraPlugins' => '',
                                    'height' => 700,
                                    'width' => '100%',
                                    'filebrowserUploadUrl' => '/master/header-kertas/uploads',
                                ]
                                ])->label(false);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->registerJs('
$(".profilrs").hide();

$(() => {
    $("#docheaderform-template_header-new").parent().removeClass("col-sm-8").addClass("col-sm-12")
})
$(document).on("change", "#docheaderform-profilrs_id", function(){
    $(".profilrs").show();
    var value = $(this).val();
    var hastag = "#";

    $.ajax({
        type: "POST",
        url: window.location.origin + "/master/header-kertas/get-header",
        data: {
            profilrs_id: value,
        },
        dataType: "JSON",
        success: function (res) {
            console.log(res.response);
            if (res.metadata.status == 200) {
                var data = res.response;
                $("#nama_rumahsakit").html(hastag + data[15] + hastag);
                $("#kelas_rumahsakit").html(hastag + data[16] + hastag);
                $("#alamatlokasi_rumahsakit").html(hastag + data[18] + hastag);
                $("#no_faksimili").html(hastag + data[26] + hastag);
                $("#website").html(hastag + data[32] + hastag);
                $("#email").html(hastag + data[33] + hastag);
                $("#no_telp_profilrs").html(hastag + data[34] + hastag);
                $("#propinsi_id").html(hastag + data[3] + hastag);
                $("#kecamatan_id").html(hastag + data[2] + hastag);
                $("#kabupaten_id").html(hastag + data[1] + hastag);
                $("#kelurahan_id").html(hastag + data[4] + hastag);
                $("#negara").html(hastag + data[35] + hastag);
            }
        }
    });
});

$("#ajax-form").docoForm("submit",{
    success : function(data) {
        if (data.status == 201)
            this.formInput[0].reset();
        
    }
});
', View::POS_END, 'header-kertas') ?>
