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
<?php
    $form = ActiveForm::begin([
        'id' => 'asuransi-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_VERTICAL,
        // 'formConfig' => [
        //     'labelSpan' => 3,
        //     'deviceSize' => ActiveForm::SIZE_SMALL
        // ],
    ]);
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=Yii::t('fe','Asuransi Baru')?></h5>
</div>

<div class="modal-body">
    <?=Html::activeHiddenInput($modelAsuransi, 'carabayar_id', ['class'=>'carabayar-id'])?>
    <?=Html::activeHiddenInput($modelAsuransi, 'penjamin_id', ['class'=>'penjamin-id'])?>
    <?=Html::activeHiddenInput($modelAsuransi, 'pasien_id', ['class'=>'pasien-id'])?>
    <?=Html::activeHiddenInput($modelAsuransi, 'asalrujukan_id', ['class'=>'asalrujukan-id'])?>

    <div class="form-rujukan">
        <?= $form->field($modelRujukan, 'no_rujukan')->textInput(); ?>
        <?= $form->field($modelRujukan, 'rujukandari_id')->dropDownList($rujukandari, [
                'class' => 'select2',
                'prompt' => '-'
            ])->label(Yii::t('fe', 'Rujukan Dariss'));
        ?>
        <?=
            $form->field($modelRujukan, 'nama_perujuk')->textInput();
        ?>
        <?=
            $form->field($modelRujukan, 'tanggal_rujukan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->textInput(['class'=>'pickadate-w-month']);
        ?>
        <?=
            $form->field($modelRujukan, 'diagnosa_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->dropDownList([], [
                'class' => 'selectDiagnosa',
                'prompt' => '-'
            ])->label(Yii::t('fe', 'Diagnosa'));
        ?>
    </div>
    
    <div class="form-asuransi">
        <?= $form->field($modelAsuransi, 'nokartuasuransi', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->textInput()->label(Yii::t('fe', 'Nomor Asuransi'));
        ?>
        <?=
            $form->field($modelAsuransi, 'namapemilikasuransi', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->textInput()->label(Yii::t('fe', 'Nama Pemilik'));
        ?>
        <?=
            $form->field($modelAsuransi, 'nomorpokokperusahaan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->textInput()->label(Yii::t('fe', 'Nomor Pokok Perusahaan'));
        ?>
        <?=
            $form->field($modelAsuransi, 'kelastanggungan_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->dropDownList($kelaspelayanan, [
                'class' => 'select2',
                'prompt' => '-'
            ])->label(Yii::t('fe', 'Kelas Tanggungan'));
        ?>
        <?=
            $form->field($modelAsuransi, 'penjamingrade_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->dropDownList([], [
                'prompt' => '-'
            ])->label(Yii::t('fe', 'Grade Penjamin'));
        ?>
        <?=
            $form->field($modelAsuransi, 'namaperusahaan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->textInput()->label(Yii::t('fe', 'Nama Perusahaan'));
        ?>
        <?=
            $form->field($modelAsuransi, 'tgl_konfirmasi', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->textInput(['class'=>'pickadate-w-month'])->label(Yii::t('fe', 'Tanggal Konfirmasi'));
        ?>
        <?=
            $form->field($modelAsuransi, 'status_konfirmasi', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->checkbox();
        ?>
    </div>
    <div class="row">
        <div class="col-md-10 text-right">
        <?php //echo Html::submitButton(Yii::t('fe','Simpan'), ['class'=>'btn btn-success btn-sm'])?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(document).ready(function(){
        $('.pickadate-w-month').pickadate({
            format: 'dd mmm, yyyy',
            selectMonths: true,
              selectYears: 99,
            max: true,
            formatSubmit: 'yyyy-mm-dd',
        });
        $(".selectDiagnosa").select2({
            placeholder: "Pilih Diagnosa",
            minimumInputLength: 3,
            ajax: {
                url: "/pendaftaran/daftar-igd/get-diagnosa?type=10",
                dataType: "json",
                quietMillis: 250,
                data: function(params) {
                    var query = {
                        search: params.term
                    }

                    return query;
                },
            },
        });
    })

    $("#asuransi-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
                $("#modal_backdrop").modal('toggle');
        }
    });
</script>
