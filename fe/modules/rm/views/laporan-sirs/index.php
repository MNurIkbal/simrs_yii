<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $this->context->_title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medik', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                <div class="btn-group pull-left">
                    <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', ' Cari'), 
                        [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'cari-laporan'
                        ]);
                    ?>
                    <?= Html::button('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'), 
                        [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'export-excel'
                        ]);
                    ?>
                    <?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' Ulang'), 
                        [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'reset-laporan'
                        ]);
                    ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-md-12 filter-form">
                    <form class="advancedFilter" onsubmit="return false;">
                        <div class="row" id="ffBody"></div>
                        <div class="row" id="ffFoot">
                            <div class="col-md-12" style="display: none">
                                <center>
                                    <button type="button" class="btn btn-sm btn-primary btn-xs advancedFilterDo">
                                        <i class="fa fa-search"></i> Cari
                                    </button>&nbsp;
                                    <button type="reset" class="btn btn-sm btn-aqua btn-xs -advancedFilterHide">
                                        <i class="fa fa-repeat"></i> Ulang
                                    </button>
                                </center>
                            </div>
                        </div>
                        <div class="row" id="ffBody">
                            <div class="form-group col-md-2">
                                <label>Bulan :</label>
                                <div class="form-group">
                                    <?php
                                        $bulan = DocoHelpers::daftarBulan();
                                    ?>
                                    <?= 
                                      Html::dropDownList('bulan', null, $bulan, 
                                            [
                                                'id' => 'bulan', 
                                                'class' => 'form-control select2', 
                                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                                            ]
                                        )
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Tahun :</label>
                                <div class="form-group">
                                    <?php
                                        $listTahun = [];
                                        for ($x = date('Y'); $x >= (date('Y') - 9) ; $x--) {
                                            $listTahun[$x] = $x;
                                        }
                                    ?>
                                    <?= 
                                      Html::dropDownList('tahun', '', $listTahun, 
                                            [
                                                'id' => 'tahun', 
                                                'class' => 'form-control select2'
                                            ]
                                        )
                                    ?>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jenis laporan :</label>
                                    <div class="form-group">
                                    <?= 
                                      Html::dropDownList('carabayar_nama', '', 
                                            ArrayHelper::map($jenisLaporan, 'lookup_id', 'lookup_name'), 
                                            [
                                                'id' => 'jenis_laporan', 
                                                'class' => 'form-control select2', 
                                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                                            ]
                                        )
                                    ?>
                                    </div>
                            </div>
                            <div class="form-group col-md-3 filter-instalasi" style="display:none;">
                                <label>Instalasi :</label>
                                    <div class="form-group">
                                        <?php
                                            $instalasi[DocoConstants::INSTALASI_ID_RJ] = DocoConstants::TITLE_RJ;
                                            $instalasi[DocoConstants::INSTALASI_ID_RD] = DocoConstants::TITLE_RD;
                                        ?>
                                        <?= 
                                            Html::dropDownList('instalasi_id', '', $instalasi, [
                                                'id' => 'instalasi_id', 
                                                'class' => 'form-control select2', 
                                                'prompt' => \Yii::t('fe', '-- Semua --'),
                                            ])
                                        ?>
                                    </div>
                            </div>
                        </div>
                    </form>
                    <div class="clearfix"></div>
                    <hr>
                    <div id="content-laporan"></div>
                    <br>
                </div>

            </div>
        </div>
    </div>
</div>


<?php 
$this->registerJs('
    $(document).ready(function() {
        dateRangeHelper(".startDate",".endDate",".targetDate");
    });

    $("#export-excel").on("click", function (e) {
        e.preventDefault();
        if (!$("#jenis_laporan").val()) {
            docoNotification("error","Pencarian Gagal !","Jenis laporan harus diisi");
            return false;
        }
        var _data = {
            jenis_laporan : $("#jenis_laporan").val(),
            bulan : $("#bulan").val(),
            tahun : $("#tahun").val(),
        }
        var _param = $.param(_data);
        if ($("#jenis_laporan").val() == 391) {
            $(this).attr("action","/rm/laporan-sirs/show-popup-excel?"+_param);
            $(this).attr("data-target","#modal_backdrop");
            $(this).attr("data-toggle","modal");
        }else if ($("#jenis_laporan").val() == 392) {
            $(this).attr("action","/rm/laporan-sirs/show-popup-excel-morbiditas?"+_param);
            $(this).attr("data-target","#modal_backdrop");
            $(this).attr("data-toggle","modal");
        }else{
            window.open("/rm/laporan-sirs/export-excel?" + _param, "_blank");
        }  
    });

    $("#cari-laporan").on("click", function(e) {
        e.preventDefault();
        if (!$("#jenis_laporan").val()) {
            docoNotification("error","Pencarian Gagal !","Jenis laporan harus diisi");
            return false;
        }
        if ($("#jenis_laporan").val() == 392) {
            $(".filter-instalasi").show();

            var _data = {
                jenis_laporan : $("#jenis_laporan").val(),
                bulan : $("#bulan").val(),
                tahun : $("#tahun").val(),
                instalasi_id : $("#instalasi_id").val()
            }
        } else {
            $(".filter-instalasi").hide();

            var _data = {
                jenis_laporan : $("#jenis_laporan").val(),
                bulan : $("#bulan").val(),
                tahun : $("#tahun").val(),
            }
        }
        $("#content-laporan").docoLoad({
            url : "/rm/laporan-sirs/get-laporan",
            data : _data,
            success : function (data) {

            }
        });
    });

    $("#reset-laporan").on("click", function(e) {
        e.preventDefault();

        $("#jenis_laporan").val(null).trigger("change");
        $("#bulan").val(null).trigger("change");
        $("#tahun").val($("#tahun option:eq(0)").val()).trigger("change");

        var _data = {
            jenis_laporan : -1,
            bulan : "",
            tahun : "",
        }

        $("#content-laporan").docoLoad({
            url : "/rm/laporan-sirs/get-laporan",
            data : _data,
            success : function (data) {

            }
        });
    });
', View::POS_END, 'b-index');
?>
