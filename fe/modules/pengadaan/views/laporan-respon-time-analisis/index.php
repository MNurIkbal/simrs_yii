<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
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
                      <h3 class="panel-title"><b><?= $title; ?></b></h3>
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
                <?php
                    $btn_toolbar = [
                        'custom-reset' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Muat Ulang'),
                            'icon' => 'fa fa-refresh',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-refresh'
                            ],
                        ]
                    ];
                ?>
                <?=DocoHelpers::generateToolbar($btn_toolbar);?>
                <?= Html::button('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'),
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-excel'
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="col-md-1">
                    <p style="margin-top: 10px">Periode: </p>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" id="rangeDemoStart" value="<?= date('d-M-Y') ?>" class="form-control startDate" />
                        <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
                        <input type="text" id="rangeDemoFinish" value="<?= date('d-M-Y') ?>" class="form-control endDate" readonly />
                        <input type="text" style="display:none" class="targetDate" col-index=2 readonly="true">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).ready(function() {
        dateRangeHelper(".startDate",".endDate",".targetDate");

        $("#btn-refresh").on("click", function() {
            $("#rangeDemoStart").val("'.date("d-M-Y").'");
            $("#rangeDemoFinish").val("'.date("d-M-Y").'");
        });

        $("#btn-excel").on("click", function(e) {
            e.preventDefault();
            var start_date = $("#rangeDemoStart").val();
            var end_date = $("#rangeDemoFinish").val();

            window.open("'.$unduh_excel.'&start_date="+start_date+"&end_date="+end_date, "_blank");
        });
    });

', View::POS_END, 'b-index');
?>
