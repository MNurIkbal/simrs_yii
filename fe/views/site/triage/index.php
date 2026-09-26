<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

$this->title = 'Form Triage';
$this->context->layout = 'triage';
?>
<style>
    .btn-triage-group {
        margin-bottom: 5px;
    }

    .btn-bed-option {
        margin-bottom: 5px;
    }

    .btn-triage-option {
        margin-bottom: 3px;
        max-width: 150px !important;
        white-space: normal;
    }

    .content-wrapper {
        padding: 0 2%;
        display: block !important;
    }
    .float{
        position: fixed;
        right: 28px;
        top: 24px;
        bottom:0;
        overflow-y:scroll;
        overflow-x:hidden;
}
</style>
<div class="row">
    <div class="form-triase">
        <?php
        $form = ActiveForm::begin([
            'id' => 'form-triase',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'formConfig' => [
                'labelSpan' => 4,
                'deviceSize' => ActiveForm::SIZE_SMALL,
            ],
        ]);
        ?>

        <div class="col-md-10">
            <div class="panel panel-default">

                    <div class="panel-heading">
                        <div class="row panel-triage">
                            <div class="col-md-4">
                                <div class="form-group mt-2">
                                    <input type="text" name="nip-scan" id="nip-scan" class="form-control nip-scan" placeholder="Scan NIP Disini" required autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2">
                                    <label class="control-label">Petugas Triase</label>
                                    <p class="label-value-form" style="margin: -4px 0 -10px 0;" id="petugas-triase">-</p>
                                    <?= Html::activeHiddenInput($model, 'pegawai_id') ?>
                                    <?= Html::activeHiddenInput($model, 'kelompokpegawai_id') ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-2">
                                    <label class="control-label">Tanggal Triase</label>
                                    <p class="label-value-form" style="margin: -4px 0 -10px 0;" id="tgl-triase"></p>
                                    <?= Html::activeHiddenInput($model, 'tgl_triase') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 mt-2.5">
                                <div class="panel panel-default">
                                    <a id="info-heading-airway  " data-toggle="collapse" href="#airway-tab" role="button" aria-expanded="false" aria-controls="airway-tab">
                                        <div class="panel-heading flex-container">
                                            <h6 class="panel-title text-bold">Airway (Jalan Napas)</h6>
                                            <ul class="icons-list">
                                                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                            </ul>
                                        </div>
                                    </a>

                                    <div class="panel-body collapse multi-collapse label-information mt-5" id="airway-tab">
                                        <div class="jalan_nafas-group">
                                            <?= Html::activeHiddenInput($model, 'jalan_nafas') ?>
                                            <?php foreach ($configData['jalan_napas_extra'] as $key => $value) : ?>
                                                <div class="row <?= $key ?>-group btn-triage-group">
                                                    <div class="col-md-2">
                                                        <b><?= str_replace('_', ' ', ucwords($key, '_')) ?></b>
                                                    </div>
                                                    <?php foreach ($value as $k => $v) : ?>
                                                        <div class="col-md-2 btn-triage-smaller <?= $k ?>-group">
                                                            <?php foreach ($v as $kunci => $nilai) : ?>
                                                                <button class="btn btn-triage-option" value="<?= $kunci ?>" data-btn_key="<?= $kunci ?>" data-triage_group="jalan_napas_extra" type="button"><?= $nilai ?></button>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <a id="info-heading-breathing" data-toggle="collapse" href="#breathing-tab" role="button" aria-expanded="false" aria-controls="breathing-tab">
                                        <div class="panel-heading flex-container">
                                            <h6 class="panel-title text-bold">Breathing (Pernapasan)</h6>
                                            <ul class="icons-list">
                                                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                            </ul>
                                        </div>
                                    </a>

                                    <div class="panel-body collapse multi-collapse label-information mt-5" id="breathing-tab">
                                        <div class="pernapasan-group">
                                            <?= Html::activeHiddenInput($model, 'pernafasan') ?>
                                            <?php foreach ($configData['pernapasan_extra'] as $key => $value) : ?>
                                                <div class="row <?= $key ?>-group btn-triage-group">
                                                    <div class="col-md-2">
                                                        <b><?= str_replace('_', ' ', ucwords($key, '_')) ?></b>
                                                    </div>
                                                    <?php foreach ($value as $k => $v) : ?>
                                                        <div class="col-md-2 btn-triage-smaller <?= $k ?>-group">
                                                            <?php foreach ($v as $kunci => $nilai) : ?>
                                                                <button class="btn btn-triage-option" data-btn_key="<?= $kunci ?>" data-triage_group="pernapasan_extra" value="<?= $kunci ?>" type="button"><?= $nilai ?></button>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <a id="info-heading" data-toggle="collapse" href="#circulation-tab" role="button" aria-expanded="false" aria-controls="circulation-tab">
                                        <div class="panel-heading flex-container">
                                            <h6 class="panel-title text-bold">Circulation (Sirkulasi)</h6>
                                            <ul class="icons-list">
                                                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                            </ul>
                                        </div>
                                    </a>

                                    <div class="panel-body collapse multi-collapse label-information mt-5" id="circulation-tab">
                                        <div class="sirkulasi-group">
                                            <?= Html::activeHiddenInput($model, 'sirkulasi') ?>
                                            <?php foreach ($configData['sirkulasi_extra'] as $key => $value) : ?>
                                                <div class="row <?= $key ?>-group btn-triage-group">
                                                    <div class="col-md-2">
                                                        <b><?= str_replace('_', ' ', ucwords($key, '_')) ?></b>
                                                    </div>
                                                    <?php foreach ($value as $k => $v) : ?>
                                                        <div class="col-md-2 btn-triage-smaller <?= $k ?>-group">
                                                            <?php foreach ($v as $kunci => $nilai) : ?>
                                                                <button class="btn btn-triage-option" data-btn_key="<?= $kunci ?>" data-triage_group="sirkulasi_extra" value="<?= $kunci ?>" type="button"><?= $nilai ?></button>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <a id="info-heading" data-toggle="collapse" href="#consciousness-tab" role="button" aria-expanded="false" aria-controls="consciousness-tab">
                                        <div class="panel-heading flex-container">
                                            <h6 class="panel-title text-bold">Consciousness (Kesadaran)</h6>
                                            <ul class="icons-list">
                                                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                            </ul>
                                        </div>
                                    </a>

                                    <div class="panel-body collapse multi-collapse label-information mt-5" id="consciousness-tab">
                                        <div class="row form-row select2-md">
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'gcseye_id', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->dropDownList([], ['id' => 'gcseyeForm', 'prompt' => 'Pilih']) ?>
                                            </div>
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'gcsverbal_id', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->dropDownList([], ['id' => 'gcsverbalForm', 'prompt' => 'Pilih']) ?>
                                            </div>
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'gcsmotorik_id', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-6']])->dropDownList([], ['id' => 'gcsmotorikForm', 'prompt' => 'Pilih']) ?>
                                            </div>
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'hasil_gcs', ['horizontalCssClasses' => ['label' => 'col-sm-2', 'wrapper' => 'col-sm-2']])->textInput(['class' => 'default-disabled', 'readonly' => true]) ?>
                                                <hr>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <a id="info-heading" data-toggle="collapse" href="#disability-tab" role="button" aria-expanded="false" aria-controls="disability-tab">
                                        <div class="panel-heading flex-container">
                                            <h6 class="panel-title text-bold">Disability (Gangguan Lain)</h6>
                                            <ul class="icons-list">
                                                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                            </ul>
                                        </div>
                                    </a>

                                    <div class="panel-body collapse multi-collapse label-information mt-5" id="disability-tab">
                                        <div class="disability-group">
                                            <?= Html::activeHiddenInput($model, 'disability') ?>
                                            <?php foreach ($configData['disability'] as $key => $value) : ?>
                                                <div class="row <?= $key ?>-group btn-triage-group">
                                                    <div class="col-md-2">
                                                        <b><?= str_replace('_', ' ', ucwords($key, '_')) ?></b>
                                                    </div>
                                                    <?php foreach ($value as $k => $v) : ?>
                                                        <div class="col-md-2 btn-triage-smaller <?= $k ?>-group">
                                                            <?php foreach ($v as $kunci => $nilai) : ?>
                                                                <button class="btn btn-triage-option" data-btn_key="<?= $kunci ?>" data-triage_group="disability"  value="<?= $kunci ?>" type="button"><?= $nilai ?></button>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default">
                                    <a id="info-heading" data-toggle="collapse" href="#vitalsign-tab" role="button" aria-expanded="false" aria-controls="vitalsign-tab">
                                        <div class="panel-heading flex-container">
                                            <h6 class="panel-title text-bold">Tanda Vital (Vital Sign)</h6>
                                            <ul class="icons-list">
                                                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                            </ul>
                                        </div>
                                    </a>

                                    <div class="panel-body collapse multi-collapse label-information mt-5" id="vitalsign-tab">
                                        <div class="row form-row">
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'tekanan_darah_sistolik', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput(['class' => 'doco-number']); ?>
                                            </div>
                                        </div><div class="row form-row">
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'tekanan_darah_diastolik', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput(['class' => 'doco-number']); ?>
                                            </div>
                                        </div>
                                        <div class="row form-row">
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'nadi', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']); ?>
                                            </div>
                                        </div>
                                        <div class="row form-row">
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'nafas', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']); ?>
                                            </div>
                                        </div>
                                        <div class="row form-row">
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'suhu', ['addon' => ['append' => ['content' => '°C']]])->textInput(['class' => 'doco-decimal-wcomma']); ?>
                                            </div>
                                        </div>
                                        <div class="row form-row">
                                            <div class="col-sm-12">
                                                <?= $form->field($model, 'saturasi_oksigen', ['addon' => ['append' => ['content' => '%']]])->textInput(['class' => 'doco-decimal-wcomma']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                 
            
            </div>
        </div>
        <div class="col-md-2">
            <div class="col-md-2 float">
                                <div class="panel panel-default">
                                    <div class="panel-body" >
                                        <div class="row form-group" >
                                            <div class="col-md-12">
                                                <label class="control-label">Bed <b class="text-danger">*</b></label>
                                            </div>
                                            <div class="col-md-12 btn-triage-group">
                                                <?php foreach ($dataBed as $key => $value) : ?>
                                                    <button class="btn btn-bed-option <?=isset($value['status_edit']) && $value['status_edit'] ? ' editable" style="border-color: #1ca189;" title="Data Triase Sudah Terisi"' : '"'?>  <?=($value['status_isi'] == true && isset($value['status_edit']) == false) ? 'disabled' : ''?> type="button" data-bed-id="<?= $value['kamartempattidur_id'] ?>"><?= $value['no_tempattidur'] ?></button>
                                                <?php endforeach; ?>
                                                <button class="btn btn-info mb-5 hidden" id="add-bed">+</button>
                                                <?= Html::activeHiddenInput($model, 'kamartempattidur_id') ?>
                                                <p class="text-danger" id="bed-validation"><strong>*</strong> No. Bed Wajib Diisi!</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Kategori Triase</label>
                                            <p class="label-value-form" id="kategori-triase">-</p>
                                            <?= Html::activeHiddenInput($model, 'hasil_triase') ?>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Waktu Respon</label>
                                            <p class="label-value-form" id="waktu-respon">-</p>
                                            <?= Html::activeHiddenInput($model, 'waktu_respon') ?>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Observation Site</label>
                                            <p class="label-value-form" id="observation-site">-</p>
                                            <?= Html::activeHiddenInput($model, 'observation_site') ?>
                                        </div>
                                        <div class="form-group hidden">
                                            <label for="" class="control-label">Pelaporan</label>
                                            <button class="btn btn-default btn-md btn-pelaporan" id="btn-pelaporan">True x False Emergency</button>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <div class="col-md-12">
                                        <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-info btn-block btn-form" id="btn-save-triage" disabled>Simpan</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="reset" class="btn btn-secondary btn-block btn-form" id="btn-reset-triage" disabled>Batal</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <hr>
                                                    <button class="btn btn-black btn-md btn-form" style="width: 100%" id="btn-doa" disabled>
                                                        DOA
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJs("
    var configData = " . json_encode($configData) . ";
    var dataGcs = " . json_encode($data_listgcs) . ";
    var configRules = " . json_encode($configRules) . ";
    ", View::POS_END);
$this->registerJs($this->render('_index.js'), View::POS_END);
?>
