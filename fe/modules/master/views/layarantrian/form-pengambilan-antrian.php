<?php
    use yii\helpers\Html;
    use kartik\form\ActiveForm;
    use kartik\file\FileInput;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<?php 
$form = ActiveForm::begin([
        'id' => 'layarantrian-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'options' => ['enctype'=>'multipart/form-data'],
        'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);

?>
<div class="modal-body">
    <?= $form->field($model, 'layarantrian_jenis',['labelSpan' => 3])->dropDownList($ddlTypeScreen, ['id'=>'layarantrian_jenis','prompt'=>'— PILIH —']) ?>

    <?= $form->field($model, 'layarantrian_fungsi',['labelSpan' => 3])->dropDownList($ddlFunctionScreen, ['id'=>'layarantrian_fungsi','prompt'=>'— PILIH —']) ?>

    <?= $form->field($model, 'is_active',['labelSpan' => 3])->dropDownList($status, ['id'=>'is_active']) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton('Simpan', ['id' => 'submitButton','class' => 'btn btn-success btn-md']) ?>
    <?= Html::button('Kembali',[
                        'class' => 'btn btn-default btn-md',
                        'data-dismiss' => 'modal'
                        ]); ?>
</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
    var form_data = null;

    // $('#submitButton').on('click', function(){
    //     file_data = $('#layarantrian_latarbelakang').prop('files')[0];   
    //     form_data = new FormData();                  
    //     form_data.append('file', file_data);

        

    //     return false;
    // });

    $('#layarantrian-form').docoForm('submit',{
            data: {
                data: $('#layarantrian-form').serializeArray(),
                form_data: form_data
            },  
            before: function(){
                console.log(form_data);
                return false;
            },
            success : function(data) {
                _afterSave()
            }
        });
    

    

    var modalTemplate = '<div class="modal-dialog modal-lg" role="document">\n' +
        '  <div class="modal-content">\n' +
        '    <div class="modal-header">\n' +
        '      <div class="kv-zoom-actions btn-group">{toggleheader}{fullscreen}{borderless}{close}</div>\n' +
        '      <h6 class="modal-title">{heading} <small><span class="kv-zoom-title"></span></small></h6>\n' +
        '    </div>\n' +
        '    <div class="modal-body">\n' +
        '      <div class="floating-buttons btn-group"></div>\n' +
        '      <div class="kv-zoom-body file-zoom-content"></div>\n' + '{prev} {next}\n' +
        '    </div>\n' +
        '  </div>\n' +
        '</div>\n';

    // Buttons inside zoom modal
    var previewZoomButtonClasses = {
        toggleheader: 'btn btn-default btn-icon btn-xs btn-header-toggle',
        fullscreen: 'btn btn-default btn-icon btn-xs',
        borderless: 'btn btn-default btn-icon btn-xs',
        close: 'btn btn-default btn-icon btn-xs'
    };

    // Icons inside zoom modal classes
    var previewZoomButtonIcons = {
        prev: '<i class="icon-arrow-left32"></i>',
        next: '<i class="icon-arrow-right32"></i>',
        toggleheader: '<i class="icon-menu-open"></i>',
        fullscreen: '<i class="icon-screen-full"></i>',
        borderless: '<i class="icon-alignment-unalign"></i>',
        close: '<i class="icon-cross3"></i>'
    };

    // File actions
    var fileActionSettings = {
        zoomClass: 'btn btn-link btn-xs btn-icon',
        zoomIcon: '<i class="icon-zoomin3"></i>',
        dragClass: 'btn btn-link btn-xs btn-icon',
        dragIcon: '<i class="icon-three-bars"></i>',
        removeClass: 'btn btn-link btn-icon btn-xs',
        removeIcon: '<i class="icon-trash"></i>',
        indicatorNew: '<i class="icon-file-plus text-slate"></i>',
        indicatorSuccess: '<i class="icon-checkmark3 file-icon-large text-success"></i>',
        indicatorError: '<i class="icon-cross2 text-danger"></i>',
        indicatorLoading: '<i class="icon-spinner2 spinner text-muted"></i>'
    };

    $('.file-input').fileinput({
        browseLabel: 'Browse',
        browseIcon: '<i class="icon-file-plus"></i>',
        uploadIcon: '<i class="icon-file-upload2"></i>',
        removeIcon: '<i class="icon-cross3"></i>',
        layoutTemplates: {
            icon: '<i class="icon-file-check"></i>',
            modal: modalTemplate
        },
        initialCaption: "No file selected",
        previewZoomButtonClasses: previewZoomButtonClasses,
        previewZoomButtonIcons: previewZoomButtonIcons,
        fileActionSettings: fileActionSettings
    });

    $(".touchspin-vertical").TouchSpin({
        min: 0,
        max: 10,
        verticalbuttons: true,
        verticalupclass: 'icon-arrow-up22',
        verticaldownclass: 'icon-arrow-down22'
    });
</script>