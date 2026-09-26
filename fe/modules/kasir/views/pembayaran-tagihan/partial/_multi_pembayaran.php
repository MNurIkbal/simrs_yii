<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Modal Form Tabel Multi Pembayaran
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

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><strong><?= $title ?></strong></h5>
</div>

<div class="modal-body">
    <div class="panel panel-white">
        <div class="panel-body">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'multi-pembayaran-form',
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
            <div class="form-group col-md-4" style="display: none">
                    <?= $form->field($model, 'tunai', [
                    'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-4',
                            'wrapper' => 'col-md-4'
                        ]
                    ])->textInput([
                        'placeholder' => Yii::t('fe', 'Pembayaran Tunai / Cash'),
                        'class' => 'form-control input-sm doco-number',
                        'autocomplete' => "off",
                        'id' => 'tunai',
                        'tabindex' => '1'
                    ])->label(Yii::t('fe', '<b>Tunai : </b>')); ?>
            </div>
            <div class="form-group col-md-4">
                <?= $form->field($model, 'tunai', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-4'
                    ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'Pembayaran Non Tunai'),
                    'class' => 'form-control input-sm doco-number',
                    'autocomplete' => "off",
                    'id' => 'non-tunai',
                    'tabindex' => '5',
                    'readonly' => true,
                ])->label(Yii::t('fe', '<b>Non Tunai : </b>')); ?>
            </div>
            <div class="form-group col-md-4" style="display: none">
                <?= $form->field($model, 'total', [
                'labelOptions' => ['class' => 'text-left'],
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-4'
                ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'Total'),
                    'class' => 'form-control input-sm doco-number',
                    'autocomplete' => "off",
                    'id' => 'total',
                    'readonly' => 'true'
                ])->label(Yii::t('fe', '<b>Total : </b>')); ?>
            </div>
            <table id="table-multi-pembayaran" class="table table-striped">
                <thead>
                    <tr class="bg-inverse">
                        <th width=1%>No</th>
                        <th width=20%><?=\Yii::t("fe", "Nomor Kartu");?></th>
                        <th width=17%><?=\Yii::t("fe", "Jenis Pembayaran");?></th>
                        <th width=17%><?=\Yii::t("fe", "Mesin EDC");?></th>
                        <th width=20%><?=\Yii::t("fe", "Catatan");?></th>
                        <th width=20%><?=\Yii::t("fe", "Nominal (Rp.)");?></th>
                        <th width="1"><?=\Yii::t("fe", "Aksi");?></th>
                    </tr>
                    <tr>
                        <td>#</td>
                        <td>
                            <?= $form->field($model, 'no_kartu', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Nomor Kartu'),
                                'class' => 'form-control input-sm',
                                'autocomplete' => "off",
                                'id' => 'no_kartu',
                                'tabindex' => '2'
                            ])->label(false); ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'jenisnontunai_id',[
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->dropDownList([],[
                                'class' => 'select2',
                                'id' => 'jenisnontunai_id',
                                'tabindex' => '3'
                            ])->label(false); ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'edclist_id',[
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->dropDownList([],[
                                'class' => 'select2',
                                'id' => 'edclist_id',
                                'tabindex' => '4'
                            ])->label(false); ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'catatan', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Catatan'),
                                'class' => 'form-control input-sm',
                                'type' => 'text',
                                'autocomplete' => "off",
                                'id' => 'catatan',
                                'tabindex' => '5'
                            ])->label(false); ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'nominal', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Nominal (Rp.)'),
                                'class' => 'form-control input-sm doco-number',
                                'autocomplete' => "off",
                                'id' => 'nominal',
                                'tabindex' => '6'
                            ])->label(false); ?>
                        </td>
                        <td>
                            <div class="btn-group pull-right">
                                <?= Html::Button(
                                    '<i class="fa fa-plus"></i>', 
                                        [
                                            'class' => 'btn btn-success btn-labeled btn-xs btn-block',
                                            'id' => 'simpan-table-multi-pembayaran'
                                ]) ?>
                            </div>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="isi-table">
                        <td class="text-center" colspan="6">
                            <?=\Yii::t("fe", "No data available in table.");?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php ActiveForm::end(); ?>
        </div>
</div>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>

<?php
        $this->registerJs($this->render('../js/_multi_pembayaran.js'), View::POS_END);
?>