<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Jenis Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;

use kartik\widgets\ActiveForm;


// Some variables
$this->title = Yii::t('fe', 'Approve Pasien Radiologi');
$this->params['breadcrumbs'][] = ['label' => 'Radiologi', 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi Pasien Rujukan Radiologi', 'url' => ['index']];
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
                <?= DocoHelpers::generateToolbar([
                    'back' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-kembali',
                            // 'data-options' => 'link',
                            'id' => 'btn-kembali',
                            'onClick' => null,
                            // 'data-content' => 'content-perda',
                            // 'data-target' => '/laboratorium/inf-pasien-rujukan-lab/form-batal?id=',
                        ]
                    ],
                    'save' => [
                        'title' => \Yii::t('fe', 'Approve'),
                        'icon' => 'fa fa-check-square-o',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-simpan',
                            // 'data-options' => 'link',
                            'id' => 'btn-simpan',
                            'onClick' => null,
                            // 'data-content' => 'content-perda',
                            // 'data-target' => '/laboratorium/inf-pasien-rujukan-lab/form-batal?id=',
                        ]
                    ],
                    'add-tindakan' => [
                        'title' => \Yii::t('fe', 'Tambah Tindakan'),
                        'icon' => 'fa fa-plus',
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '80%',
                            'action' => '/radiologi/inf-pasien-rujukan-rad/form-modal-tindakan?penjamin_id='.$penjaminId.'&kelaspelayanan_id='.$kelasPelayananId.'&pasienkirimkeunitlain_id='.$id,
                        ]
                    ],
                ], '#tb-inf-pasien-rujukan-lab') ?>
            </div>
            <div class="panel-body">
                <?= Yii::$app->controller->renderPartial('_info_pasien', ['detail' => $detail]) ?>
                <?= Yii::$app->controller->renderPartial('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
var pasienKirimKeUnitLainId = ' . $id . ';
var _dokterRujukId = "'.$dokterRujukId.'";
var _dokterRujukNama = "'.$dokterPerujuk.'";

', View::POS_END);
// Register js file
$this->registerJs($this->render('js/form_aproval.js'), View::POS_END);
?>