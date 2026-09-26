<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-08 17:09:50
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 10:42:09
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Bedah sentral', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi pasien operasi', 'url' => ['informasi-pasien-operasi']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .datepicker>div{
        display:none;
    }
    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }
    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }
    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        width: 100px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }
    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 100px;
        border: solid 0.2px;
    }
    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }
    .my-legend a {
        color: #777;
    }
</style>
<?=Html::hiddenInput('iddokterbedah', DocoConstants::TIM_DOKTER_BEDAH, ['id' => 'idDokterBedah'])?>
<?=Html::hiddenInput('pendaftaran_id', !empty($data['pendaftaran_id']) ? $data['pendaftaran_id'] : null, ['id' => 'pendaftaran_id'])?>
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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                                <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                <div <?php echo $display ?> >
                    <?= DocoHelpers::generateToolbar([
                        // 'search',
                        'back',
                        'riwayat-pasien' => [
                            'type'  => 'button',
                            'title' => Yii::t('fe', 'Riwayat Pasien'),
                            'icon'  => 'fa fa-history',
                            'attributes' => [
                                'id'          => 'btn-riwayat-pasien',
                                'data-width'  => '90%',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action'      => '/igd/riwayat-pasien/history-patient?norm='.$data['no_rekam_medik'].'&instalasi='.$data['instalasiasal_id'].'&modal=is_modal',true,
                            ]
                        ],
                    ]) ?>
                </div>
            </div>
            <div class="panel-body">
               <!-- pannel detail pasien -->
               <div class="col-md-12">
                    <?=$this->render('detail-partial/_infopasien', ['data'=>$data, 'carabayar_kode_warna' => $carabayar_kode_warna, 'data_rencanaOperasi'=>$data_rencanaOperasi,])?>
               </div>
               <!-- pannel detail pasien -->

               <div class="col-md-12">
                    <?=$this->render('detail-partial/_tab', [
                        'data'=> $data,
                        'posisi'=> $posisi,
                        'status'=> $status,
                        'id' => $penunjangId,
                        'activeTab' => $activeTab,
                        'lastOperasi' => $lastOperasi,
                        'roleBatalBtn' => $roleBatalBtn,
                    ])?>
               </div>
            </div>
        </div>
    </div>
</div>


