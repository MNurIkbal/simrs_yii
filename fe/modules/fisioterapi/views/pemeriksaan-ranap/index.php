<?php

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

$listPegawai = ArrayHelper::getValue($dataView, 'listPegawai');
$title = ArrayHelper::getValue($dataView, 'title');
$currentUser = ArrayHelper::getValue($dataView, 'currentUser');
$pendaftaranId = ArrayHelper::getValue($dataView, 'pendaftaranId');
$pendaftaranIdEnc = ArrayHelper::getValue($dataView, 'pendaftaranIdEnc');
$programTerapiIds = ArrayHelper::getValue($dataView, 'programTerapiIds');
$programTerapiIdsEnc = ArrayHelper::getValue($dataView, 'programTerapiIdsEnc');
$isReadOnly = ArrayHelper::getValue($dataView, 'isReadOnly');
$noRekamMedik = ArrayHelper::getValue($dataView, 'dataPasien.no_rekam_medik');
$noRekamMedikEnc = DocoHelpers::encrypt($noRekamMedik);
$instalasiId = ArrayHelper::getValue($dataView, 'instalasiId');
$instalasiIdEnc = DocoHelpers::encrypt($instalasiId);
$pasienIdEnc = ArrayHelper::getValue($dataView, 'pasienIdEnc');

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Fisioterapi'), 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = $title;
$defaultUrlAction = "/fisioterapi/pemeriksaan-ranap/store-soap?pendaftaran_id=$pendaftaranIdEnc&program_terapi_ids=$programTerapiIdsEnc";

?>
<style>
   #tb-cppt_wrapper {
        height: 460px !important;
        overflow-y: auto;
    }
    #modal_backdrop {
        z-index: 1051 !important;
        max-height: calc(100vh);
        overflow-y: auto;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => '/fisioterapi/informasi-pasien-fisioterapi'
                        ]
                    ],
                    'riwayat-pasien' => [
                        'type'  => 'button',
                        'title' => Yii::t('fe', 'Riwayat Pasien'),
                        'icon'  => 'fa fa-history',
                        'attributes' => [
                            'id'          => 'btn-riwayat-pasien',
                            'data-width'  => '90%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-riwayat-pasien',
                            'action'      => '/igd/riwayat-pasien/history-patient?norm=' . $noRekamMedik . '&instalasi=' . $instalasiIdEnc . '&modal=is_modal', true,
                        ]
                    ]
                ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9">
                                <?php echo $this->render("partials/card/informasi-pasien.php", compact('dataView')); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo $this->render("partials/card/informasi-terapi.php", compact('dataView')); ?>
                            </div>
                        </div>
                        <div class="row body">
                            <div class="col-md-12">
                                <div id="div-cppt">
                                    <div class="row">
                                        <div class="tabbable" style="position: relative">
                                            <div class="nav-sticky-wrapper nav-sticky-cppt" id="nav-sticky">
                                                <div class="nav nav-tabs nav-tab-cppt nav-tab-periksa" id="nav-tab" role="tablist">
                                                    <a id="tab-cppt" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-cppt" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">CPPT</a>
                                                    <a id="tab-upload-dokumen" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-upload-dokumen" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Upload Dokumen</a>
                                                    <a id="tab-resume-medis" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-resume-medis" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Resume Medis</a>
                                                    <a id="tab-uji-fungsi-fisioterapi" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-uji-fungsi-fisioterapi" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Uji Fungsi</a>
                                                    <a id="tab-rehabilitasi" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-rehabilitasi" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Program Rehabilitasi</a>

                                                </div>
                                            </div>
                                            <div class="tab-content">
                                                <div class="tab-pane" id="view-cppt">
                                                    <div id="content-cppt">  </div>
                                                </div>
                                                <div class="tab-pane" id="view-upload-dokumen">
                                                    <div id="content-upload-dokumen" class="col-lg-12"></div>
                                                </div>
                                                <div class="tab-pane" id="view-resume-medis">
                                                    <div id="content-resume-medis" class="col-lg-12"></div>
                                                </div>
                                                <div class="tab-pane" id="view-uji-fungsi-fisioterapi">
                                                    <div id="content-uji-fungsi-fisioterapi" class="col-lg-12"></div>
                                                </div>
                                                <div class="tab-pane" id="view-rehabilitasi">
                                                    <div id="content-rehabilitasi" class="col-lg-12"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modalProgramTerapi" class="modal" style="overflow-y: auto !important;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="margin-top: -1%; margin-left: -10%; width: 120% !important;"></div>
    </div>
</div>
<div id="modal-preview" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-header bg-inverse" style="z-index: 1050">
            <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Preview</h5>
        </div>
        <div class="modal-content">
            <div class="preview-wrapper" style="position: relative;" id="preview-wrapper">
                <div class="overlay-preview"></div>
                <iframe frameborder="0" id="preview-content" style="width: 100%; height: 85vh;"></iframe>
            </div>
        </div>
    </div>
</div>
<div id="modal-order-pemeriksaan" style="z-index: 2041 !important; overflow-y: auto !important;" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>
<div id="modal-riwayat-pasien" style="z-index: 2041 !important; overflow-y: auto !important;" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>

<?php
$phpVars = [
    'listPegawai' => $listPegawai,
    'defaultUrlAction' => $defaultUrlAction,
    'isReadOnly' => $isReadOnly,
    'currentUser' => $currentUser,
    'pendaftaranId' => $pendaftaranIdEnc,
    'programTerapiId' => $programTerapiIdsEnc,
    'pasienId' => $pasienIdEnc,
];
$this->registerJsVar('phpVars', $phpVars);
$this->registerJs($this->render("cppt.js"), View::POS_END, 'js');
?>
