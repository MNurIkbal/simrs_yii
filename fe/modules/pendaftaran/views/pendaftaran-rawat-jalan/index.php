<?php

use app\components\DocoHelpers;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .btn-link {
        color: green;
    }

    .no-margin-left {
       margin-left: 0 !important;
    }
</style>

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
                            <b><?= Yii::$app->docoVars->workspace("modul_alias") . ($loket_nama ? ' - ' . Yii::t('fe', 'Loket') . ' ' . $loket_nama : '') ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>

                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><?= Yii::t('fe', 'Tekan enter untuk mencari') ?></li>
                        <li>
                            <div class='form-group'>
                                <input type="text" class="form-control no-antrian" name="no_antrian" autocomplete="off" placeholder="Ketik antrian manual" readonly onfocus="this.removeAttribute('readonly');" >
                            </div>
                        </li>
                        <li>
                            <button type="button" class="btn btn-primary" id="btn-pilih-antrian" action="/pendaftaran/daftar/pilih-antrian" data-width="900px" data-toggle="modal" data-target="#modal_backdrop"
                            >Panggil Antrian</button>
                        </li>
                        <li>
                            <?php echo Html::button(Yii::t('fe', 'Ubah Jenis Antrian'),[
                                'class' => 'btn btn-info btn-md',
                                'id'=>'btn-ubah-jenis-antrian',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/pendaftaran/daftar/ubah-jenis-antrian?loket_id='.$loket_id.'&jenisantrian_id='.$jenisantrian_id,
                            ]) ?>
                        </li>
                        <li>
                            <?= Html::hiddenInput('loket', $loket_nama, ['id'=>'loket']); ?>
                        </li>
                        <li class="hidden"><a data-action="collapse"></a></li>
                    </ul>
                    <?= Yii::$app->controller->renderPartial('/daftar/partial/component/info-shortcut'); ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <?= Select2::widget([
                                'model' => $modelPasien,
                                'attribute' => 'no_rekam_medik',
                                'options' => [
                                    'id' => 'no_rekam_medik',
                                    'placeholder' => Yii::t('fe','No. RM').' / '.Yii::t('fe', 'Nama Pasien'). ' / ' .Yii::t('fe', 'Tanggal Lahir')
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                    ],
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['/pendaftaran/end-point/norm']),
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
                                        'function(pasien) {
                                            return pasien.text;
                                        }'
                                    ),
                                    'templateSelection' => new JsExpression(
                                        'function (pasien) {
                                            $(".pendaftaran-id").val(pasien.pendaftaran_id);
                                            return pasien.text;
                                        }'
                                    ),
                                ],
                                'pluginEvents' => [
                                    'select2:select' => 'function(res) {
                                        var response = res.params.data;

                                        if (response.pasien_id) {
                                            getInfoPasien(response.pasien_id);
                                        }
                                    }'
                                ],
                            ]) ?>
                        </div>

                        <div class="col-md-1">
                            <?= Html::a(Yii::t('fe', 'Pencarian Lanjutan'), [
                                '/pendaftaran/pendaftaran-rawat-jalan/pencarian-lanjutan'
                            ], [
                                'class' => 'btn btn-link btn-sm',
                                'id' => 'btn-pencarian-lanjutan',
                                'data-target' => '#modal_pencarian_lanjutan',
                                'data-options' => 'link',
                                'data-toggle' => 'modal'
                            ]) ?>
                        </div>

                        <div class="col-md-3" style="margin-left:3%!important;">
                            <?= Html::button('<b><i class="fa fa-user"></i></b>'.Yii::t('fe', 'Pasien Baru'), [
                                'class' => 'btn btn-info btn-labeled btn-xs',
                                'id' => 'btn-pasien-baru'
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:1%;">
                    <div id="form-infopasien" class="col-md-3" style="display:none;">
                        <?php echo Yii::$app->controller->renderPartial('partial/infopasien'); ?>
                    </div>
                    <div id="form-kunjungan" class="col-md-9" style="display:none;">
                        <?php echo Yii::$app->controller->renderPartial('partial/kunjungan', [
                            'modelPasien' => $modelPasien,
                            'modelKunjungan' => $modelKunjungan,
                            'modelBpjs' => $modelBpjs,
                            'modelAsuransi' => $modelAsuransi,
                            'modelRujukan' => $modelRujukan,
                            'optionsKunjungan' => $optionsKunjungan
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_pencarian_lanjutan" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<?php
    $this->registerJs(''.$this->render('js/index.js'));
?>