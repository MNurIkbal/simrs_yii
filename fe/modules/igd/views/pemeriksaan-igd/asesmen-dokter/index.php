<?php
/**
 * @Author: Ardi Pratama Septiadi
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;

// echo '<pre>';
// print_r($historyPenyakit);
// exit();
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">   
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'custom-save' => [
                        'title' => Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-floppy-o',
                        'attributes' => [
                            'data-options'=>'click',
                            'id' => 'btn-save-asesmen-dokter',
                        ],
                    ],
                    'custom-print' => [
                        'title' => Yii::t('fe', 'Cetak'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'data-options'=>'click',
                            'id' => 'btn-print-asesmen-dokter',
                        ],
                    ],
                ]) ?>
            </div>
            <div class="panel-body">

                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'form-asesmen-dokter',
                        'type' => ActiveForm::TYPE_VERTICAL,
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]); 
                ?>
                <div class="row">
                    <div class="col-lg-6">
                        <?=Html::activeHiddenInput($model, 'dokter_id', ['class'=>'form-control'])?>
                        <?=Html::activeHiddenInput($model, 'pendaftaran_id', ['class'=>'form-control'])?>
                        <?=Html::activeHiddenInput($model, 'asesmenmedisrd_id', ['class'=>'form-control'])?>
                        <?=$form->field($model, 'dokter_jaga')->textInput(['readonly'=>true]);?>
                        <?=$form->field($model, 'tgl_asesmen')->textInput(['readonly'=>true]);?>
                        <?= $form->field($model, 'jenis_asmenperawat')
                            ->radioList(
                                $list_jenis_asmenperawat,
                                ['inline'=>true]
                            );
                        ?>
                        <?=$form->field($model, 'riwayat')->textArea()?>
                        <?= $form->field($model, 'riwayat_dahulu')->widget(Select2::classname(),[
                                'showToggleAll' => false,
                                'options' => [
                                    'multiple' => true,
                                    'placeholder' => '-- Pilih --'
                                ],
                                'pluginOptions' => [
                                    'tags' => true,
                                    'tokenSeparators' => [',', '_'],
                                    'maximumInputLength' => 50,
                                    // 'allowClear' => true,
                                    // 'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                    ],
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['/igd/end-point/get-new-diagnosa']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('
                                            function(params) {
                                                return {
                                                    q: params.term,
                                                    type: "diagnosa_masuk",
                                                    all_text: 0,
                                                    id_with_text: 1,
                                                    page:params.page || 1
                                                }; 
                                            }
                                        '),
                                        'processResults' => new JsExpression('
                                            function (data, params) {
                                                            params.page = params.page || 1;
                                                            return {
                                                            results: data.result,
                                                                pagination: {
                                                                    more: data.pagination.more
                                                                }
                                                            }
                                            }
                                        ')
                                    ],
                                    'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                    'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                    'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                                ],
                            ]);
                        ?>
                        <div class="form-group field-asesmenmedisrdform-riwayat_dahulu">
                            <label class="control-label col-sm-4" for="asesmenmedisrdform-riwayat_dahulu"></label>
                            <div class="col-sm-8">
                                <?php 
                                if (isset($historyPenyakit) && !empty($historyPenyakit)) {
                                    foreach ($historyPenyakit as $key => $value) {
                                        if ($value) {
                                            foreach ($value as $each) {
                                                echo "<li>" . $each['text'] . "</li>";
                                            }
                                        }
                                    }
                                } 
                                ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <?= Html::hiddenInput('key_riwayat_dahulu', json_encode($historyPenyakit), ['class' => 'readonly']) ?>
                        <?php
                         echo $form->field($model, 'diagnosakerja_id')->widget(Select2::classname(), [          
                                'initValueText' => isset($text_diagnosakerja_id) && $text_diagnosakerja_id != ''? $text_diagnosakerja_id:null,                                    
                                'options' => [
                                    'placeholder' => '-- Pilih --',
                                    'class' => 'form-control input-sm select2'
                                ],
                                'pluginOptions' => [
                                    // 'allowClear' => true,
                                    'tags' => true,
                                    'tokenSeparators' => [',', '_'],
                                    // 'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                    ],
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['/igd/end-point/get-new-diagnosa']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('
                                            function(params) {
                                                return {
                                                    q: params.term,
                                                    type: "diagnosa_masuk",
                                                    all_text: 0,
                                                    id_with_text: 1,
                                                    page:params.page || 1
                                                }; 
                                            }
                                        '),
                                        'processResults' => new JsExpression('
                                            function (data, params) {
                                                            params.page = params.page || 1;
                                                            return {
                                                            results: data.result,
                                                                pagination: {
                                                                    more: data.pagination.more
                                                                }
                                                            }
                                            }
                                        ')
                                    ],
                                    'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                    'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                    'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                                ],
                            ]);
                        ?>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h6 class="panel-title"><?=Yii::t('fe', 'Glasgow coma scale')?></h6>
                                
                            </div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <?=$form->field($model, 'gcseye_id')->dropDownList(ArrayHelper::map($data_gcsEye, 'metodegcs_id', 'nama_and_nilai'), ['class'=>'select2 gcs_eye','prompt'=>'--Pilih--','options'=>$gcsEyeOptions])?>
                                    <?=$form->field($model, 'gcsverbal_id')->dropDownList(ArrayHelper::map($data_gcsVerbal, 'metodegcs_id', 'nama_and_nilai'), ['class'=>'select2 gcs_verbal','prompt'=>'--Pilih--','options'=>$gcsVerbalOptions])?>
                                    <?=$form->field($model, 'gcsmotorik_id')->dropDownList(ArrayHelper::map($data_gcsMotorik, 'metodegcs_id', 'nama_and_nilai'), ['class'=>'select2 gcs_motorik','prompt'=>'--Pilih--','options'=>$gcsMotorikOptions])?>
                                    
                                    <?=$form->field($model, 'jumlah_gcs')->textInput(['class'=>'nilai_gcs', 'readonly'=>true]);?>
                                    <?=$form->field($model, 'is_kapitis')->checkbox()?>
                                    <?=$form->field($model, 'hasil_gcs')->textInput(['class'=>'hasil_gcs','readonly'=>true])?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Pemeriksaan Anggota Tubuh') ?></h5>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div class="image-frame">
                                <?php
                                    echo Html::img('@web/media/img/img-pemeriksaan/bagian_tubuh.jpg', ['class'=>'img-responsive']);
                                ?>
                                </div>
                                <!-- modal anatomi start -->
                                <div class="tag" style="display: none" data-show="1">
                                    <span style="box-sizing: border-box;position: absolute;border: 6px solid #b93d3d;border-color: transparent transparent #ff0000 #ff0000;transform-origin: 0 0;transform: rotate(135deg);box-shadow: -3px 3px 3px -3px rgba(0,0,0,0.3);margin-left: 18px;"></span>
                                    <div class="well well-sm" style="min-height:130px;">
                                        <div class="form-group">
                                         <label class="col-lg-3">Bagian<sup style="color: red">*</sup></label>
                                            <div class="col-lg-9">
                                                <?php echo Html::dropDownList('bagain_tubuh', null, ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'),
                                                               array(
                                                                'class' => 'form-control bagian-tubuh',
                                                                'style' => 'padding : 9px 12px !important;',
                                                                'empty' => '-- Pilih --',
                                                               )); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                         <label class="col-lg-3">Detail Bagian<sup style="color: red">*</sup></label>
                                            <div class="col-lg-9">
                                                <?php echo Html::dropDownList('bagain_tubuh', null, [],
                                                               array(
                                                                'class' => 'form-control bagian-tubuh-detail',
                                                                'style' => 'padding : 9px 12px !important;',
                                                                'empty' => '-- Pilih --',
                                                               )); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                         <label class="col-lg-3">Catatan<sup style="color: red">*</sup></label>
                                            <div class="col-lg-9">
                                                <input type="text"
                                                       placeholder="catatan.." 
                                                       class="form-control add-caption">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-lg-12">
                                                <p class="helper-text ">(tekan <b>enter</b> untuk selesai)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- modal anatomi end -->
                            </div>
                            <div class="col-md-6">
                                <div class="col-lg-6">
                                    <h6 class="panel-title"><?=Yii::t('fe', 'Tabel Pemeriksaan Anatomi Tubuh')?></h6>
                                </div>
                                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-anggotatubuh">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th>No</th>
                                            <th><?=Yii::t('fe', 'Tanggal periksa')?></th>
                                            <th><?=Yii::t('fe', 'Bagian tubuh')?></th>
                                            <th><?=Yii::t('fe', 'Bagian tubuh detail')?></th>
                                            <th><?=Yii::t('fe', 'Keterangan')?></th>
                                            <th><?=Yii::t('fe', 'Aksi')?></th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        <!-- table data -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$jsonBagianTubuh = json_encode(ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'));
$jsonDetailBagianTubuh = json_encode($data_detailbagiantubuh);
$this->registerJs("
    $(document).ready(function(){
        $('#form-asesmen-dokter :input').not('#btn-print-asesmen-dokter').prop('disabled', ".$status_disabled.");
        $('#btn-save-asesmen-dokter').prop('disabled', ".$status_disabled.");
        addRow();
        setTimeout(function(){
            $('.hapus-item').prop('disabled', ".$status_disabled.");
        },500)
        $('.input-group-addon').".$hide.";
    });

    var removeDisable = function(){
        $('.btn-cetak-asesmen').removeClass('disabled')
    }
    $('.btn-cetak-asesmen').on('click', function(){
        let url = window.location.origin;
        let target = $(this).attr('data-target');
        window.open(url+target);
    })
    var tabel_anggotatubuh = $('.tabel-anggotatubuh').DataTable({
        filter: false,
        bLengthChange: false,
        bInfo: false,
        processing: true,
        paging: false,
    });

    // define data master map
    var tmpData = ".$jsonAnatomi."
    var bagianTubuh = ".$jsonBagianTubuh."
    var detailBagianTubuh = ".$jsonDetailBagianTubuh."
    var counter = ".$counter."
    var dataGcs = ".json_encode($data_gcs)."
    var listGcs = ".json_encode($data_listgcs)."
", View::POS_END, 'js2');
$this->registerJs($this->render('js/asesmendokter.js'), View::POS_END, 'js')

?>