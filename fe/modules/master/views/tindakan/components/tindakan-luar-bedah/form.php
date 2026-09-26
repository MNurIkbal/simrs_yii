<?php

/**
 * @Author: Ripan
 * @Date:   23 Mei 2022
 */
use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'simpan' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'data-options' => 'click',
                            'form-id' => 'tindakan-luar-bedah-form',
                            'data-render' => 'tindakan-luar-bedah',
                            'data-tab' => 'tab-tindakan-luar-bedah',
                            'data-target' => '#view-tindakan-luar-bedah',
                            'id' => 'btn-simpan-tindakan-luar-bedah'
                        ]
                    ],
                    'kembali' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'spa btn-back-tindakan',
                            'data-options' => 'click',
                            'data-render' => 'tindakan-luar-bedah',
                            'data-tab' => 'tab-tindakan-luar-bedah',
                            'data-target' => '#view-tindakan-luar-bedah',
                        ]
                    ],
                ]) ?>
            </div>
            
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'tindakan-luar-bedah-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
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
                        <?php if (isset($tindakan_id)): ?>
                            <?= $form->field($model, 'daftartindakan_nama', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Tindakan'),
                                'class' => 'form-control input-sm',
                                'readonly' => "true",
                                'value' => $tindakan_nama,
                                'tabindex' => '1'
                            ])->label(Yii::t('fe', 'Tindakan')); ?>

                            <?= Html::activeHiddenInput($model, 'daftartindakan_id', ['id' => 'daftartindakanuntukluarbedah_id', 'value' => $tindakan_id]); ?>
                        <?php else: ?>
                            <?= $form->field($model, 'daftartindakan_id', [
                                    'labelOptions' => ['class' => 'text-right']
                                ])->dropDownList([], [
                                    'class' => 'form-control select2 autoTindakan',
                                    'id' => 'daftartindakanuntukluarbedah_id',
                                    'prompt' => '-',
                                    'tabindex' => '1'
                            ])->label(Yii::t('fe', 'Tindakan')) ?>
                        <?php endif; ?>
                    </div>
                    <hr>
                </div>

                <div class="col-md-12">
                    <div class="panel-body">
                        <table id="table-tindakan-luar-bedah" class="table table-striped">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width=3%>No</th>
                                    <th width=55%><?=\Yii::t("fe", "Tindakan Di Luar Bedah");?></th>
                                    <th><?=\Yii::t("fe", "Qty");?></th>
                                    <th><?=\Yii::t("fe", "Ditagihan");?></th>
                                    <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                                </tr>
                                <tr>
                                    <td>#</td>
                                    <td>
                                        <?= $form->field($model, 'tindakanluarbedah_id',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                        ])->dropDownList([],[
                                            'class' => 'select2',
                                            'id' => 'tindakanluarbedah_id',
                                            'tabindex' => '2'
                                        ])->label(false); ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, 'qty', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Qty'),
                                            'class' => 'form-control input-sm doco-decimal',
                                            'autocomplete' => "off",
                                            'id' => 'qty',
                                            'tabindex' => '4'
                                        ])->label(false); ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, 'ditagihkan')->checkbox([
                                            'id' => 'ditagihkan',
                                        ]); ?>
                                    </td>
                                    <td>
                                        <div class="btn-group pull-right">
                                            <?= Html::Button(
                                                '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                                    [
                                                        'class' => 'btn btn-success btn-labeled btn-xs btn-block',
                                                        'id' => 'simpan-table-tindakan-luar-bedah'
                                            ]) ?>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="isi-table-luar-bedah">
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
    var data_detail = '. $data_detail .';
    var url = "'. $url .'";
' . $this->render('js/tindakan.js'), View::POS_END);
?>