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
    use softark\duallistbox\DualListbox;
?>
<?php
    $form = ActiveForm::begin([
        'id'=>'kasuspenyakitruangan-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="form-group required">
        <label class="control-label col-sm-3"><?=$model->attributeLabels()['ruangan_id']?></label>
        <div class="col-sm-9">
            <?=Html::activeDropdownList($model, 'ruangan_id', $options, ['class'=>'form-control autoListRuangan'])?>
        </div>
    </div>

    <?= $form->field($model, 'jeniskasuspenyakit_id', [
        'horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-3',
            'wrapper' => 'col-md-9'
        ]
    ])->dropDownList($listKasusPenyakit, ['prompt' => '-- Pilih Jenis Kasus Penyakit --', 'class' => 'select2 autoJenisKasusPenyakit']);
    ?>
    <?= $form->field($model, 'is_active',[
        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-3',
            'wrapper' => 'col-md-9'
        ]
        ])->radioList([
            1=> Yii::t('fe', 'Aktif'),
            0=> Yii::t('fe', 'Tidak Aktif'),
         ], 
        ['id'=>'is_active', 'inline'=>true, 'value'=> $model->is_active ]); 
    ?>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['id'=>'btn-simpan','class' => 'btn btn bg-teal btn-sm']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
<?php ActiveForm::end(); ?>
</div>


<script type="text/javascript">
    $('#kasuspenyakitruangan-form').docoForm('submit',{
        success : function(data) {
            var form = $("#kasuspenyakitruangan-form");
            form[0].reset();
            tableRuangan.draw();
            $("#modal_kasuspenyakitruangan").modal('toggle');
            // $("#modal_backdrop").modal('toggle');
        },
        error : function(data){
            $(this).find('.error').hide();
            $(document).ready(function () {
                $("div.help-block").remove();
                console.log($(this).val() );
            });
        }
    });

    $(document).ready(function(){

        $(".autoListRuangan").select2({
            placeholder: "-- Pilih Ruangan --",
            minimumInputLength: 3,
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
        });


    });

    /*$(".autoListRuangan").change(function() {
        var ruangan_id = $(this).val();
        $.ajax({
            type: 'GET',
            url: '/master/jenis-kasus-penyakit/find-penyakit-ruangan?ruangan_id=' + ruangan_id,
            dataType: 'JSON',
            success: function(res){
                var listKasusPenyakit = new Array();
                $.each(res['response'], function(i, item) {
                    listKasusPenyakit[i] = item.jeniskasuspenyakit_id;
                });
                $('.autoListKasusPenyakit').val(listKasusPenyakit).trigger('change');
            },
        });
    });*/
</script>
