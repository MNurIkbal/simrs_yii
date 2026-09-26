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
    <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                                <th width=40%><?=\Yii::t("fe", "Komponen");?></th>
                                <th><?=\Yii::t("fe", "Harga (Rp.)");?></th>
                                <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                            </tr>
                            <tr>
                                <td>#</td>
                                <td>
                                    <?= $form->field($model, 'komponentarif_id',[
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ],
                                    ])->dropDownList([],[
                                        'class' => 'select2',
                                        'id' => 'add_komponentarif_id',
                                    ])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'harga_komponen', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-4'
                                        ]
                                    ])->textInput([
                                        'placeholder' => Yii::t('fe', 'Harga (Rp.)'),
                                        'class' => 'form-control input-sm doco-number',
                                        'autocomplete' => "off",
                                        'id' => 'harga_komponen',
                                    ])->label(false); ?>
                                </td>
                                <td>
                                    <div class="btn-group pull-right">
                                        <?= Html::Button(
                                            '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                                [
                                                    'class' => 'btn btn-success btn-labeled btn-xs btn-block',
                                                    'id' => 'simpan-table-add-component'
                                        ]) ?>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="isi-table">
                                <td class="text-center" colspan="4">
                                    <?=\Yii::t("fe", "No data available in table.");?>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td align="right"><p><b>Total Harga : </b></p></td>
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
                                        'class' => 'form-control input-sm doco-number',
                                        'autocomplete' => "off",
                                        'id' => 'total_komponen',
                                        'readonly' => 'true'
                                    ])->label(false); ?>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
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