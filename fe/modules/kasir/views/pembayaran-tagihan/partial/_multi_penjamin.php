<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Modal Form Tabel Multi Penjamin
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
                    'id' => 'multi-penjamin-form',
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
                    <table id="table-multi-penjamin" class="table table-striped">
                        <thead>
                            <tr class="bg-inverse">
                                <th width=3%>No</th>
                                <th width=25%><?=\Yii::t("fe", "Nama Penjamin");?></th>
                                <th><?=\Yii::t("fe", "Nomor Kartu");?></th>
                                <th><?=\Yii::t("fe", "Dijamin (Rp.)");?></th>
                                <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                            </tr>
                            <tr>
                                <td>#</td>
                                <td>
                                    <?= $form->field($model, 'penjamin_id',[
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ],
                                    ])->dropDownList([$penjamin_id => $res_default],[
                                        'class' => 'select2',
                                        'id' => 'penjamin_id',
                                        'tabindex' => '1'
                                    ])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'no_kartu', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-4'
                                        ]
                                    ])->textInput([
                                        'placeholder' => Yii::t('fe', 'Nomor Kartu'),
                                        'class' => 'form-control input-sm',
                                        'type' => 'number',
                                        'autocomplete' => "off",
                                        'id' => 'no_kartu',
                                        'tabindex' => '2'
                                    ])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'dijamin', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-4'
                                        ]
                                    ])->textInput([
                                        'placeholder' => Yii::t('fe', 'Dijamin (Rp.)'),
                                        'class' => 'form-control input-sm doco-number',
                                        'autocomplete' => "off",
                                        'id' => 'dijamin',
                                        'tabindex' => '3'
                                    ])->label(false); ?>
                                </td>
                                <td>
                                    <div class="btn-group pull-right">
                                        <?= Html::Button(
                                            '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                                [
                                                    'class' => 'btn btn-success btn-labeled btn-xs btn-block',
                                                    'id' => 'simpan-table-multi-penjamin'
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
$this->registerJs('

    // Event Ready
    $(document).ready(function(){
        generateTablePenjamin()
    });
', View::POS_END);

$this->registerJs($this->render('../js/_multi_penjamin.js'), View::POS_END);
?>