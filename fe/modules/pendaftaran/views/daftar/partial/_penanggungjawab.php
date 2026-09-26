<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-12 14:52:01
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-05-11 17:23:29
 */

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
        <h5 class="panel-title"><?=Yii::t('fe','Penanggung jawab pasien')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">                        
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <?php
        $form = ActiveForm::begin([
            'id' => 'penanggungjawab-form',
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
        <?=
            $form->field($modelPenanggungjawab, 'pengantar', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->dropDownList(ArrayHelper::map($lib['pengantar'], 'lookup_id', 'lookup_value'), [
                'class' => 'select2',
                'prompt' => '-'
            ]);
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'pj_nama', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textInput();
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'jk', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->radioList(
                        ArrayHelper::map($lib['jenis_kelamin'], 'lookup_id', 'lookup_value'), 
                        ['inline'=>true]
                    );;
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'jenis_identitas', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->dropDownList(ArrayHelper::map($lib['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                'class' => 'select2',
                'prompt' => '-'
            ]);
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'no_identitas', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textInput();
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'hubungan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->dropDownList(ArrayHelper::map($lib['hubungan_keluarga'], 'lookup_id', 'lookup_value'), [
                'class' => 'select2',
                'prompt' => '-'
            ]);
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'tempat_lahir', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]                
            ])->textInput();
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'tgl_lahir', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
                'addon' => [
                    'append' => [
                        ['content' => '<i class="fa fa-calendar "></i>'],
                    ],
                ]
            ])->textInput(['class'=>'pickadate-w-month dateusia']);
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'umur', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textInput(['class'=>'umurtext', 'readonly'=>true]);
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'alamat', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textArea();
        ?>
        <?=
            $form->field($modelPenanggungjawab, 'no_tlp', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
            ])->textInput();
        ?>
        <?php ActiveForm::end(); ?> 
    </div>
</div>
