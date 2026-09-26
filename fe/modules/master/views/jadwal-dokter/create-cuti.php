<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php
    $form = ActiveForm::begin([
        'id' => 'create-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'action' => $formAction,    
        'formConfig' => [
            'labelSpan' => 3, 
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>
    
    <?= $form->field($model, 'pegawai_id')->dropDownList( 
        $dokterList,
        [
            'options' => $dokterOptions,
            'class' => 'select2',
            'id' => 'pegawaicuti_id',
            'prompt' => Yii::t('fe', '-- Pilih --'),
        ]);
    ?>
    <?= $form->field($model, 'spesialis_nama')->textInput(['id' => 'spesialis_nama', 'readonly' => true]) ?>
    <?= $form->field($model, 'spesialis_id')->hiddenInput(['id' => 'spesialis_id'])->label(false); ?>
    <?= $form->field($model, 'ruangan_id')->widget(DepDrop::classname(), [
        'name' => 'ruangancuti_id',
        'options' => [
            'class' => 'select2',
            'id' => 'ruangancuti_id',
        ],
        'pluginOptions' => [
            'depends' => ['pegawaicuti_id'],
            'placeholder' => Yii::t('fe', '-- Pilih --'),
            'url' => Url::to(['/master/jadwal-dokter/dep-list-ruangan-pegawai'])
        ],
    ]); ?>
    <?= $form->field($model, 'tgl_cuti_awal', [
        'horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-3',
            'wrapper' => 'col-md-9'
        ],
        'inputOptions'=>['id'=>'tgl_cuti_awal'],
        'addon' => [
            'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
        ]
    ]); ?>
    <?= $form->field($model, 'tgl_cuti_akhir', [
        'horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-3',
            'wrapper' => 'col-md-9'
        ],
        'inputOptions'=>['id'=>'tgl_cuti_akhir'],
        'addon' => [
            'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
        ]
    ]); ?>
    <?= $form->field($model, 'alasan_cuti')->textarea(['rows' => '4'],['class' => 'form-control']); ?>

    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        initPickDate('#tgl_cuti_awal', {
            min: new Date(),
        }, new Date());
        initPickDate('#tgl_cuti_akhir', {
            min: new Date($('#tgl_cuti_awal').val()),
        }, new Date());
    });

    $('#pegawaicuti_id').on('change', function() {
        var spesialis_id = $(this).find('option:selected').data('spesialis_id'); 
        var spesialis_nama = $(this).find('option:selected').data('spesialis_nama'); 

        $('#spesialis_id').val(spesialis_id); 
        $('#spesialis_nama').val(spesialis_nama); 
    });

    $('#tgl_cuti_awal').on('change', function() {
        var tglAkhir = $('#tgl_cuti_akhir');
        var submitVal = formatDate($(this).val());
        var tglAkhirSubmit = $('input[name="JadwalCutiForm[tgl_cuti_akhir]_submit"]');

        if (new Date($(this).val()) > new Date(tglAkhir.val())) {
            tglAkhir.val($(this).val()).trigger('change');
            tglAkhirSubmit.val(submitVal).trigger('change');
        }
        initPickDate('#tgl_cuti_akhir', {}, new Date(tglAkhir.val()), $(this).val());
    });

    $("#create-form").docoForm("submit",{
        success : function(data) {
            table.ajax.reload();
            $("#modal_backdrop").modal("toggle");
        },
    });

    function initPickDate(inputId, options, startDate, minDate = null) {
        options.format = 'dd mmm yyyy';
        options.formatSubmit = 'yyyy-mm-dd';
        options.onStart = function () {
            var date = startDate;
            this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
        var pickDate = $(inputId).pickadate(options);

        if (minDate) {
            pickDate.pickadate('picker').set('min', new Date(minDate))
        }
    }

    function formatDate(data) {
        var date = new Date(data);
        var formattedDate = date.getFullYear() + 
            '-' + (date.getMonth() + 1) + 
            '-' + date.getDate();

        return formattedDate;
    }
</script>
