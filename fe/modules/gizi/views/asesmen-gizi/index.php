<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Gizi'), 'url' => ['/gizi/inf-pasien-ranap']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['inf-pasien-ranap']];
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
                <?=
                DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => '/gizi'
                        ]
                    ]
                    ]) ?>

            </div>
            <div class="panel-body">
                <!-- identitas pasien start -->
                <?=Yii::$app->controller->renderPartial('_pasien_identitas', [
                    'data_pasien' => $data_pasien
                ]);?>
                <!-- identitas pasien end -->

                <!-- riwayat personal start -->
                <?=Yii::$app->controller->renderPartial('riwayat_personal', [
                    'data_pasien' => $data_pasien,
                    'r_penyakitkeluarga' => $r_penyakitkeluarga,
                    'merokok' => $merokok,
                    'diagnosa_nama' => $diagnosa_nama,
                ]);?>
                <!-- riwayat personal end -->

                <div class="row">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>

                <!-- riwayat pasien start -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5 ><?=Yii::t('fe', 'Riwayat pasien')?></h5>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?= Html::a('Riwayat Pasien', 
                        null,
                         ['class'=>'btn btn-info', 'target' => '_blank','href'=>Url::to('/igd/riwayat-pasien/index?norm='.$data_pasien['no_rekam_medik'],true)]) ?>
                    </div>
                </div>
                <!-- riwayat pasien end -->

                <div class="row">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- pemeriksaan pasien start -->
                        <?=Yii::$app->controller->renderPartial('_pasien_asesmen', [
                            'data_pasien' => $data_pasien,
                            'style_button' => $style_button,
                            'style_button_pulang' => $style_button_pulang,
                        ]);?>
                        <!-- pemeriksaan pasien end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    $this->registerJs('
        var pendaftaran_id = "'. $id .'";
        var permintaan_makan = "'. $permintaan_makan .'";
    ', View::POS_END);
    $this->registerJs($this->render('js/index.js'), View::POS_END);
?>