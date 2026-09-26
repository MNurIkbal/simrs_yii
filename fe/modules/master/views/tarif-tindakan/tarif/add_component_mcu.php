<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Modal Form Tabel Add Component
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

<style>
#modal_backdrop {
    z-index: 1051 !important;
}
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close btn-close-modal">&times;</button>
    <h5 class="modal-title"><strong><?= $title ?></strong></h5>
</div>

<div class="modal-body">
    <div class="panel-heading">
        <h5 class="panel-title"><b>Tindakan : </b><b id="title-komponen"></b></h5>
    </div>
    <div class="panel panel-white">
        <div class="panel-body">
        <?php
                $form = ActiveForm::begin([
                    'id' => 'add-component-form',
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
                    <table id="table-add-component" class="table table-striped">
                        <thead>
                            <tr class="bg-inverse">
                                <th width=3%>No</th>
                                <th><?=\Yii::t("fe", "Komponen");?></th>
                                <th><?=\Yii::t("fe", "Harga Tarif (Rp.)");?></th>
                                <th><?=\Yii::t("fe", "Harga (Rp.)");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="isi-table">
                                <td class="text-center" colspan="5">
                                    <?=\Yii::t("fe", "No data available in table.");?>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td align="right"><p><b>Total Harga: </b></p></td>
                                <td>
                                <div class="input-group"><span class="input-group-addon">Rp. </span><input type="text" id="total_komponen_origin" class="text-right form-control input-sm doco-number" style="width:100px" name="AddComponent[total_komponen_origin]" readonly="true" placeholder="Total Harga Origin" autocomplete="off"></div>
                                <td>
                                    <?= $form->field($model, 'total_komponen', [
                                        'labelOptions' => ['class' => 'text-left'],
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-4'
                                        ],
                                        'addon'=>[
                                            'prepend' => [
                                                'content'=>'Rp. '
                                            ]
                                        ],
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Total Harga'),
                                            'class' => 'text-right form-control input-sm doco-number',
                                            'autocomplete' => "off",
                                            'style' => "width:100px",
                                            'id' => 'total_komponen',
                                            'readonly' => 'true'
                                        ])->label(false); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm btn-close-modal']); ?>
</div>

<?php
$this->registerJs('
var list_id = '.$list_id.'
    // Event Ready
    $(document).ready(function(){
        var _title = list_komponen[list_id].tindakan
        $("#title-komponen").text(_title)
        generateTableKomponen(list_id)
    });
', View::POS_END);

$this->registerJs($this->render('../js/add_component.js'), View::POS_END);
?>