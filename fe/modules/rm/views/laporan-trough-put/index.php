<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

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
                    <?php
                        // Html::button('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'), 
                        // [
                        //     'class' => 'btn btn-info btn-labeled btn-xs',
                        //     'id' => 'export-excel',
                            
                        // ]);
                    ?>
                    <?= DocoHelpers::generateToolbar([
                        'export-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Unduh Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'export-excel',
                                'data-options' => 'excel-serconn',
                                'data-target'=> '#modal_backdrop',
                                'data-url' => Url::home() . 'rm/laporan-trough-put/show-popup-excel?',
                                'data-width' => '75%',
                                'data-custom' => 'true'
                            ]
                        ],
                    ]);?>
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
                        </div>
                    </form>
                    <div class="clearfix"></div>
                    <hr>
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

        if(!$("#bulan").val()) {
            docoNotification("error","Unduh Excel Gagal !","Bulan harus diisi.");
            return false;
        } 
        var _data = {
            bulan : $("#bulan").val(),
            tahun : $("#tahun").val(),
        }
        var _param = $.param(_data);
        //window.open("/rm/laporan-trough-put/export-excel?" + _param, "_blank"); 
        $("#export-excel").attr("action", "/rm/laporan-trough-put/show-popup-excel?" + _param);
    });
', View::POS_END, 'b-index');
?>

