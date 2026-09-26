<?php

/**
 * @Author: Wahyu Saepuloh
 * @Date:   11 November 2019
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'simpan' => [
                        'title' => \Yii::t('fe', 'Update'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            // 'class' => 'spa',
                            'data-options' => 'click',
                            'action' => $action,
                            'form-id' => 'tindakan-bmhp-form',
                            'data-render' => 'tindakan-bmhp',
                            'data-tab' => 'tab-tindakan-bmhp',
                            'data-target' => '#view-tindakan-bmhp',
                            'id' => 'btn-update-tindakan-bmhp'
                        ]
                    ],
                    'kembali' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'spa btn-back-tindakan',
                            'data-options' => 'click',
                            'data-render' => 'tindakan-bmhp',
                            'data-tab' => 'tab-tindakan-bmhp',
                            'data-target' => '#view-tindakan-bmhp',
                        ]
                    ],
                ]) ?>
            </div>
            
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'tindakan-bmhp-form',
                        // 'action' => '/master/tindakan/set-list-item',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
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
                        <label class="control-label text-left control-label col-sm-12">
                            <h3><strong><?= $title ?></strong></h3>
                        </label>
                    </div>
                    <div class="form-group col-md-4">
                        <?= $form->field($model, 'daftartindakan_nama', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-4'
                            ]
                        ])->textInput([
                            'placeholder' => Yii::t('fe', 'Tindakan'),
                            'class' => 'form-control input-sm',
                            'readonly' => "true",
                            'id' => 'daftartindakan_nama',
                            'value' => $nama_tindakan,
                            'tabindex' => '1'
                        ])->label(Yii::t('fe', 'Tindakan')); ?>
                        <?= Html::activeHiddenInput($model, 'daftartindakan_id', ['id' => 'daftartindakan_id', 'value' => $id_tindakan]); ?>
                    </div>
                    <hr>
                </div>
                <div class="col-md-12">
                    <div class="panel-body">
                        <table id="table-tindakan-bmhp" class="table table-striped">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width=3%>No</th>
                                    <th width=25%><?=\Yii::t("fe", "Group");?></th>
                                    <th width=25%><?=\Yii::t("fe", "Obat Alkes");?></th>
                                    <th><?=\Yii::t("fe", "Satuan Unit");?></th>
                                    <th><?=\Yii::t("fe", "Qty");?></th>
                                    <th><?=\Yii::t("fe", "QTY Konversi");?></th>
                                    <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                                </tr>
                                <tr>
                                    <td>#</td>
                                    <td>
                                        <?= $form->field($model, 'group',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                        ])->dropDownList($group_jenisobat,[
                                            'class' => 'select2',
                                            'prompt' => \Yii::t('fe', '— Pilih Group —'),
                                            'id' => 'group',
                                            'tabindex' => '2'
                                        ])->label(false); ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, 'obatalkes_id',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                        ])->dropDownList([],[
                                            'class' => 'select2',
                                            'id' => 'obatalkes_id',
                                            'tabindex' => '2'
                                        ])->label(false); ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, 'satuanunit_id', [
                                            'labelOptions' => ['class' => 'text-right']
                                        ])->widget(DepDrop::classname(), [
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'options' => [
                                                'id' => 'satuanunit_id',
                                                'class' => 'form-control select2 input-sm',
                                                'tabindex' => '3'
                                            ],
                                            'pluginOptions' => [
                                                'depends' => ['obatalkes_id'],
                                                'placeholder' => \Yii::t('fe', '-- Pilih Satuan --'),
                                                'url' => Url::to(['/master/tindakan/list-satuan-besar'])
                                            ],
                                            'pluginEvents' => [
                                                'depdrop:afterChange' => 'function(event, id, value, textStatus){
                                                    var _response = $(\'#satuanunit_id\').depdrop(\'getAjaxResults\');
                                                }'
                                            ]
                                        ])->label(false); ?>
                                        <?= Html::activeHiddenInput($model, 'satuaninput_id', ['id' => 'satuaninput_id']); ?>
                                        <?= Html::activeHiddenInput($model, 'satuanunit_id', ['id' => 'satuanunit_id']); ?>
                                        <?= Html::activeHiddenInput($model, 'nilai_konversi', ['id' => 'nilai_konversi']); ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, 'qty_input', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Qty'),
                                            'class' => 'form-control input-sm doco-decimal',
                                            'autocomplete' => "off",
                                            'id' => 'qty_input',
                                            'tabindex' => '4'
                                        ])->label(false); ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, 'qty_konversi', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Qty Konversi'),
                                            'class' => 'form-control input-sm doco-decimal',
                                            'autocomplete' => "off",
                                            'readonly' => 'true',
                                            'id' => 'qty_konversi',
                                        ])->label(false); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group pull-right">
                                            <?= Html::Button(
                                                '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                                    [
                                                        'class' => 'btn btn-success btn-labeled btn-xs btn-block',
                                                        'id' => 'simpan-table-tindakan-bmhp'
                                            ]) ?>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="isi-table-bmhp">
                                    <td class="text-center" colspan="6">
                                        <?=\Yii::t("fe", "No data available in table.");?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
                
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var data_detail = '. $detail_data .';
' . $this->render('js/tindakan-update.js'), View::POS_END);
?>