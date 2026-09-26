<?php

/**
 * @author Randy Vianda Putra
 * @copyright 11 April 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
// use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;
use kartik\typeahead\Typeahead;
use kartik\widgets\FileInput;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master Display Antrian' , 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
// $model->is_active=1;
?>


<?php  
// $model->is_active=true;
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>


<style lang="">
    .list-ruangan {
        height: 30%;
        overflow-y: scroll;
    }

    .img-display {
        width:200px;
        height:200px;
    }

</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><?= $this->title ?></h3>
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]); ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div> 
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-save']) ?>
                <?= Html::button('<b><i class="fa fa-repeat"></i></b>' . \Yii::t('fe', 'Ulang'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-ulang']) ?>
                <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>' . \Yii::t('fe', 'Kembali'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-kembali']) ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'antrian-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_INLINE,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                            'enctype'=>'multipart/form-data'
                        ]
                    ]);
                    ?>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <?=
                                $form->field($model, 'jenisantrian_id', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList($data_antrian, [
                                    'class' => 'select2 jenis_antrian',
                                    'id' => 'instalasi_select',
                                    'prompt' => Yii::t('fe', '-- Pilih --')
                                ]);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'layarantrian_nama', [
                                'horizontalCssClasses' => [           'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel('layarantrian_nama'),
                                    'class' => 'form-control input-sm typeahead',
                                    'autocomplete' => "off",
                                    'id' => 'pemesanan-obat-qty',
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="is_loket">
                        <div class="col-md-12">
                            <div class="col-md-12" id="loket-col">
                                <?= $form->field($model, 'loket_id', ['horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-2',
                                        'wrapper' => 'col-md-10',
                                ]])
                                    ->dropDownList([], [
                                        'class' => 'form-control input-sm list-loket',
                                        'multiple' => 'multiple',
                                        'id' => 'dualistbox-loket',
                                    ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="is_poli">
                        <div class="col-md-12">
                            <div class="col-md-12" id="ruangan-col">
                                <?= $form->field($model, 'ruangan_id', ['horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-2',
                                        'wrapper' => 'col-md-10',
                                ]])
                                    ->dropDownList($ruangan, [
                                        'class' => 'form-control input-sm list-ruangan',
                                        'multiple' => 'multiple',
                                        'id' => 'dualistbox-ruangan',
                                    ]);
                                ?>
                            </div>
                            <div class="col-md-6 is_pegawai">
                                <?= $form->field($model, 'pegawai_id', ['horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-2',
                                        'wrapper' => 'col-md-10',
                                ]])
                                    ->dropDownList([], [
                                        'class' => 'form-control input-sm list-pegawai',
                                        'multiple' => 'multiple',
                                        'id' => 'dualistbox-pegawai',
                                    ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label text-left control-label col-sm-4" for="field-displayantrianform-is_active"><?= $model->getAttributeLabel('Status') ?></label>
                                <div class="col-md-8">
                                    <label>
                                    <?=
                                        $form->field($model, 'is_active', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8',
                                            ],
                                        ])->checkbox([
                                            'class' => 'styled checked-tablel',
                                            'label' => 'Aktif'
                                        ])
                                    ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $list_id = !empty($list_id) ? json_encode($list_id) : '{}';
    $list_pegawai = !empty($list_pegawai) ? json_encode($list_pegawai) : '{}';
    $selected_id_loket = !empty($selected_id_loket) ? json_encode($selected_id_loket) : '{}';
    $this->registerJs('
        var dataRuangan = '. $list_id . ';
        var dataPegawai = '. $list_pegawai . ';
        var dataLoket = '. $selected_id_loket . ';
        var isBanyakLoket = '.$isBanyakLoket.';
    ');
    $this->registerJs($this->render('../assets/js/display-antrian.js'));
?>
