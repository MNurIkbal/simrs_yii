<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-11 10:20:31
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\Select2;
use yii\web\JsExpression;


$this->title = \Yii::t('fe', $title);
$form = ActiveForm::begin([
    'id' => 'asesmenmedis-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
]);
$disabled = '';
if(empty($model->asesmenmedis_id)){
    // $disabled = 'disabled';
}
if($model->loged != true){
    $disabled = 'disabled';
}
?>
<style>
.no-padding{
    padding-top: 0px;
}
.m-btm-5{
    margin-bottom: 5px;
}
</style>
<br>
<div class="panel">
    <div class="panel-white">
        <div class="panel-body">
            <br>
            <div class="form-asesmen">
                <?php
                if ($isStopAkomodasi) {
                    ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="alert alert-danger" role="alert"><strong>Pasien Sudah Melakukan Stop Akomodasi</strong></div>
                            </div>
                        </div>
                    <?php
                }
                ?>
                <fieldset title="1" class="stepy-step" onmouseover="this.title='';">
                    <legend class="stepy-legend"></legend>
                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/page_awal.php',[
                        'form' => $form,
                        'model' => $model,
                        'data_kategori_asesmen' => $data_kategori_asesmen,
                        'dokterNama' => $dokterNama,
                        'tahun' => $tahun,
                        'data_diagnosa' => $data_diagnosa,
                        'data_imunisasi' => $data_imunisasi,
                        'data_sumberInfo' => $data_sumberInfo,
                        'data_isMerokok' => $data_isMerokok,
                        'data_obatalkes' => $data_obatalkes,
                        'data_kategori_asesmen' => $data_kategori_asesmen,
                        'data_status_meroko' => $data_status_meroko,
                        'waktu_tumbuh_kembang' => $waktu_tumbuh_kembang
                    ]); ?>
                    <br>

                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/page_awal_anak.php',[
                        'form' => $form,
                        'model' => $model,
                        'data_komplikasi' => $data_komplikasi,
                        'data_neotanus' => $data_neotanus,
                        'data_maternal' => $data_maternal,
                        'waktu_tumbuh_kembang' => $waktu_tumbuh_kembang
                    ]); ?>
                    <br>
                </fieldset>
                <fieldset title="2" class="stepy-step" onmouseover="this.title='';">
                    <legend class="stepy-legend"><?=Yii::t('fe', 'Asesmen awal medis')?></legend>
                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/tanda_vital.php',[
                        'data_denyutJantung' => $data_denyutJantung,
                        'model' => $model,
                    ]); ?>
                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/keadaan_umum.php',[
                        'data_gcsEye' => $data_gcsEye,
                        'data_gcsVerbal' => $data_gcsVerbal,
                        'data_gcsMotorik' => $data_gcsMotorik,
                        'data_kontak' => $data_kontak,
                        'form' => $form,
                        'data_metodAsesmen' => $data_metodAsesmen,
                        'data_isTerintubasi' => $data_isTerintubasi,
                        'model' => $model,
                        'gcsEyeOptions' => $gcsEyeOptions,
                        'gcsVerbalOptions' => $gcsVerbalOptions,
                        'gcsMotorikOptions' => $gcsMotorikOptions,
                        'data_kesadaran' => $data_kesadaran,
                        'data_kesadaran_umum' => $data_kesadaran_umum,
                    ]); ?>

                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/pemeriksaan_fisik.php',[
                        'data_kepala' => $data_kepala,
                        'data_mata' => $data_mata,
                        'data_telinga' => $data_telinga,
                        'data_leher' => $data_leher,
                        'data_mulut' => $data_mulut,
                        'data_paru' => $data_paru,
                        'data_thoraks' => $data_thoraks,
                        'data_pergerakan' => $data_pergerakan,
                        'data_perkusi' => $data_perkusi,
                        'data_pernapasan' => $data_pernapasan,
                        'data_rochi' => $data_rochi,
                        'data_wheezing' => $data_wheezing,
                        'data_irama' => $data_irama,
                        'data_bunyi_jantung' => $data_bunyi_jantung,
                        'data_kelainan' => $data_kelainan,
                        'data_benjolan' => $data_benjolan,
                        'data_nyeri_tekan' => $data_nyeri_tekan,
                        'data_hernia' => $data_hernia,
                        'data_bising_usus' => $data_bising_usus,
                        'data_distensi' => $data_distensi,
                        'data_tulang_belakang' => $data_tulang_belakang,
                        'data_sistem_saraf' => $data_sistem_saraf,
                        'data_genetalia' => $data_genetalia,
                        'data_edema' => $data_edema,
                        'data_crt' => $data_crt,
                        'form' => $form,
                        'model' => $model,
                    ]); ?>



                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/status_lokalis_dewasa.php',[
                        'model' => $model,
                        'form' => $form,
                        'data_bagiantubuh' => $data_bagiantubuh,
                        'data_luka_bakar' => $data_luka_bakar,
                    ]); ?>

                    <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/status_lokalis_anak.php',[
                        'model' => $model,
                        'form' => $form,
                        'data_bagiantubuh' => $data_bagiantubuh,
                        'data_luka_bakar' => $data_luka_bakar,
                    ]); ?>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Pemeriksaan penunjang')?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['catatan_rad']?></label>
                                        <div class="col-sm-3" style="padding: 10px">
                                            <?php if($data_hasil_rad): ?>
                                                <?php foreach($data_hasil_rad as $each): ?>
                                                    <?=
                                                    Html::a(
                                                        Yii::t('fe', 'Lihat hasil'),
                                                        [
                                                            '/radiologi/hasil-rad/cetak',
                                                            'id' => $each
                                                        ],
                                                        [
                                                            'class' => 'btn btn-xs btn-success',
                                                            'target' => 'blank',
                                                            'style' => 'margin-bottom:5px;'
                                                        ]
                                                    );
                                                    ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?=Html::activeTextArea($model, 'catatan_rad', ['class'=>'form-control'])?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['catatan_lab']?></label>
                                        <div class="col-sm-3" style="padding: 10px">
                                            <?php if($data_hasil_lab): ?>
                                                <?php foreach($data_hasil_lab as $each): ?>
                                                    <?=
                                                    Html::a(
                                                        Yii::t('fe', 'Lihat hasil'),
                                                        [
                                                            '/laboratorium/integrasi-lis-hasil/cetak',
                                                            'id' => $each
                                                        ],
                                                        [
                                                            'class' => 'btn btn-xs btn-success',
                                                            'target' => 'blank',
                                                            'style' => 'margin-bottom:5px;'
                                                        ]
                                                    );
                                                    ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?=Html::activeTextArea($model, 'catatan_lab', ['class'=>'form-control'])?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Daftar masalah / Diagnosa')?></h6>
                                    <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    // echo $form->field($model, 'diagnosa_id')->dropDownList(ArrayHelper::map($data_diagnosa, 'diagnosa_id', 'diagnosa_nama'), ['class'=>'select2', 'prompt'=>'']);
                                    echo $form->field($model, 'diagnosa_id')->widget(Select2::classname(), [
                                        'initValueText' => isset($text_diagnosa_id) && $text_diagnosa_id != '' ? $text_diagnosa_id : null,
                                        'options' => [
                                            'id' => 'diagnosa_id',
                                            'placeholder' => '-- Pilih --',
                                            'class' => 'form-control input-sm select2'
                                        ],
                                        'pluginOptions' => [
                                            // 'allowClear' => true,
                                            'tags' => true,
                                            'tokenSeparators' => [',', '_'],
                                            'minimumInputLength' => 3,
                                            'language' => [
                                                'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                            ],
                                            'ajax' => [
                                                'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('
                                                    function(params) {
                                                        return {
                                                            q: params.term,
                                                            type: "diagnosa_utama",
                                                            all_text: 0,
                                                            id_with_text: 1,
                                                        };
                                                    }
                                                ')
                                            ],
                                            'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                            'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                            'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                                        ],
                                    ])
                                    ?>
                                    <?=$form->field($model, 'masalah')->textArea()?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Discharge planning')?></h6>
                                    <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                                </div>
                                <div class="panel-body">
                                    <?=$form->field($model, 'discharge_plan')->radioList($data_discharge)?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Care planning')?></h6>
                                    <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                                </div>
                                <div class="panel-body">
                                <div class="col-md-6">
                                        <?=$form->field($model, 'care_plan')->textArea(['class'=>'care-plan'])?>
                                </div>
                                <div class="col-md-6">
                                         <?=$form->field($model, 'rencana')->textArea(['class'=>'rencana'])?>
                                </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <?php if ($model['asesmenmedis_id'] != ''): ?>
                <?= Html::button("<b><i class='fa fa-trash'></i></b> Hapus", ['class'=>'btn btn-xs btn-labeled btn-info stepy-finish btn-hapus', 'action' => '/ranap/pemeriksaan-rawat-inap/hapus-asesmen-medis?id='.DocoHelpers::encrypt($pendaftaran_id).'&asesmenmedis_id='.$model['asesmenmedis_id']]) ?>
                <?php endif ?>
                <?=Html::button("<b><i class='fa fa-print'></i></b> ".Yii::t('fe','print'), ['class'=>'stepy-finish btn-cetak-asesmen btn btn-xs btn-labeled btn-info '.(empty($model->asesmenmedis_id)?'disabled' : ''), 'data-target'=>(!empty($model->asesmenmedis_id) ? Url::to(['cetak-asesmen','id'=>DocoHelpers::encrypt($pendaftaran_id)]) : '')])?>
                <?=Html::submitButton($model['asesmenmedis_id'] != '' ? "<b><i class='fa fa-pencil'></i></b> Ubah" : "<b><i class='fa fa-floppy-o'></i></b> Simpan", ['class'=>'stepy-finish btn btn-xs btn-labeled btn-info '.($isStopAkomodasi ? 'disabled' : '')])?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php
$jsonBagianTubuh = json_encode(ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'));
$jsonDetailBagianTubuh = json_encode($data_detailbagiantubuh);
$this->registerJs("
    var is_disabled = '".$disabled."';
    $(document).ready(function(){
        $('#asesmenmedis-form :input').not('.btn-cetak-asesmen').prop('disabled', ".$status_disabled.");
        $('.input-group-addon').".$hide.";
    });

    var removeDisable = function(){
        $('.btn-cetak-asesmen').removeClass('disabled')
    }
    $('.btn-cetak-asesmen').on('click', function(){
        let url = window.location.origin;
        let target = $(this).attr('data-target');
        if (target == '') {
            return false;
        } else {
            window.open(url+target);
        }
    })
    var tabel_anggotatubuh = $('.tabel-anggotatubuh').DataTable({
        filter: false,
        bLengthChange: false,
        bInfo: false,
        processing: true,
        paging: false,
    });
    $('.form-asesmen').stepy({
          backLabel: '".Yii::t('fe', 'Kembali')." <b><i class=\'fa fa-chevron-left\'></i></b>',
          nextLabel: '".Yii::t('fe', 'Selanjutnya')." <b><i class=\'fa fa-chevron-right\'></i></b>',
          enter: false,
        })
    $('.form-asesmen').find('.button-next').addClass('btn btn-xs btn-labeled btn-info');
    $('.form-asesmen').find('.button-back').addClass('btn btn-xs btn-labeled btn-info');

    // define data master tekanan darah & map
    var data_tekanandarah = ".json_encode($data_tekanandarah).";
    var tmpData = ".$jsonAnatomi.";
    var bagianTubuh = ".$jsonBagianTubuh.";
    var detailBagianTubuh = ".$jsonDetailBagianTubuh.";
    var counter = ".$counter.";
    var metodeGcs = ".json_encode($data_metodegcs).";
    var dataGcs = ".json_encode($data_gcs).";
    var listGcs = ".json_encode($data_listgcs).";
    var dataBmi = ".json_encode($data_bmi).";
    var dataBmiAnak = ".json_encode($data_bmi_anak).";
    var dataPenyakitDahulu = ".json_encode($penyakitDahulu).";
    var dataRiwayatPenyakitDahulu = ".json_encode($riwayatPenyakitDahulu).";
    var jeniskelamin = '. $jeniskelamin .';
    var data_sumber_info = '".$sumber_info."';
    var data_sumber_info_lainnya = '".$sumber_info_lainnya."';
    var getBulan = ".$getBulan.";
    var jeniskelamin_id = ".$jeniskelamin_id.";
    var asmedStopAkomodasi = ".$isStopAkomodasi.";
    var rowDataRiwayatPenyakitDahulu = dataPenyakitDahulu.length-1;
    var rowDataRiwayatPenyakitDahuluTotal = dataPenyakitDahulu.length-1;
    var umur = ".json_encode($getUmur).";
    var is_draft = " . $is_draft . ";
", View::POS_END, 'js2');
$this->registerJs($this->render('js/asesmenmedis.js'), View::POS_END, 'js')

?>
