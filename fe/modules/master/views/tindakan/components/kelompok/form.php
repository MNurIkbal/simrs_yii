<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'simpan' => [
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-save',
                            'attributes' => [
                                'class' => 'spa',
                                'action' => $action,
                                'data-options' => 'click',
                                'form-id' => 'kelompok-form',
                                'data-render' => 'kelompok',
                                'data-tab' => 'tab-kelompok',
                                'data-target' => '#view-kelompok',
                                'id' => 'btn-save'
                            ]
                        ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'kelompok',
                                'data-tab' => 'tab-kelompok',
                                'data-target' => '#view-kelompok',
                            ]
                        ],
                    ],'#table-kelompok');
                ?>
            </div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'kelompok-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                        ]
                    ]);
                    ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label text-left control-label col-sm-3">
                                <h3><strong><?= $title ?></strong></h3>
                            </label>
                            <div class="col-md-5"></div>
                        </div>
                        <?=
                            $form->field($model, 'kelompoktindakan_kode', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Kode Kelompok'))
                        ?>
                        <?=
                            $form->field($model, 'kelompoktindakan_nama', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Nama Kelompok'))
                        ?>
                        <?=
                            $form->field($model, 'kelompoktindakan_namalainnya', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Nama Lainnya'))
                        ?>
                        <?=
                            $form->field($model, 'kelompoktindakan_persencyto', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                'class' => 'form-control', 
                                'placeholder' => 'Format Penulisan Contoh: 10 atau 10.5'
                            ])
                            ->label(Yii::t('fe', 'Cyto (%)'))
                        ?>
                        <?=
                            $form->field($model, 'kelompoktindakan_persendiskon', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                'class' => 'form-control',
                                'placeholder' => 'Format Penulisan Contoh: 10 atau 10.5'
                            ])
                            ->label(Yii::t('fe', 'Diskon'))
                        ?>
                        <?=
                            $form->field($model, 'catatan', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textArea()
                        ?>
                        <?=
                            $form->field($model, 'is_active', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->checkbox(['label' => 'Aktif'])->label(Yii::t('fe', 'Status'))
                        ?>
                        
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs($this->render('js/kelompok.js'), View::POS_END);
?>
