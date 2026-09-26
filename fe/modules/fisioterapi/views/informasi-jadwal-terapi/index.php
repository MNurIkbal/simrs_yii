<?php
/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\widgets\filters\DropdownStatusProgramFisioterapi\DHSelectStatusProgramFisioterapi;
use app\widgets\DHMonthRangePickerWidget;
use yii\helpers\ArrayHelper;
use app\widgets\DHDatePickerWidget;
use kartik\select2\Select2;
use yii\helpers\Html;

$title = ArrayHelper::getValue($dataView, 'title');
$listPegawai = ArrayHelper::getValue($dataView, 'listPegawai');
$this->params['breadcrumbs'][] = ['label' => 'Fisioterapi', 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = $title;
?>
<style>
    .picker__button--clear{
        display: none !important;
    }.panel{
        overflow-x: unset !important;
    }.sidebar-timeline{
        font-size: 12px;
        padding: 5px;
        min-width: 150px;
    }.jqtl-ruler-line-item{
        background-color: #37474f !important;
        font-family: "Roboto", Helvetica Neue, Helvetica, Arial, sans-serif !important;
        font-size: 12px !important;
        border-right: 1px solid white;
    }.jqtl-side-index-margin{
        background-color: #37474f !important;
        color: white;
    }.jqtl-event-label{
        margin: auto;
    }.popover-title{
        display: none;
    }.jqtl-event-node::after{
        content: none;
    }#myTimeline{
        position: unset !important;
    }.popover {
        background-color: #333333 !important;
        border-radius: 8px !important;
    }.popover.bottom > .arrow:after{
        border-bottom-color: #333333 !important

    }.popover-jadwal-terapi{
        color: white;
        font-size: 12px;
    }.select2-container .select2-selection--single{
        height: 35px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-toolbar clearfix">
                        <?= DocoHelpers::generateToolbar([
                                'search',
                                'reset' => [
                                    'attributes' => [
                                        'data-parent' => '.filter-form',
                                    ]
                                ],
                            ], '#table-jadwal-terapi') ?>
                    </div>
                    <div class="row" style="margin: 20px">
                        <div class="col-md-3">
                            <label>Tanggal Terapi :</label>
                            <br />
                            <?= DHDatePickerWidget::widget([
                                'with_default' => true,
                                'default_value' => date('d-m-Y'),
                            ]); ?>
                        </div>
                        <div class="col-md-3">
                            <label>Dokter :</label>
                            <br />
                            <?= Html::dropDownList(
                                'terapis',
                                '',
                                $listPegawai,
                                [
                                    'class' => 'form-control select2 select-pegawai',
                                    'id' => 'terapis',
                                    'prompt' => Yii::t('fe', 'Semua')
                                ]
                            ) ?>
                        </div>
                    </div>
                    <div style="margin:20px" id="loading"></div>
                    <div id="timeline-wrapper" style="margin:20px; overflow-x: auto;">
                        <div id="myTimeline">
                            <ul class="timeline-events">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$dataFilter = [];
$this->registerJsVar('dataFilter', $dataFilter);
$this->registerJs($this->render("js/index.js"), View::POS_END, 'js');
?>