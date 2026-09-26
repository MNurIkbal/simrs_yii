<?php
    use yii\helpers\ArrayHelper;
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'penjamin-form',
    // 'enableAjaxValidation'=>false,
    // 'enableClientValidation'=>false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'options' => ['enctype'=>'multipart/form-data'],
    'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>

<div class="modal-body">
    <?=$form
        ->field($model, 'carabayar_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($caraBayar, [
            'class' => 'form-control input-sm select2',
            'prompt' => Yii::t('fe', '--Pilih Cara Bayar--'),
        ]);
    ?>
    <?php
        // echo $form->field($model, 'is_online')
        // ->radioList(
        //     [
        //         1 => Yii::t('fe', 'Ya'),
        //         0 => Yii::t('fe', 'Tidak'),
        //      ],
        //     ['id' => 'is_online', 'inline' => true]
        // );
    ?>
    <table class='table' id='tablepenjamin'>
        <thead>
            <tr>
                <th align="center"  style="text-align: left;">
                    <div class="col-lg-10 penjamin-kode required">
                        <?= Yii::t('fe', 'Kode Penjamin'); ?></th>
                    </div>
                <th align="center"  style="text-align: left;">
                    <div class="col-lg-10 nama required">
                        <?= Yii::t('fe', 'Nama Penjamin'); ?></th>
                    </div>
                <th align="left"  style="text-align: left;">
                    <div class="col-lg-10 namalain">
                        <?= Yii::t('fe', 'Nama Lainnya'); ?></th>
                    </div>
                <th align="left"  style="text-align: left;">
                    <div class="col-lg-10 groupmargin_id">
                        <?= Yii::t('fe', 'Group Margin'); ?></th>
                    </div>
                <th align="right" style="text-align: right;">
                    <div class="col-lg-10 button">
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="td-inverse">
                <td>
                    <?= $form->field($model, 'penjamin_kode', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-0',
                                'wrapper' => 'col-md-12'
                            ]
                        ])->textInput([
                            'placeholder' => Yii::t('fe', 'Kode Penjamin'),
                            'class' => 'penjamin-kode form-control check-input',
                            'autocomplete' => "off",
                            'readonly' => false,
                            'name'=> 'PenjaminForm[penjamin_kode][]'
                        ])->label(false);
                    ?>
                </td>
                <td>
                    <?= $form->field($model, 'penjamin_nama', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-0',
                                'wrapper' => 'col-md-12'
                            ]
                        ])->textInput([
                            'placeholder' => Yii::t('fe', 'Nama Penjamin'),
                            'class' => 'penjamin-nama form-control check-input',
                            'autocomplete' => "off",
                            'readonly' => false,
                            'name'=> 'PenjaminForm[penjamin_nama][]'
                        ])->label(false);
                    ?>
                </td>
                <td>
                    <?= $form->field($model, 'penjamin_namalainnya', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-0',
                                'wrapper' => 'col-md-12'
                            ]
                        ])->textInput([
                            'placeholder' => Yii::t('fe', 'Nama Lainnya'),
                            'class' => 'penjamin-lain form-control check-input',
                            'autocomplete' => "off",
                            'readonly' => false,
                            'name'=> 'PenjaminForm[penjamin_namalainnya][]'
                        ])->label(false);
                    ?>
                </td>
                <td>
                    <?= $form->field($model, 'groupmargin_id', [
                            'labelOptions' => ['class' => 'text-left', 'style' => 'display: none'],
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-0',
                                'wrapper' => 'col-md-12'
                            ]])
                        ->dropDownList($margingroup, [
                            'class' => 'form-control input-sm select2',
                            'prompt' => '--Pilih Group Margin--',
                            'name' => 'PenjaminForm[groupmargin_id][]'
                        ]);
                    ?>
                </td>
                <td>
                     <button id="btn-add" type="button" class='btn btn-success' style="margin-bottom: 10px;" onclick="addForm(this);"><i class='fa fa-plus'/></button>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="modal-footer">
        <button type="submit" id="btn-simpan" class="btn btn-info btn-labeled btn-xs data-save" data-target="ajax-form" onclick=""><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
        <button type="button" class="btn btn-info btn-labeled btn-xs data-back" data-dismiss="modal"><b><i class="fa fa-arrow-left"></i></b>Kembali</button>
    </div>
</div>

<script type="text/javascript">
    var _inc = 0;
    $('#penjamin-form').docoForm('submit',{
        success : function(data) {
            var form = $("#penjamin-form");
            form[0].reset();
            table2.draw();
            $("#modal_backdrop").modal('toggle');
        },
        error : function(data){
            var res = data.responseJSON.response.data;
            for (var objProp in res) {
                res[objProp].forEach(el => {
                    docoNotification("error", i18next.t("Proses Gagal !"), i18next.t(el));
                })
            }

            $.each($('.check-input'), function () {
                var _parent = $(this).closest('div')
                var _helpBlock = _parent.find('.help-block')
                if (_helpBlock.html() === '' || typeof _helpBlock.html() === 'undefined') {
                    _parent.closest('td').find('div.has-error').removeClass('has-error')
                }
            })
            // $(this).find('.error').hide();
                // $(document).ready(function () {
                //     // $('.check-input').trigger('change');
                //     var lainnya = document.getElementsByClassName("penjamin-lain");
                //     for(var i=0; i<lainnya.length; i++) {
                //         // lainnya[i].setAttribute("style", "margin-bottom: 30px;");
                //         // $('input[name="PenjaminForm[penjamin_nama]['+[i]+']"]').attr("style", "margin-bottom: 30px;");
                //     }
                //     // $("input#penjaminform-penjamin_namalainnya").attr("style", "margin-bottom: 30px;");
                //     $("#btn-add").attr("style", "margin-bottom: 30px;");
                //     $("div.help-block").remove();
                //     console.log($(this).val() );
                // });
        }
    });
    function addForm(e) {
        _inc++;
        var template = '<tr>' +
                '<td>' +
                    '<div class="form-group field-penjaminform-penjamin_kode required">' +
                        '<div class="col-lg-12">' +
                        '<input type="text" id="penjaminform-penjamin_kode" class="penjamin-kode form-control check-input" name="PenjaminForm[penjamin_kode][]" placeholder= "<?=Yii::t('fe', 'Nama Penjamin')?>">' +
                            '<div class="help-block"></div>'+
                    '</div>' +
                '</td>' +
                '<td>' +
                    '<div class="form-group field-penjaminform-penjamin_nama required">' +
                        '<div class="col-lg-12">' +
                        '<input type="text" id="penjaminform-penjamin_nama" class="penjamin-nama form-control check-input" name="PenjaminForm[penjamin_nama][]" placeholder= "<?=Yii::t('fe', 'Nama Penjamin')?>">' +
                            '<div class="help-block"></div>'+
                    '</div>' +
                '</td>' +
                '<td>' +
                    '<div class="col-lg-12">' +
                        '<div class="form-group field-penjaminform-penjamin_namalainnya required">' +
                        '<input type="text" id="PenjaminForm-penjamin_namalainnya" class="penjamin-lain form-control check-input" name="PenjaminForm[penjamin_namalainnya][]" placeholder= "<?=Yii::t('fe', 'Nama Lainnya')?>">' +

                    '</div>' +
                '</td>' +
                '<td>' +
                    `<?= $form->field($model, 'groupmargin_id', [
                            'labelOptions' => ['class' => 'text-left', 'style' => 'display: none'],
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-0',
                                'wrapper' => 'col-md-12'
                            ]])
                        ->dropDownList($margingroup, [
                            'class' => 'form-control input-sm select2',
                            'prompt' => '--Pilih Group Margin--',
                            'name' => 'PenjaminForm[groupmargin_id][]'
                        ]);
                    ?>` +
                '</td>' +
                '<td>' +
                        '<button type="button" class="btn btn-danger btn-sm btn-deletes"><i class="fa fa-trash"></i></button>'+
                '</td>' +
            '</tr>';
        $('#tablepenjamin > tbody').append(template);
        $('.btn-deletes').on('click', function(){
            $(this).parent().parent().remove();
        });
        $('.select2').select2();
    }

    $(document).on('keydown', null, 'alt+s', function (event) {
        $("#btn-simpan").click();
    });

    $(document).on('keydown', null, 'alt+S', function (event) {
        $("#btn-simpan").click();
    });
</script>

<?php ActiveForm::end(); ?>
