<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$disabled = false;
$state = ArrayHelper::getValue($diagnosa, 'state', false);
$statusKunjunganId = ArrayHelper::getValue($info, 'status_kunjungan_id');
$noRekamMedik = ArrayHelper::getValue($info, 'no_rekammedik');
$noPendaftaran = ArrayHelper::getValue($info, 'no_pendaftaran');
$namaPasien = ArrayHelper::getValue($info, 'nama_pasien');

if (!$state) {
    $disabled = true;
}

$disableKoreksi = false;
if ($statusKunjunganId == 551) {
    $disableKoreksi = true;
}

$panel_info = '( ';
$panel_info .= $noRekamMedik;
$panel_info .= $noPendaftaran;
$panel_info .= $namaPasien;
$panel_info .= ' )';

?>
<style type="text/css">
    .tbl-koreksi tbody tr td {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }

    .select2-container .select2-selection--single {
        height: 100% !important;
        padding-right: 10px !important;
    }

    .select2-selection__rendered {
        word-wrap: break-word !important;
        text-overflow: inherit !important;
        white-space: normal !important;
    }

    .have-update {
        background: #b5e4b5 !important;
    }

    .padding-0 {
        padding: 0px !important;
    }
    .info-pasien {
        display: none;
    }
    .btn-xsm {
        padding: 3px 6px !important;
    }

    p.dpjp {
        padding-left: 10px;
    }

    .panel-expandable {
        cursor: pointer;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="panel-toolbar clearfix">

    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-white">
                    <div class="panel-toolbar clearfix">
                        <?= DocoHelpers::generateToolbar([
                            'simpanKoreksi' => [
                                'title' => \Yii::t('fe', 'Simpan Koreksi'),
                                'icon' => 'fa fa-save',
                                'attributes' => [
                                    'id'   => 'btn-koreksi',
                                    'data-options' => 'click',
                                    'data-target' => 'form-koreksi-diagnosa',
                                ]
                            ]
                        ]); ?>
                    </div>
                    <div class="panel-body">
                        <br>
                        <!-- Info pasien and detail start here -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default" id="panel-info-pasien">
                                    <div class="panel-heading panel-expandable">
                                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien'); ?></b> <b class="panel-info"><?= $panel_info; ?></b></h6>
                                        <div class="heading-elements">
                                            <ul class="icons-list">
                                                <li><a data-action="collapse" class="rotate-180"></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="panel-body info-pasien">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Rekam Medik") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp;<?= $noRekamMedik ?> </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Lahir") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp;<?= isset($info['tgl_lahir']) ? date('d M Y', strtotime($info['tgl_lahir'])) : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Registrasi") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp;<?= isset($info['tgl_pendaftaran']) ? date('d M Y', strtotime($info['tgl_pendaftaran'])) : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Umur") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp; <?= isset($info['umur']) ? $info['umur'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Registrasi") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp;<?= isset($info['no_pendaftaran']) ? $info['no_pendaftaran'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter Pemeriksa") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp; <?= isset($info['dokter_nama']) ? $info['dokter_nama'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama pasien") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp;<?= isset($info['nama_pasien']) ? $info['nama_pasien'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kelas pelayanan") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp; <?= isset($info['kelas_nama']) ? $info['kelas_nama'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kelamin") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp;<?= isset($info['jenis_kelamin']) ? $info['jenis_kelamin'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara bayar") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp; <?= isset($info['carabayar_nama']) ? $info['carabayar_nama'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kasus Penyakit") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp; <?= isset($info['jeniskasuspenyakit_nama']) ? $info['jeniskasuspenyakit_nama'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Penjamin") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp; <?= isset($info['penjamin_nama']) ? $info['penjamin_nama'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Ruangan") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp;<?= isset($info['ruangan_nama']) ? $info['ruangan_nama'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kasus penyakit") ?></b></label>
                                                        <div class="col-sm-7">
                                                            <p><b>:</b>&nbsp;<?= isset($info['jeniskasuspenyakit_nama']) ? $info['jeniskasuspenyakit_nama'] : '-' ?> </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7">
                                                        <label class="text-left control-label col-sm-4"><b><?= Yii::t("fe", "Dokter penanggung jawab") ?></b></label>
                                                        <div class="col-sm-8">
                                                            <p class="dpjp"><b>:</b>&nbsp;<?= isset($info['dokter_nama']) ? $info['dokter_nama'] : '-' ?> </p>
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
                        <div id="load_diagnosa"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-left"></div>

<?php

$this->registerJs("
    var table;
    var _id = '{$id}';
    var updateStatus = false;
    var disableEdit = '{$disableKoreksi}';
    var _info = " . json_encode($diagnosa['data']) . ";
    var _kunjunganId = " . json_encode($diagnosa['kunjungan_id']) . ";
    var _infoPasien = " . json_encode($info) . ";
    var isEditKoreksi = true;
    var linkEdit = '/penjamin-asuransi/informasi-pasien-ranap-bpjs/proses?id={$idEnc}&admisi=null&state={$stateEnc}';

    $(document).ready(function(){
        var _detail = " . json_encode($diagnosa['detail']) . ";
        var _disable = '{$disabled}';
        var _statusKunjungan = '{$status_kunjungan}'
        
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
    })
    " . $this->render('proses.js'), View::POS_END, 'js');

?>