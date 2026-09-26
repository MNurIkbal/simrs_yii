<?php

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
                    <h3 class="panel-title"><b>Laporan</b></h3>
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
                    <?= Html::button('<b><i class="fa fa-eye"></i></b>'.Yii::t('fe', ' Tampilkan'), 
                        [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'tampilkan-laporan'
                        ]);
                    ?>
                    <?= Html::button('<b><i class="fa fa-arrows-alt"></i></b>'.Yii::t('fe', ' Halaman Penuh'), 
                        [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'full-page'
                        ]);
                    ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-md-12 filter-form">
                    <form class="advancedFilter" onsubmit="return false;">
                        <div class="row" id="ffBody"></div>
                        <div class="row" id="ffFoot">
                        </div>
                        <div class="row" id="ffBody">
                            <div class="form-group col-md-3">
                                <label>Laporan :</label>
                                    <div class="form-group">
                                    <?= 
                                      Html::dropDownList('dokumen', '', 
                                            ArrayHelper::map($dokumen, 'code', 'title'), 
                                            [
                                                'id' => 'dokumen', 
                                                'class' => 'form-control select2', 
                                                'prompt' => \Yii::t('fe', '-- Pilih --'),
                                            ]
                                        )
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


    $("#tampilkan-laporan").on("click", function(e) {
        e.preventDefault();
        if (!$("#dokumen").val()) {
            docoNotification("error","Tampilkan Laporan Gagal !","Laporan harus dipilih");
            return false;
        }
        if ($("#dokumen").val() == 392) {

            var _data = {
                dokumen : $("#dokumen").val()
            }
        } else {

            var _data = {
                dokumen : $("#dokumen").val()
            }
        }
        $("#content-laporan").docoLoad({
            url : "/dcms/laporan/get-content-laporan",
            data : _data,
            success : function (data) {

            }
        });
    });

    $("#full-page").on("click", function(e) {
        e.preventDefault();
        if (!$("#dokumen").val()) {
            docoNotification("error","Gagal !","Laporan harus dipilih");
            return false;
        }
        window.open("/dcms/laporan/full?dokumen="+$("#dokumen").val());
    });
', View::POS_END, 'b-index');
?>
