<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php

    $form = ActiveForm::begin([
        'id' => 'batal-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false,
        'enableClientValidation'=>false,
        'action' => '/pendaftaran/informasi-pasien/pengajuan-sep?id='.$model->pendaftaran_id,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>

    <?= $form->field($model, 'no_kartu')->textInput() ?>
    <?= $form->field($model, 'nama_pasien')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'tgl_sep', [
            'addon' => [
                'append' => [
                    'content' => '<i class="fa fa-calendar"></i>'
                ]
            ]
        ])->textInput([
            'id' => 'tgl_sep',
            'class' => 'form-control input-sm pickadate-w-month',
            'placeholder' => $model->getAttributeLabel('tgl_sep'),
            'autocomplete' => 'off',
            'readonly' => true
        ])->label($model->getAttributeLabel('tgl_sep')) ?>

    <?= $form->field($model, 'jenis_pelayanan')
                ->dropDownList(
                    [
                        '2'=>Yii::t('fe', 'Rawat Jalan'),
                        '1'=>Yii::t('fe', 'Rawat Inap'),
                    ],
                    [
                        'id'=>'jenis_pelayanan',
                        'class' => 'select2',
                        'disabled' => false,
                        'prompt'=>'— PILIH —',
                    ]
                );
    ?>
    <?php $model->jenis_pengajuan = 1;
        echo $form->field($model, 'jenis_pengajuan')
                ->dropDownList(
                    [
                        '2'=>Yii::t('fe', 'Pengajuan Finger Print'),
                        '1'=>Yii::t('fe', 'Pengajuan Backdate'),
                    ],
                    [
                        'id'=>'jenis_pengajuan',
                        'class' => 'select2',
                        'disabled' => false,
                        'prompt'=>'— PILIH —',
                    ]
                );
    ?>
    <?= $form->field($model, 'keterangan')->textarea(['rows' => '4'],['class' => 'form-control']); ?>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    $(function () {
        $('.doco-number').trigger('change')
    })
    $("#batal-form").docoForm("submit",{
        success : function(data) {
            // handle table not found
            try {
                table.draw();
            }
            catch(err) {

            }

            $("#modal_backdrop").modal("toggle");
        },
    });
    var date = new Date();
    var today = [date.getFullYear(), date.getMonth(), date.getDate()];
    var tomorrow = [date.getFullYear(), date.getMonth(), date.getDate() + 6];
    $('.pickadate-w-month').pickadate({
        format: 'yyyy-mm-dd',
        selectMonths: true,
        selectYears: 99,
        formatSubmit: 'yyyy-mm-dd',
        max: today,
    });
</script>
