<?php

/**
 * @author Randy Vianda Putra
 * @todo View Input Hasil Scan Radiologi
 * @copyright 27 Juli 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use kartik\datetime\DateTimePicker;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;

$this->title = \Yii::t('fe', 'Input Hasil Radiologi');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Beranda'), 'url' => ['/laboratorium']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Transaksi')];
$this->params['breadcrumbs'][] = $title;

?>

<style lang="">
    .js .inputfile {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }
    #file-1 {
        display:none;
        margin: 10px;
    }
    .inputfile + label {
        max-width: 100%;
        font-size: 1.25rem;
        /* 20px */
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        display: inline-block;
        overflow: hidden;
        /* padding: 0.625rem 1.25rem; */
        padding: 7px 45px;
        max-width: 350px;
        /* 10px 20px */
    }

    .no-js .inputfile + label {
        display: none;
    }

    .inputfile:focus + label,
    .inputfile.has-focus + label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }

    .inputfile + label * {
        /* pointer-events: none; */
        /* in case of FastClick lib use */
    }

    .inputfile + label svg {
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
        margin-top: -0.25em;
        /* 4px */
        margin-right: 0.25em;
        /* 4px */
    }


    .inputfile-1 + label {
        color: #ffffff;
        background-color: #009ACD;
        
    }

    .inputfile-1:focus + label,
    .inputfile-1.has-focus + label,
    .inputfile-1 + label:hover {
        background-color: #00688B;
    }

    .lurus {
        float: left;
        margin-left: 5px;
    }

    .link-upload {
        font-size: 14px;
        color: black;
        margin: 5px;
        padding: 5px;
        font-weight: bold;
    }

    .content {
        min-height: 480px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?= 
                    Html::button('<b><i class="fa fa-arrow-left"></i></b>' . \Yii::t('fe', 'Kembali'), [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-kembali',
                        'id' => 'btn-kembali',
                        'data-options' => 'link',
                        'data-target' => \Yii::$app->request->referrer
                    ]) 
                ?>
                <?php
                    $disabled = ($status == DocoConstants::ST_SELESAI_PNNJG) ? true : false;
                ?>
                <?= 
                    Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-save',
                        'disabled' => $disabled
                    ])
                ?>
            </div>

            <div class="panel-body content">
                    <?php 
                        $form = ActiveForm::begin([
                            'id' => 'form-hasil',
                            'action' => '/radiologi/input-hasil/save-upload',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                            'options' => [
                                'role' => 'form',
                                'enctype' => 'multipart/form-data'
                            ]
                        ]);
                    ?>
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <div class="form-group field-uploadhasilform-pemeriksaan">
                                <label class="control-label text-left control-label col-sm-4" for="uploadhasilform-pemeriksaan"><?= Yii::t('fe', 'Nama pemeriksaan') ?></label>
                                <label class="control-label text-left col-sm-8">
                                    <?= $tindakan; ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <?= 
                                $form->field($model, 'pegawairad_id', [
                                    'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList($data_pegawai, [
                                    'class' => 'form-control input-sm pegawai select2',
                                    'prompt' => Yii::t('fe', '-- Masukan petugas radiologi --'),
                                    'value' => $penanggungjawab_id
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <div class="form-group field-uploadhasilform-upload">
                                <label class="control-label text-left control-label col-sm-4" for="uploadhasilform-upload"><?= Yii::t('fe', 'Upload hasil scan') ?></label>
                                <div class="col-md-8">
                                    <?=
                                        Html::button('<b><i class="fa fa-plus"></i></b>' . \Yii::t('fe', 'Tambah berkas'), [
                                            'class' => 'btn btn-info btn-labeled btn-xs',
                                            'id' => 'add-upload',
                                            'disabled' => $disabled
                                        ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?= Html::hiddenInput('hasilpemeriksaanrad_id', $hasilpemeriksaanrad_id, ['class' => 'hasilpemeriksaanrad_id']); ?>
                        <?= Html::hiddenInput('penunjang_id', $penunjang_id, ['class' => 'penunjang_id']); ?>
                        <div class="col-md-8 col-xs-offset-1" style="margin-left: 178px">
                            * <b><?= Yii::t('fe', 'maksimal 250 mb'); ?></b>
                        </div>
                        <?php
                            if (!empty($data_upload)) {
                                foreach ($data_upload as $key => $value) {
                                    $disable = $disabled ? 'disabled' : '';
                        ?>
                                <div class="col-md-8 col-xs-offset-1" style="margin-left: 178px;margin-bottom:5px;">
                                    <div class="lurus">
                                        <button 
                                            type="button" 
                                            data-id="<?= $value['hasilpemeriksaanraddetail_id'] ?>" 
                                            data-parent="<?= $folderName ?>"
                                            class="btn btn-sm btn-block btn-danger delete" 
                                            <?= $disable ?>
                                        ><i class="fa fa-trash"></i></button>
                                    </div>
                                    <div class="lurus">
                                        <input type="hidden" class="form-control">
                                        <label class="" style="margin-top:5px;">
                                            <a href="/media/input-hasil-rad/<?= $folderName .'/'. $value['upload_file']?>" target="blank" class="link-upload">
                                                <?= $value['upload_file'] ?>
                                            </a>
                                        </label>
                                        <div class="error-upload"></div>
                                    </div>
                                    <div class="lurus">
                                        <div class="col-md-12">
                                            <input 
                                                class="form-control catatan" 
                                                value="<?= $value['catatan'] ?>" 
                                                name="" 
                                                type="text" 
                                                style="width:400px;" 
                                                placeholder="Masukan catatan" 
                                                data-id="<?= $value['hasilpemeriksaanraddetail_id'] ?>"
                                            >
                                        </div>
                                    </div>
                                </div>
                        <?php
                                }
                            } else {
                        ?>
                            <div class="col-md-8 col-xs-offset-1 upload-section" style="margin-left: 178px" id="upload-section">
                                <div class="lurus">
                                    <button type="button" class="btn btn-sm btn-block btn-danger delete"><i class="fa fa-trash"></i></button>
                                </div>
                                <div class="lurus">
                                    <input type="file" name="UploadHasilForm[upload_file][]" id="file-1" class="form-control inputfile inputfile-1" multiple=true>
                                    <label for="file-1">
                                        <i class="fa fa-upload"></i>
                                        <span id="label-file">Pilih Berkas</span>
                                    </label>
                                    <div class="error-upload"></div>
                                </div>
                                <div class="lurus">
                                    <div class="col-md-12">
                                        <input class="form-control" name="UploadHasilForm[catatan][]" type="text" style="width:400px;" placeholder="Masukan catatan">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div id="list-upload"></div>
                    </div>
                    <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs("
        var MAX_UPLOAD = {$maxUpload}
    ", VIEW::POS_END, 'js-kunings');
    $this->registerJs($this->render('js/upload-hasil.js'), View::POS_END);

?>
