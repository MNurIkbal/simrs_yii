<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-10 16:55:44
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-11 16:55:12
 */

use app\components\DocoConstants;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$disabled = false;
if (!$diagnosa['state']) {
    $disabled = true;
}

$disableKoreksi = false;
if (in_array($info['status_kunjungan'], [DocoConstants::STATUS_VERIFIKASI_BPJS_PRS, DocoConstants::STATUS_VERIFIKASI_BPJS_FNL])) {
    $disableKoreksi = true;
}

?>
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'back',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'koreksi' => [
                        'title' => \Yii::t('fe', 'E-Klaim'),
                        'icon' => 'fa fa-folder',
                        'attributes' => [
                            'id'   => 'btn-koreksi',
                            'data-options' => 'click',
                            'data-target' => 'form-koreksi-diagnosa',
                        ]
                    ],
                ]); ?>
            </div>
            <div class="panel-body">
                <br>
                <!-- Info pasien and detail start here -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien'); ?></b></h6>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Rekam Medik") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($info['no_rekammedik']) ? $info['no_rekammedik'] : '-' ?> </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Lahir") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($info['tgl_lahir']) ? date('d M Y', strtotime($info['tgl_lahir'])) : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal pendaftaran") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($info['tgl_pendaftaran']) ? date('d M Y', strtotime($info['tgl_pendaftaran'])) : '-' ?> </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Umur") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp; <?= isset($info['umur']) ? $info['umur'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Pendaftaran") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($info['no_pendaftaran']) ? $info['no_pendaftaran'] : '-' ?> </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kelas pelayanan") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp; <?= isset($info['kelas_nama']) ? $info['kelas_nama'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama pasien") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($info['nama_pasien']) ? $info['nama_pasien'] : '-' ?> </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara bayar") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp; <?= isset($info['carabayar_nama']) ? $info['carabayar_nama'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kelamin") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($info['jenis_kelamin']) ? $info['jenis_kelamin'] : '-' ?> </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Penjamin") ?></b></label>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp; <?= isset($info['penjamin_nama']) ? $info['penjamin_nama'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <?php
                                        $filename = isset($info['photopasien']) ? !empty($info['photopasien']) ? '/media/img/pasien/' . $info['photopasien'] : '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                        ?>
                                        <?= Html::img($filename, ['style' => 'height: 150px;margin: 5px auto', 'class' => 'img-responsive']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <b>
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Detail informasi pasien') ?></h6>
                                </b>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="text-left control-label col-sm-3"><b><?= Yii::t("fe", "Ruangan") ?></b></label>
                                        <div class="col-sm-5">
                                            <p><b>:</b>&nbsp;<?= isset($info['ruangan_nama']) ? $info['ruangan_nama'] : '-' ?> </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kasus penyakit") ?></b></label>
                                        <div class="col-sm-5">
                                            <p><b>:</b>&nbsp;<?= isset($info['jeniskasuspenyakit_nama']) ? $info['jeniskasuspenyakit_nama'] : '-' ?> </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Dokter penanggung jawab") ?></b></label>
                                        <div class="col-sm-5">
                                            <p><b>:</b>&nbsp;<?= isset($info['dokter_nama']) ? $info['dokter_nama'] : '-' ?> </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="load_diagnosa"></div>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs("
    var table;
    var _id = '{$id}';
    let updateStatus = false;
    let disableEdit = '{$disableKoreksi}';
    var _info = " . json_encode($diagnosa['data']) . ";
    var _kunjunganId = " . json_encode($diagnosa['kunjungan_id']) . ";
    var _infoPasien = " . json_encode($info) . ";
    var isEditKoreksi = false;

    $(document).ready(function(){
        var _detail = " . json_encode($diagnosa['detail']) . ";
        var _disable = '{$disabled}';
        getDiagnosa();

        if(disableEdit) {
            $('.koreksi-diagnosa').attr('disabled', true);
            $('.btn-xsm').attr('disabled', true);
            $('.check-inacbg').attr('disabled', true);
            $('.radio-icdprimer').attr('disabled', true);
        }
       
        $(document).on('click','.check-inacbg', function(){
            if($(this).is(':checked')){
                $(this).closest('tr').find('.radio-icdprimer').attr('disabled', false)
            }else{
                var key = $(this).attr('data-key');
                var radioval = $(this).closest('tr').find('.radio-icdprimer').prop('checked')
                if(radioval){
                    $('input[name=\"FormKoreksi[is_icdprimer]\"]').prop('checked', false);
                }
                $(this).closest('tr').find('.radio-icdprimer').attr('disabled', true)
            }
        })
    });
    " . $this->render('proses.js'), View::POS_END, 'js');

?>