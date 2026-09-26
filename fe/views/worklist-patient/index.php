<?php

use app\components\DocoConstants;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('fe', 'Informasi Pasien');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['/rajal/dashboard']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .dataTables_scrollBody {
        overflow: hidden;
        max-height: unset;
    }

    .dataTables_scroll {
        overflow-x: scroll !important;
        overflow-y: hidden !important;
        max-height: unset;
    }

    .DTFC_LeftBodyLiner table thead tr,
    #table-patient thead tr {
        visibility: hidden;
    }

    .dataTables_scroll .open .dropdown-menu {
        position: relative;
    }

    .tooltip-inner {
        white-space: nowrap;
        max-width: none;
        overflow: hidden;
        text-align: left!important;
    }

    .select2-selection__rendered {
        height: 35px;
        overflow-y: auto !important;
    }
</style>
<div class="row body">
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

            <div class="panel-body">
                <!--filter-->
                <div class="col-sm-12">
                    <nav>
                        <div class="nav nav-tabs nav-tab-worklist" id="nav-tab" role="tablist">
                            <?php if (in_array(ArrayHelper::getValue($config_tab_worklist, 'konfig_worklist_semua_pasien_hide_dokter', false), [false, 'false']) || (ArrayHelper::getValue($config_tab_worklist, 'konfig_worklist_semua_pasien_hide_dokter') == true && $is_dokter == false)) { ?>
                                <a class="nav-item nav-tab-type nav-link" data-instalasi ="all" data-type="all" id="nav-all-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Semua Pasien</a>
                            <?php } ?>
                            <a class="nav-item nav-tab-type nav-link" data-instalasi="<?= DocoConstants::INSTALASI_ID_RJ ?>" data-type="rj" id="nav-rajal-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Pasien Rawat Jalan</a>
                            <a class="nav-item nav-tab-type nav-link" data-instalasi="<?= DocoConstants::INSTALASI_MCU ?>" data-type="mcu" id="nav-mcu-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Pasien MCU</a>
                            <a class="nav-item nav-tab-type nav-link" data-instalasi="<?= DocoConstants::INSTALASI_ID_APPOINTMENT ?>" data-type="ol" id="nav-appointment-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Appointment</a>
                            <a class="nav-item nav-tab-type nav-link" data-instalasi="<?= DocoConstants::INSTALASI_ID_RD ?>" data-type="rd" id="nav-igd-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Pasien Emergency</a>
                            <a class="nav-item nav-tab-type nav-link" data-instalasi="<?= DocoConstants::INSTALASI_ID_RI ?>" data-type="ri" id="nav-ranap-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Pasien Rawat Inap</a>
                            <!-- <a class="nav-item nav-tab-type nav-link" data-instalasi="<?= DocoConstants::INSTALASI_ID_BEDAH ?>" data-type="ot" id="nav-bedah-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Procedure / OT</a> -->
                        </div>
                    </nav>
                    <?= Yii::$app->controller->renderPartial('//worklist-patient/partials/__table', [
                        'carabayar' => isset($dropdown['carabayar']) ? $dropdown['carabayar'] : []
                    ]); ?>
                    <!-- <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab">
                        </div>
                        <div class="tab-pane fade" id="nav-rajal" role="tabpanel" aria-labelledby="nav-rajal-tab">
                        </div>
                        <div class="tab-pane fade" id="nav-mcu" role="tabpanel" aria-labelledby="nav-mcu-tab">
                        </div>
                        <div class="tab-pane fade" id="nav-igd" role="tabpanel" aria-labelledby="nav-igd-tab">
                        </div>
                        <div class="tab-pane fade" id="nav-ranap" role="tabpanel" aria-labelledby="nav-ranap-tab">
                        </div>
                        <div class="tab-pane fade" id="nav-appointment" role="tabpanel" aria-labelledby="nav-appointment-tab">
                        </div>
                        <div class="tab-pane fade" id="nav-bedah" role="tabpanel" aria-labelledby="nav-bedah-tab">
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal-general" class="modal fade" style="z-index:1065;" data-backdrop="static" data-keyboard=false>
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>
<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<!-- End -->
<?php
$this->registerJs('
    const type = "' . $type . '";
    const dropdownData = ' . json_encode($dropdown) . '
    var defaultSorting = '.json_encode($defaultSorting).';
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/__index.js'), View::POS_END);
?>
