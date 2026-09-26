<?php
use yii\web\View;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$title = $dataView['title'];
$listPegawai = $dataView['listPegawai'];
$currentUser = ArrayHelper::getValue($dataView, 'currentUser');

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Fisioterapi'), 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = $title;

$isReadOnly = ArrayHelper::getValue($dataView, 'isReadOnly');
$noRekamMedik = ArrayHelper::getValue($dataView, 'dataPasien.no_rekam_medik');
$instalasiIdRj = ArrayHelper::getValue($dataView, 'instalasiIdRj');
$defaultUrlAction = "/fisioterapi/pemeriksaan/store-soap?pendaftaran_id=$pendaftaranId&program_terapi_id=$programTerapiIds&program_terapi_detail_id=$programTerapiDetailIds";
$pasienIdEnc = ArrayHelper::getValue($dataView, 'pasienIdEnc');
?>
<style>
    #tb-cppt_wrapper {
        height: 460px !important;
        overflow-y: auto;
    }
    #modal_backdrop {
        z-index: 1041 !important;
        max-height: calc(100vh);
        overflow-y: auto;
    }
    .h-115 {
        height: 115px !important;
    }
    .nav-tab-cppt .nav-link {
        max-width: 250px !important;
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
                <?= DocoHelpers::generateToolbar([
                        'back' => [
                            'attributes' => [
                                'href' => '/fisioterapi/informasi-pasien-fisioterapi'
                            ]
                        ],
                        'riwayat-pasien' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Riwayat Pasien'),
                            'icon' => 'fa fa-history',
                            'attributes' => [
                                'id' => 'btn-riwayat-pasien',
                                'data-width' => '90%',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/igd/riwayat-pasien/history-patient?norm='.$noRekamMedik.'&instalasi='.$instalasiIdRj.'&modal=is_modal=true',
                            ]
                        ]
                    ]) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9">
                                <?= $this->render("partials/informasi-pasien.php", compact('dataView')) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $this->render("partials/informasi-terapi.php", compact('dataView')) ?>
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
                                                    <a id="tab-formulir-rajal" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-formulir-rajal" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Formulir Rawat Jalan</a>
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
                                                <div class="tab-pane" id="view-formulir-rajal">
                                                    <div id="content-formulir-rajal" class="col-lg-12"></div>
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
<?php
    $phpVars = [
        'listPegawai' => $listPegawai,
        'defaultUrlAction' => $defaultUrlAction,
        'isReadOnly' => $isReadOnly,
        'currentUser' => $currentUser,
        'pendaftaranId' => $pendaftaranId,
        'programTerapiId' => $programTerapiIds,
        'programTerapiDetailId' => $programTerapiDetailIds,
        'pasienId' => $pasienIdEnc,
    ];
    $this->registerJsVar('phpVars', $phpVars);
    $this->registerJs($this->render("cppt.js"), View::POS_END, 'js');
?>
