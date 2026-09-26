<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-03 17:41:39
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-08 18:03:13
 */
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use kartik\widgets\FileInput;
use yii\helpers\Url;
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
	<?php 
	$form = ActiveForm::begin([
					'id'=>'profiluser-form',
					'options'=>[
						'class' => 'form-horizontal', 
	                    'enableAjaxValidation' => true,
	                    'role' => 'form',
						'enctype'=>'multipart/form-data'
					],
				]);
	?>

	<div class="form-group required">				
		<div class="col-lg-12">
			<?=$form->field($model, 'nama_pemakai')->textInput()?>				
		</div>		
	</div>
	<div class="form-group required">				
		<div class="col-lg-11">						
			<?php

			echo $form->field($model, 'jabatan_id')->dropDownList(
						ArrayHelper::map($data_jabatan['response']['data'], 'jabatan_id', 'jabatan_nama'),
						['class' => 'select2 jabatanSelect']
				);
			?>	
			
			
		</div>		
		<br>
		<a class="btn btn-default" style="margin-top: 7px"><i class="fa fa-list-ul"></i></a>
	</div>
	<div class="form-group">
		<div class="col-lg-12">

		<?php 					
		if(!empty($model->photouser)){
			echo Html::img('@web/media/user-foto/'.$model->photouser,['class'=>'img-responsive']);
		}			
			echo $form->field($model, 'photouser')->widget(FileInput::classname(), [
			    'options' => ['accept' => 'image/*','class'=>'file-input','id'=>'photouser-id'],
			]);		
		//echo $form->field($model, 'photouser')->fileInput(['class'=>'file-input']);
		?>
		</div>
	</div>
	<?=$form->field($model, 'loginpemakai_id')->hiddenInput()->label(false)?>
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
// $('#profiluser-form').on('click', function() {
//     var file_data = $('#photouser-id').prop('files')[0];   
//     var form_data = new FormData();                  
//     form_data.append('file', file_data);
//     alert(form_data);                             
//     $.ajax({
//                 url: $(this).attr('action'), // point to server-side PHP script 
//                 dataType: 'text',  // what to expect back from the PHP script, if anything
//                 cache: false,
//                 contentType: false,
//                 processData: false,
//                 data: form_data,                         
//                 type: 'post',
//                 success: function(php_script_response){

//                 }
//      });
// });

	
    $('#profiluser-form').on('submit',function(){
    	var file_data = $("#photouser-id").prop("files")[0];        	
		var form_data = new FormData();
	    form_data.append("photouser", file_data);

		$('#profiluser-form').docoForm('submit',{
	        contentType: false,
	        processData: false,
	        cache: false,
	        data : form_data,           	 
	        success : function(data) {        	
	        	console.log(data);
	            this.formInput[0].reset();
	            _afterSave()
	        }
	    });    
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
</script>