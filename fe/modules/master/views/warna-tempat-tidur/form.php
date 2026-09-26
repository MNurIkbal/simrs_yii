<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\color\ColorInput;
// use kartik\widgets\ColorInput;
?>
<?php
$this->registerCss('
    .sp-palette {
        max-width: 240px !important;
    }.sp-krajee .sp-choose, .sp-krajee .sp-cancel, .sp-krajee button{
        width: 70px;
    }
    .form-horizontal .radio, .form-horizontal .checkbox, .form-horizontal .radio-inline, .form-horizontal .checkbox-inline{
        padding-left: 16px;
    }

');
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    // 'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation'=>false,
    'options' => [
        'skip-confirm' => "true"
    ],
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'kettempattidur_nama')
        ->textInput([
            'class' => 'form-control input-sm',
            'placeholder' => $model->attributeLabels()['kettempattidur_nama']
        ]);
    ?>
    
    <?= $form->field($model, 'kode_warna')->widget(ColorInput::classname(), [
                    'options' => ['placeholder' => '---Pilih Warna--'],
                ]);
            ?>
  <input type="hidden" id="kettempattidur_warna" name="WarnaTempatTidurForm[kettempattidur_warna]" class="form-control input-sm" value="">
  <input type="hidden" id="is_kosong" name="WarnaTempatTidurForm[is_kosong]" class="form-control input-sm" value="1">
  
  <script type="text/javascript">
        $(".form-group").find(".col-sm-offset-4").removeClass('col-sm-offset-4');
        $(".switch").bootstrapSwitch();
        $(document).on("switchChange.bootstrapSwitch", ".switch", function (e, state) {
            if (e.target.checked == true) {
                $value = '1';
                $('input.prop_state').val($value);
            } else {
                $value = '0';
                $('input.prop_state').val($value);
            }
        });
    </script>
    <?php if ($title == "Ubah Warna Tempat Tidur") { ?>

                <?= $form->field($model, 'is_active',[
                'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ]
                ])->radioList(array('1'=>'Aktif','0'=>'Tidak Aktif'),['inline'=>true, ]); ?>
    <?php } ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['id'=>'btn-simpan','class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<?php
    // warnatempattidurform-kode_warna
$this->registerJs('

    ');
?>
<script type="text/javascript">
    $('#warnatempattidurform-kode_warna').change(function() {
      var valKode = $(this).val();
        color = valKode.toLowerCase();
        c = w3color(color);
        if(c.valid){
            var vname =  c.toName();
            console.log(vname);
            $('#kettempattidur_warna').val(vname);
        }else{
            $('#kettempattidur_warna').val('');
        }
    });

    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            var form = $("#ajax-form");
            form[0].reset();
            tableWarnatempattidur.draw();
            $("#modal_backdrop").modal('toggle');
        },
        error : function(data){
            $("span.error").remove();
        }
    });
</script>