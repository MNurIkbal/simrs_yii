<?php
/**
 * @Author: Ikhwanu Arriyadh T
 * @Date:   2022-02-15
 */

use app\components\DocoHelpers;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoConstants;


$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Update tanggal pulang'), 'url' => ['/pendaftaran/update-tanggal-pulang']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading mb-20">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                    </div>
                </div>
                <!-- <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div> -->
            </div>
            <div class="panel-body">
                <div class="row" id="form-informasi_pasien">
                    <div class="col-3">
                        <div id="form-infopasien" class="col-md-3">
                            <?php echo Yii::$app->controller->renderPartial('partial/_informasi-pasien'); ?>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="col-md-9" id="form-update_tanggal_pulang">
                        <?php echo Yii::$app->controller->renderPartial('partial/_form-update-tanggal-pulang', [
                            'model' => $model,
                            'result' => $result,
                            'status_pulang' => $status_pulang,
                        ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $data = json_encode($result);
    $model = json_encode($model);
    $this->registerJs("
    var data = $data;
    var model = $model;
", View::POS_END, 'index');
$this->registerJs($this->render('partial/js/_form-update-tanggal-pulang.js'), View::POS_END);
?>