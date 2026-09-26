<?php


use yii\web\View;
use app\components\DocoHelpers;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\DatePicker;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Approval Produksi Obat', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style lang="">
    .datepicker>div{
        display:block;
    }
</style>

<div class="modal-header bg-inverse">
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="panel-body">
            <?php
                $form = ActiveForm::begin([
                  'id' => 'form-produksiObat',
                  'type' => ActiveForm::TYPE_HORIZONTAL,
                  'enableAjaxValidation' => false,
                  'enableClientValidation' => false,
                  'validateOnSubmit' => false,
                  'formConfig' => [
                      'labelSpan' => 4,
                      'deviceSize' => ActiveForm::SIZE_MEDIUM
                  ],
                  'options' => [
                      'class' => 'form-horizontal',
                      'role' => 'form',
                  ]
                ]);
            ?>    
            <?= $form->field($model, 'pegawai_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                    ])->dropDownList([], [
                                    'prompt' => Yii::t('fe', '-- Pilih --'),
                        'class' => 'select2',
                        'tab-index' => 2
                    ]);
            ?>
            <?= $form->field($model, 'tgl_produksi', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
                'addon' => ['append' => [
                'content' => '<i class="fa fa-calendar"></i>']]
                ])->textInput([
                        'placeholder' => $model->getAttributeLabel('tgl_produksi'),
                        'class' => 'form-control input-sm',
                        'readonly' => true,
                        'value' => date('d-M-Y H:i:s')
                ])->label(Yii::t("fe", "Tanggal Produksi"));
            ?>
            <?= $form->field($model, 'tgl_kadaluarsa', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DatePicker::classname(), [
                'name' => 'date_12',
                'value' => "",
                'readonly' => true,
                'language' => 'en',
                'options' => [
                    'tabindex' => 5,
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                    'startDate' => "0d"

                ]
            ])->label(Yii::t("fe", "Tanggal Kadaluarsa")); ?>
            <?= $form->field($model, 'batch_number', [
              'horizontalCssClasses' => [
                  'label' => 'text-left control-label col-sm-4',
                  'wrapper' => 'col-md-7'
              ]
            ])->textInput([
                'placeholder' => $model->getAttributeLabel('batch_number'),
                'class' => 'form-control input-sm typeahead',
                'autocomplete' => "off"
            ]); ?>
            <center><p style="color:red;"><b>* Data stok obat akan terupdate mengacu pada data produksi sesuai dengan tanggal produksi</b></p></center>
        <hr>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6">
                    <?= Html::submitButton(
                        '<b></b>' . Yii::t('fe','Ya'), 
                        [
                            'class' => 'btn btn-success btn-block btn-approve-produksi',
                            'id' => 'btn-simpan-alert'
                        ]) 
                    ?>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-block btn-danger btn-tidak" id='btn-tidak' data-dismiss="modal">Tidak</button>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
    $this->registerJs('
        var id = "' . $id_produksi . '";
    ', View::POS_END,'js-kuning');
    $this->registerJs($this->render('js/__modal_produksi.js'));
?>