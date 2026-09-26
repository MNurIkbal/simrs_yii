<?php
    use yii\web\View;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use yii\widgets\Breadcrumbs;
    use yii\web\JsExpression;

    use app\components\DocoHelpers;

    use kartik\widgets\Select2;
    use kartik\widgets\ActiveForm;
    use kartik\widgets\DepDrop;
    // use kartik\select2\Select2Asset;
    use softark\duallistbox\DualListbox;
?>
<style type="text/css">
    .p-bot-50 {
        padding-bottom: 50px;
    }
</style>
<?php
    $form = ActiveForm::begin([
        'id'=>'menudiet-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        // 'type' => ActiveForm::TYPE_INLINE,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
        'options' => [
            'role' => 'form',
            'enctype' => 'multipart/form-data'
        ]
    ]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body p-bot-50">
    <?= $form->field($model, 'jenisdiet_id', [
        'horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-3',
            'wrapper' => 'col-md-9'
        ]
    ])->dropDownList($listJenisDiet, ['prompt' => '— Pilih Jenis Diet —', 
                                        'class' => 'select2',
                                        'disabled' => $disabled
                                    ]);
    ?>
    <?=Html::hiddenInput('MenuDietForm[jenisdiet_id_tmp]', $model->jenisdiet_id);?>
    <?= $form->field($model, 'makanandiet_id[]',[
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->dropDownList($temp,[
                    'class' => 'select2',
                    'id' => 'makananDiet',
                    'multiple'=>'multiple',
                    'value'=> $listValue
                    // 'prompt' => '— Pilih —'
            ]); 
    ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['id'=>'btn-simpan','class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>

<?php ActiveForm::end(); ?>


<script type="text/javascript">
    $('#menudiet-form').docoForm('submit',{
        success : function(data) {
            var form = $("#menudiet-form");
            form[0].reset();
            tableMenuDiet.draw();
            $("#modal_backdrop").modal('toggle');
        },
        error : function(data){
            // $(this).find('.error').hide();
            /*var _res = data.responseJSON.response.data;
            var logo  = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp';
            $.each(_res, function(key, val) {
                if(key == 'MenuDietForm[makanandiet_id]'){
                    var _field     = $('[name="'+ key +'[]"]');
                    var _div       = $('.error_' + key);
                    var _getId = _field.attr('id');
                    var _group     = _field.closest('div.input-group');
                    var _selectize = _field.closest('.form-group').find('div.selectize-control');
                    var _select2   = _field.closest('div').find('.select2-container');
                    // Menambahkan class Error pada form-group
                    _field.parent('div').addClass('has-error');
                    _field.parent('.required').addClass('has-error');
                    $('.field-'+_getId).addClass('has-error');
                    
                    if(_group.length) {
                        _group.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                    } else if(_selectize.length) {
                        _selectize.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                    } else if (_select2.length) {
                        _select2.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                    } else if(_div.length) {
                        _div.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                    } else {
                        _field.after('<span class="help-block error">'+ logo + val[0] +'</span>');
                    }
                }

            });*/
        }
    });



    $(document).ready(function(){
        $('#makananDiet').select2({
            placeholder: "— Pilih Makanan —",
            minimumInputLength: 3, 
            multiple : true,
            ajax : {
                url: baseUrl+"master/end-point/get-data-makanan-diet",
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { 
                    return m; 
                },
            },
            cache: true
        });
       
        /*$(".autoJenisDiet").select2({
            placeholder: "-- Pilih Nama Jenis Diet --",
            // minimumInputLength: 3,
            ajax: {
                url: "/master/end-point/get-jenis-penyakit-all-ruangan",
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        }).on('select2:select', function(e) {
            var data = e.params.data;
            console.log(data);
        });*/
    });
</script>
