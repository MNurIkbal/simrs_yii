<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Modal Form Tabel Diskon Dokter
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
                    'id' => 'diskon-dokter-form',
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
                    <table id="table-diskon-dokter" class="table table-striped">
                        <thead>
                            <tr class="bg-inverse">
                                <th width=3%>No</th>
                                <th width=25%><?=\Yii::t("fe", "Nama Dokter");?></th>
                                <th><?=\Yii::t("fe", "Jasa Dokter (Rp.)");?></th>
                                <!-- <th>< ? =\Yii::t("fe", "Diskon"); ? ></th> -->
                                <th><?=\Yii::t("fe", "Nominal (Rp.)");?></th>
                                <th><?=\Yii::t("fe", "Alasan");?></th>
                                <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                            </tr>
                            <tr>
                                <td>#</td>
                                <td>
                                    <?= $form->field($model, 'dokter_id',[
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ],
                                    ])->dropDownList([],[
                                        'class' => 'select2',
                                        'id' => 'dokter_id',
                                        'tabindex' => '1'
                                    ])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'jasa_dokter', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-4'
                                        ]
                                    ])->textInput([
                                        'placeholder' => Yii::t('fe', 'Jasa Dokter (Rp.)'),
                                        'class' => 'form-control input-sm doco-number',
                                        'autocomplete' => "off",
                                        'id' => 'jasa_dokter',
                                        'readonly' => true,
                                        'tabindex' => '2'
                                    ])->label(false); ?>
                                </td>
                                <!-- <td>
                                    < ? php echo $form->field($model, 'diskon', [
                                            'addon' => [
                                                'prepend' => [
                                                    'content' => '<label>
                                                    <input type="checkbox" id="is_persentase" name="DiskonDokter[is_persentase]" value="0" autocomplete="off"> Persen
                                                    </label>',
                                                ]
                                            ]
                                        ])
                                        ->textInput(
                                            [
                                                // 'placeholder' => Yii::t('fe', 'diskon'),
                                                'class' => 'form-control input-sm doco-number',
                                                'autocomplete' => "off",
                                                'id' => 'diskon',
                                                'tabindex' => '3'
                                            ]
                                        )->label(false);
                                    ? >
                                </td> -->
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
                                        'tabindex' => '4'
                                    ])->label(false); ?>
                                </td>
                                <td>
                                    <?= $form->field($model, 'alasan', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-4'
                                        ]
                                    ])->textarea([
                                        'placeholder' => Yii::t('fe', 'Alasan'),
                                        'class' => 'form-control input-sm',
                                        'autocomplete' => "off",
                                        'id' => 'alasan',
                                        'tabindex' => '5'
                                    ])->label(false); ?>
                                </td>
                                <td>
                                    <div class="btn-group pull-right">
                                        <?= Html::Button(
                                            '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                                [
                                                    'class' => 'btn btn-success btn-labeled btn-xs btn-block',
                                                    'id' => 'simpan-table-diskon-dokter'
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

    var list_dokter = []
    var list_jasa_dokter = []
    var data = []
    var data_list = []
    list_dokter = '.json_encode($list_dokter).'
    list_jasa_dokter = '.json_encode($list_jasa_dokter).'
    data.push(
        {
            id: "",
            text: ""
        }
    )
    $.each(list_dokter, function(key, val) {
        var _listdata = {
                id: key,
                text: val,
        }
        data.push(_listdata)
    })

    // Event Ready
    $(document).ready(function(){
        generateTableDiskonDokter()
    });
', View::POS_END);

$this->registerJs($this->render('../js/_diskon_dokter.js'), View::POS_END);
?>