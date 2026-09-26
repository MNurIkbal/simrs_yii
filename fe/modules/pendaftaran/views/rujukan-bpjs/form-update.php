<?php
/**
* @Author: Sigit
* @Date:   2020-12-29 09:18:05
*/

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rujukan Pasien Keluar (BPJS)'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'form-rujukan-bpjs',
                'enableClientValidation' => false,
                'enableAjaxValidation' => false
            ]) ?>
            
            <div class="panel-body">
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <?= $form->field($model, 'nosep')->textInput([
                                'id' => 'nosep',
                                'class' => 'form-control input-sm',
                                'placeholder' => $model->getAttributeLabel('nosep')
                            ])->label(false) ?>
                        </div>
                        <div class="col-md-3" style="margin-left:0%!important;">
                            <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', 'Cari'), [
                                'class' => 'btn btn-info btn-labeled btn-xs',
                                'id' => 'btn-cari-sep'
                            ]) ?>
                            <?= Html::button('<b><i class="fa fa-refresh"></i></b>'.Yii::t('fe', 'Batal'), [
                                'class' => 'btn btn-danger btn-labeled btn-xs',
                                'id' => 'btn-batal-sep'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body" id="content-rujukan" style="display:none;">
                <div class="row" style="margin-top:1%;">
                    <div class="col-md-3">
                        <?php echo Yii::$app->controller->renderPartial('infopasien'); ?>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'tanggal_rujukan', [
                                    'addon' => [
                                        'append' => [
                                            'content' => '<i class="fa fa-calendar"></i>'
                                        ]
                                    ]
                                ])->textInput([
                                    'id' => 'tanggal_rujukan',
                                    'class' => 'form-control input-sm',
                                    'placeholder' => $model->getAttributeLabel('tanggal_rujukan'),
                                    'autocomplete' => 'off',
                                    'readonly' => true
                                ])->label($model->getAttributeLabel('tanggal_rujukan')) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'diagnosa_rujukan')->widget(Select2::classname(), [
                                    'options' => [
                                        'id' => 'diagnosa_rujukan'
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 3,
                                        'language' => [
                                            'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                        ],
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['/pendaftaran/rujukan-bpjs/get-diagnosa']),
                                            'dataType' => 'json',
                                            'delay' => 500,
                                            'data' => new JsExpression('
                                                function(params) {
                                                    return {
                                                        q:params.term
                                                    };
                                                }
                                            ')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression(
                                            'function(diagnosa) {
                                                return diagnosa.text;
                                            }'
                                        ),
                                        'templateSelection' => new JsExpression(
                                            'function (diagnosa) {
                                                $("#diagnosa_rujukan_nama").val(diagnosa.text);
                                                return diagnosa.text;
                                            }'
                                        ),
                                    ],
                                    'pluginEvents' => [
                                        'select2:select' => 'function(res) {

                                        }'
                                    ],
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'tanggal_rencana_kunjungan', [
                                    'addon' => [
                                        'append' => [
                                            'content' => '<i class="fa fa-calendar"></i>'
                                        ]
                                    ]
                                ])->textInput([
                                    'id' => 'tanggal_rencana_kunjungan',
                                    'class' => 'form-control input-sm pickadate-w-month',
                                    'placeholder' => $model->getAttributeLabel('tanggal_rencana_kunjungan'),
                                    'autocomplete' => 'off',
                                    'readonly' => false
                                ])->label($model->getAttributeLabel('tanggal_rencana_kunjungan')) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'dirujukke', [
                                    'horizontalCssClasses' => [
                                        'label' => 'col-md-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                    'addon' => [
                                        'append' => [
                                            'content' => Html::a('<i class="fa fa-hospital-o "></i>',null, [
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_pencarian_rujukan',
                                                'data-width' => '1000px',
                                                'data-popup' => "tooltip",
                                                'id' => 'btn-pencarian-rujukan',
                                                'action' =>'/pendaftaran/rujukan-bpjs/modal-pencarian-rujukan',
                                                'title' => Yii::t("fe","Pencarian Rujukan")
                                            ])
                                        ],
                                        'class' => 'asdasd'
                                    ]
                                ])->textInput([
                                    'id' => 'dirujukke',
                                    'class' => 'form-control',
                                    'placeholder' => $model->getAttributeLabel('dirujukke'),
                                    'readonly' => true
                                ])->label($model->getAttributeLabel('dirujukke')) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'jenis_pelayanan_bpjs')->dropDownList(
                                    $options['jenisPelayanan'], [
                                        'id' => 'jenis_pelayanan_bpjs',
                                        'class' => 'select2'
                                    ]
                                )->label() ?>
                            </div>
                            <div class="col-md-6" id="field-spesialis">
                                <?= $form->field($model, 'spesialis')->textInput([
                                    'id' => 'spesialis',
                                    'class' => 'form-control',
                                    'placeholder' => $model->getAttributeLabel('spesialis atau subspesialis'),
                                    'readonly' => true
                                ])->label($model->getAttributeLabel('spesialis')) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'rujukan')->dropDownList(
                                    $options['tipeRujukan'], [
                                        'id' => 'rujukan',
                                        'class' => 'select2'
                                    ]
                                )->label($model->getAttributeLabel('rujukan')) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'catatan_rujukan')->textarea([
                                    'id' => 'catatan_rujukan',
                                    'class' => 'form-control',
                                    'rows' => '3',
                                ])->label($model->getAttributeLabel('catatan_rujukan')) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>'. Yii::t('fe','Simpan'), [
                    'class' => 'btn btn-info btn-labeled btn-xs',
                    'style' => 'position:absolute;right:10px;bottom:10px;'
                ]); ?>
            </div>
            
            <?= Html::activeHiddenInput($model, 'pendaftaran_id', ['id' => 'pendaftaran_id']); ?>
            <?= Html::activeHiddenInput($model, 'pasienadmisi_id', ['id' => 'pasienadmisi_id']); ?>
            <?= Html::activeHiddenInput($model, 'bpjs_id', ['id' => 'bpjs_id']); ?>
            <?= Html::activeHiddenInput($model, 'poli_rujukan', ['id' => 'poli_rujukan']); ?>
            <?= Html::activeHiddenInput($model, 'kode_ppkrujukan', ['id' => 'kode_ppkrujukan']); ?>
            <?= Html::activeHiddenInput($model, 'dirujukke_nama', ['id' => 'dirujukke_nama']); ?>
            <?= Html::activeHiddenInput($model, 'kode_spesialis', ['id' => 'kode_spesialis']); ?>
            <?= Html::activeHiddenInput($model, 'no_rujukan', ['id' => 'no_rujukan']); ?>
            <?= Html::activeHiddenInput($model, 'diagnosa_rujukan_nama', ['id' => 'diagnosa_rujukan_nama']); ?>
            <?= Html::hiddenInput('jenis_pelayanan', null, ['id' => 'jenis_pelayanan']); ?>
            <?= Html::hiddenInput('tmp_tgl_kunjungan', null, ['id' => 'tmp_tgl_kunjungan']); ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div id="modal_pencarian_rujukan" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$this->registerJs('
    var url_print = "'.Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-rujukan'.'";
    var url = "'.Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/update'.'";
    $(".namapasien").text("'.$peserta['nama'].'");
    $(".nomorrekammedik").text("'.$peserta['mr']['noMR'].'");
    $(".nosep").text("'.$additional_request['noSep'].'");
    $(".tglsep").text("'.date('Y-m-d', strtotime($sep['tglSep'])).'");
    $(".jenispelayanan").text("'.$sep['jnsPelayanan'].'");
    $(".diagnosa").text("'.$sep['diagnosa'].'");
    $(".nokartubpjs").text("'.$peserta['noKartu'].'");
    $(".namapeserta").text("'.$peserta['nama'].'");
    $(".tanggallahir").text("'.$peserta['tglLahir'].'");
    $(".jeniskelamin").text("'.(($peserta['sex'] == 'P') ? "Perempuan" : "Laki-laki").'");
    $(".hakkelas").text("'.$peserta['hakKelas']['keterangan'].'");

    $("#tanggal_rujukan").val("'.date('Y-m-d', strtotime($data_rujukan['tanggal_rujukan'])).'");
    $("#pendaftaran_id").val("'.$data_rujukan['pendaftaran_id'].'");
    $("#pasienadmisi_id").val("'.$data_rujukan['pasienadmisi_id'].'");
    $("#bpjs_id").val("'.$data_rujukan['bpjs_id'].'");
    $("#poli_rujukan").val("'.$data_rujukan['poli_rujukan'].'");
    $("#jenis_pelayanan_bpjs").val("'.$data_rujukan['jenis_pelayanan_bpjs'].'").trigger("change");
    $("#dirujukke").val("'.$data_rujukan['dirujukke_nama'].'");
    $("#rujukan").val("'.$data_rujukan['rujukan'].'");
    $("#catatan_rujukan").val('.str_replace('<br>','\n', $catatan_rujukan).');
    $("#spesialis").val("'.$data_rujukan['spesialis'].'");
    $("#kode_spesialis").val("'.$additional_request['kodeSpesialis'].'");
    $("#nosep").val("'.$additional_request['noSep'].'");
    $("#tmp_tgl_kunjungan").val("'.$additional_request['tglRencanaKunjungan'].'");
    $("#jenis_pelayanan").val("'.$additional_request['jnsPelayanan'].'");
    $("#kode_ppkrujukan").val("'.$additional_request['ppkDirujuk'].'");
    $("#dirujukke_nama").val("'.$data_rujukan['dirujukke_nama'].'");
    $("#no_rujukan").val("'.$data_rujukan['no_rujukan'].'");
    $("#diagnosa_rujukan_nama").val("'.$data_rujukan['diagnosa_rujukan_nama'].'");
    $("#tanggal_rencana_kunjungan").val("'.$data_rujukan['tanggal_rencana_kunjungan'].'");
    
    var kode = "'.$data_rujukan['diagnosa_rujukan'].'";
    var nama = "'.$data_rujukan['diagnosa_rujukan_nama'].'";
    var nmProvider = "'.$peserta['provUmum']['nmProvider'].'";
    var kdProvider = "'.$peserta['provUmum']['kdProvider'].'";
    var newOption = new Option(nama, kode, false, false);
    $("#diagnosa_rujukan").append(newOption);
    $("#diagnosa_rujukan").val(kode).trigger("change");

    // Show content rujukan
    $("#content-rujukan").show();

    if ($("#rujukan").val() == 2) {
        $("#field-spesialis").hide();
        $("#dirujukke").val("'.$peserta['provUmum']['nmProvider'].'");
        $("#dirujukke_nama").val("'.$peserta['provUmum']['nmProvider'].'");
        $("#kode_ppkrujukan").val("'.$peserta['provUmum']['kdProvider'].'");
        $(".faskes").text("'.$peserta['provUmum']['kdProvider'].'"+" - "+"'.$peserta['provUmum']['nmProvider'].'");
    }
', View::POS_END, 'index');

$this->registerJs($this->render('js/form-update.js'), View::POS_END);
$this->registerJs($this->render('js/set-tgl.js'), View::POS_END);
?>

