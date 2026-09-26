<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

?>

<div class="panel panel-white">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe','Formulir kunjungan')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <?php
        $form = ActiveForm::begin([
            'id' => 'igd-form',
            // 'action' => '/pendaftaran/transaksi-pemesanan/save-cache',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);
        ?>
        <?= $form->field($modelIgd, 'tgl_pendaftaran', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
                'addon' => ['append' => [
                        'content' => '<i class="fa fa-calendar"></i>'
                    ]
                ]
                ])->textInput([
                    'placeholder' => $modelIgd->getAttributeLabel('tgl_pendaftaran'),
                    'class' => 'form-control input-sm pickadate',
                    'id' => 'tgl-pendaftaran',
                    'autocomplete' => "off",
                    'readonly' => true
                ]);
            ?>
        <?=
            $form->field($modelIgd, 'ruangan_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->dropDownList($ruangan, [
                'class' => 'select2 selectRuangan',
                'id'=>'ruangan_id',
                'prompt' => '-'
            ]);
        ?>
        <?=
        $form->field($modelIgd, 'jenis_kasus_penyakit_id', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ]
            ])->widget(DepDrop::classname(), [
                'name' => 'jenis_kasus_penyakit_id',
                'options' => [
                    'disabled' => false,
                    'class' => 'form-control select2',
                ],
                'pluginOptions' => [
                    'depends' => ['ruangan_id'],
                    'placeholder' => Yii::t('fe', '-- Pilih --'),
                    'url' => Url::to(['daftar/get-jenis-kasus-penyakit'])
                ],
                'pluginEvents'=>[
                    "depdrop:afterChange"=>"function(event, id, value) {
                        $('#kunjunganform-jeniskasuspenyakit_id').focus();
                    }",
                ]
            ]);
        ?>
        <?=
        $form->field($modelIgd, 'kelaspelayanan_id', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ]
            ])->widget(DepDrop::classname(), [
                'name' => 'jenis_kasus_penyakit_id',
                'options' => [
                    'disabled' => false,
                    'class' => 'form-control select2 selectKp',
                ],
                'pluginOptions' => [
                    'depends' => ['ruangan_id'],
                    'placeholder' => Yii::t('fe', '-- Pilih --'),
                    'url' => Url::to(['daftar/get-kelas-pelayanan'])
                ]
            ]);
        ?>
        <?=
        $form->field($modelIgd, 'dokter_id', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ]
            ])->widget(DepDrop::classname(), [
                'name' => 'dokter_id',
                'options' => [
                    'disabled' => false,
                    'class' => 'form-control select2',
                ],
                'pluginOptions' => [
                    'depends' => ['ruangan_id'],
                    'placeholder' => Yii::t('fe', '-- Pilih --'),
                    'url' => Url::to(['daftar/get-dokter'])
                ]
            ]);
        ?>
        <?=
            $form->field($modelIgd, 'carabayar_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->dropDownList($carabayar, [
                'class' => 'select2 selectCarabayar',
                'id'=>'selectCarabayar',
                'prompt' => '-'
            ]);
        ?>
        <?=
        $form->field($modelIgd, 'penjamin_id', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ]
            ])->widget(DepDrop::classname(), [
                'name' => 'penjamin_id',
                'options' => [
                    'disabled' => false,
                    'class' => 'form-control select2',
                    'id' => 'ruangan_select',
                ],
                'pluginOptions' => [
                    'depends' => ['selectCarabayar'],
                    'placeholder' => Yii::t('fe', '-- Pilih --'),
                    'url' => Url::to(['/daftar/get-penjamin'])
                ]
            ]);
        ?>
        <?=
            $form->field($modelIgd, 'keadaan_masuk', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->dropDownList(ArrayHelper::map($lib['keadaan_masuk'], 'lookup_id', 'lookup_value'), [
                'class' => 'select2',
                'prompt' => '-'
            ]);
        ?>
        <?=
            $form->field($modelIgd, 'transportasi', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->dropDownList(ArrayHelper::map($lib['transportasi'], 'lookup_id', 'lookup_value'), [
                'class' => 'select2',
                'prompt' => '-'
            ]);
        ?>
        <?=
            $form->field($modelIgd, 'keterangan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textArea([], [
                'rows' => '6',
            ]);
        ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>