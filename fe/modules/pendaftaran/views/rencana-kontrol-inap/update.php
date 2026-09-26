<?php
/**
 * @Author: Fajar
 * @Date:   2022-01-18
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
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rencana Kontrol/Rencana Inap'), 'url' => ['/pendaftaran/rencana-kontrol-inap']];
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
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row" id="form-informasi_pasien">
                    <div class="col-3">
                        <div id="form-infopasien" class="col-md-3">
                            <?php echo Yii::$app->controller->renderPartial('partial/_informasi-pasien'); ?>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="col-md-9" id="form-rencana_kontrol">
                        <?php echo Yii::$app->controller->renderPartial('partial/_form-update', [
                            'model' => $model,
                        ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $_peserta = json_encode($peserta);
    $_rencanaKontrol = json_encode($rencanaKontrol);
    $_pasien = json_encode($pasien);

$this->registerJs("
    var peserta = $_peserta;
    var rencanaKontrol = $_rencanaKontrol;
    var pasien = $_pasien;
    if (rencanaKontrol.jnsKontrol == '1') {
        var noKartu = $('#no_kartu').val();
    } else {
        var noKartu = $('#no_sep').val();
    }
", View::POS_END, 'index');
$this->registerJs($this->render('partial/js/update.js'), View::POS_END);
?>