<?php
// Author : Ardi Pratama
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Tambah Instruksi') ?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'custom-back' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Kembali'),
                'icon' => 'fa fa-arrow-left',
                'attributes' => [
                    'id' => 'btn-back-terapi',
                    'data-options' => 'click',
                    'data-jns_instruksi' => $jns_instruksi,
                    'data-source_tab' => $sourceTab
                ],
            ]
        ]) ?>
    </div>
 
    <div class="panel-body">
        <div class="row">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <?php $form = ActiveForm::begin([
                                'id' => 'form-instruksi', 
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'enableClientValidation' => false,
                                'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL]
                            ]) ?>
                            <div class="form-group required">
                                <label class="control-label col-md-6 has-star"><?=Yii::t('fe', 'Jenis')?></label>
                                <div class="col-md-6">
                                    <?= Select2::widget([
                                        'model' => $modelInstruksi,
                                        'attribute' => 'jenis_instruksi',
                                        'data' => ArrayHelper::map($list_jenis_instruksi,'lookup_id','lookup_name'),
                                        'options' => ['placeholder' => 'Pilih'],
                                        'hideSearch' => true,
                                        'pluginOptions' => [
                                            // 'allowClear' => true
                                        ],
                                    ]); ?>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="control-label col-md-6 has-star"><?=Yii::t('fe', 'catatan_instruksi')?></label>
                                <div class="col-md-6">
                                    <?= Html::activeTextarea($modelInstruksi, 'catatan_instruksi',['class'=>'form-control']) ?>
                                </div>
                            </div>
                                <!-- Hidden inputs -->
                                <?= Html::activeHiddenInput($modelInstruksi, 'instruksi_id') ?>
                                <?= Html::activeHiddenInput($modelInstruksi, 'cppt_id') ?>
                                <?= Html::activeHiddenInput($modelInstruksi, 'tgl_instruksi') ?>

                            <?php ActiveForm::end() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="content-transaksi-terapi" class="row">
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var pasien_id_now = "'.$pasien_id_now.'";
    var ruangan_now = "'.$ruangan_now.'";
', View::POS_END);
$this->registerJs($this->render('js/transaksi_terapi.js'), View::POS_END);
?>