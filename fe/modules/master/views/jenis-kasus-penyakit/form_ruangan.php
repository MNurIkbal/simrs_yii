<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use softark\duallistbox\DualListbox;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'kasuspenyakitruangan-form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="ruangan_id" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Ruangan'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'ruangan_id')
                ->dropDownList(
                    $listRuangan,
                    [
                        'class' => 'select2 autoListRuangan',
                        // 'id' => 'ruangan_id'
                    ]
                )->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="list_jeniskasuspenyakit_id" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Jenis kasus penyakit'); ?>
        </label>
        <div class="col-lg-9">
            <?php
            echo $form->field($model, 'list_jeniskasuspenyakit_id')
                ->dropDownList(
                    $listKasusPenyakit,
                    [
                        'class' => 'select2 autoListKasusPenyakit', 
                        'multiple'=>'multiple',
                        'id' => 'select2_list_jeniskasuspenyakit_id'
                    ]
                )->label(false); 
            // $options = [
            //     'multiple' => true,
            //     'size' => 20,
            // ];
            // echo $form->field($model, 'list_jeniskasuspenyakit_id')->widget(\softark\duallistbox\DualListbox::className(),[
            //     'items' => $items,
            //     'options' => $options,
            //     'clientOptions' => [
            //         'moveOnSelect' => false,
            //         'selectedListLabel' => 'Selected Items',
            //         'nonSelectedListLabel' => 'Available Items',
            //     ],
            // ]);
            ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('Simpan', ['class' => 'btn btn-success btn-md']) ?>
            <?= Html::button('Kembali',[
                                'class' => 'btn btn-default btn-md',
                                'data-dismiss' => 'modal'
                                ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#kasuspenyakitruangan-form').docoForm('submit',{
        success : function(data) {
            this.formInput[0].reset();
            $('#modal_backdrop').modal('hide');
            _afterSaveRuangan();
        }
    });

    $(".autoListRuangan").change(function() {
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
    });
</script>
