<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>

<div class="modal-body">
	<div class="row">
		<div class="col-md-12">
		<?php
		$form = ActiveForm::begin([
		    'id' => 'ajax-form', 
		    'type' => ActiveForm::TYPE_HORIZONTAL,
		    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
		]); 
		?>
		<?php if($jenis == 1): ?>
			<!-- PENDAFTARAN -->
			<?php if($tipe_1 == 0): ?>
				<div class="row">
					<div class="col-md-4 col-md-offset-4">
						<a href="#" type="button" class="btn btn-info btn-lg btn3d" 
							data-fungsi="ambil_tiket"
							data-toggle= "modal",
							data-target= "#modal_backdrop" >
							<h2 class="no-margin text-black">AMBIL ANTRIAN<br/><?php echo strtoupper($title); ?></h2>
						</a>
					</div>
				</div>
			<?php else: ?>
				<?php if($tipe_1 == 1): ?>
					<?php if(! empty($tipe_2)): ?>
						<?php if($tipe_2 == 1): ?>
							<h6><?php echo Yii::t('fe', 'Cari Data Pasien') ?></h6>
							<fieldset>
								<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
									<div class="col-md-10 col-md-offset-3">
										<?php 
											echo $form->field($model, 'no_rekam_medik', [
											    'addon' => [
											        'append' => [
											            'content' => Html::button('Go', [
										            			'class' => 'btn btn-primary',
										            			'onclick' => "var norm = $('#antrianform-no_rekam_medik').val();
											            			$.ajax({
											            				dataType: 'json',
											            				type: 'GET',
											            				cache: false,
											            				url: '".Url::to([$modul .'/cari-pasien'])."?rm='+norm,
											            				success  : function(response) {
	        																$('#display_rekam_medik').val(response.nama_pasien);
	    																}
											            			})"
										            		]), 
											            'asButton' => true
											        ],
											        'groupOptions' => ['class'=>'input-group-xlg '],
											    ]
											])->label(false)->textInput(['class' => 'required']);
										?>

										<?php
											echo $form->field($model, 'display_rekam_medik', [
											    'addon' => ['append' => ['content'=>'<input type="checkbox">']]
											])->label(false)->textInput(['id' => 'display_rekam_medik', 'readonly' => 'readonly']);
										?>
									</div>
								</div>
							</fieldset>
						<?php endif; ?>
					<?php endif; ?>
					<h6><?php echo Yii::t('fe', 'PILIH REKANAN') ?></h6>
					<fieldset>
						<div class="row">
							<div class="col-md-10 col-md-offset-3">
								<?= $form->field($model, 'cara_bayar')->dropDownList($ddlCaraBayar, ['id'=>'cara_bayar','class'=>'bg-info','prompt'=>'— PILIH —'])->label(false); ?>

								<?= $form->field($model, 'penjamin')->widget(DepDrop::classname(), [
									'options'=>['id'=>'penjamin'], 
									'pluginOptions' => [
								        'depends' => ['cara_bayar'],
								        'placeholder' => '-- PILIH --',
								        'url' => Url::to(['/master/penjamin/list-penjamin'])
								    ]
								])->label(false); ?>
							</div>
						</div>
					</fieldset>
				<?php endif; ?>
			<?php endif; ?>

			<h6><?php echo Yii::t('fe', 'PILIH POLI') ?></h6>
			<fieldset>
				<div class="row">
					<div class="col-md-12">

					</div>
				</div>
			</fieldset>

			<h6><?php echo Yii::t('fe', 'PILIH DOKTER') ?></h6>
			<fieldset>
				<div class="row">
					<div class="col-md-12">pilihan rekanan</div>
				</div>
			</fieldset>
		<?php elseif($jenis == 2): ?>
			<!-- kasir -->
		<?php elseif($jenis == 3): ?>
			<!-- apotek -->
			<?php foreach($tipe as $t): ?>
				<?php if($t == 0): ?>
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<a href="#" type="button" class="btn btn-info btn-lg btn3d" 
								data-fungsi="ambil_tiket"
								data-toggle= "modal",
								data-target= "#modal_backdrop" >
								<h2 class="no-margin text-black">AMBIL ANTRIAN<br/><?php echo strtoupper($title); ?></h2>
							</a>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php elseif($jenis == 4): ?>
			<!-- unit_penunjang -->
		<?php endif; ?>
		<?php ActiveForm::end(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#ajax-form').addClass('steps-validation');
	
	var form = $(".steps-validation").show();
	// Initialize wizard
    $(".steps-validation").steps({
        headerTag: "h6",
        bodyTag: "fieldset",
        transitionEffect: "fade",
        titleTemplate: '<span class="number">#index#</span> #title#',
        autoFocus: true,
        onCanceled: function(event){

        },
        onStepChanging: function (event, currentIndex, newIndex) {

            // Allways allow previous action even if the current form is not valid!
            if (currentIndex > newIndex) {
                return true;
            }

            // Forbid next action on "Warning" step if the user is to young
            if (newIndex === 3 && Number($("#age-2").val()) < 18) {
                return false;
            }

            // Needed in some cases if the user went back (clean up)
            if (currentIndex < newIndex) {

                // To remove error styles
                form.find(".body:eq(" + newIndex + ") label.error").remove();
                form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
            }

            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },

        onStepChanged: function (event, currentIndex, priorIndex) {

            // Used to skip the "Warning" step if the user is old enough.
            if (currentIndex === 2 && Number($("#age-2").val()) >= 18) {
                form.steps("next");
            }

            // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
            if (currentIndex === 2 && priorIndex === 3) {
                form.steps("previous");
            }
        },

        onFinishing: function (event, currentIndex) {
            form.validate().settings.ignore = ":disabled";
            return form.valid();
        },

        onFinished: function (event, currentIndex) {
            alert("Submitted!");
        }
    });


    // Initialize validation
    $(".steps-validation").validate({
        ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
        errorClass: 'validation-error-label',
        successClass: 'validation-valid-label',
        highlight: function(element, errorClass) {
            $(element).removeClass(errorClass);
        },
        unhighlight: function(element, errorClass) {
            $(element).removeClass(errorClass);
        },

        // Different components require proper error label placement
        errorPlacement: function(error, element) {

            // Styled checkboxes, radios, bootstrap switch
            if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container') ) {
                if(element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                    error.appendTo( element.parent().parent().parent().parent() );
                }
                 else {
                    error.appendTo( element.parent().parent().parent().parent().parent() );
                }
            }

            // Unstyled checkboxes, radios
            else if (element.parents('div').hasClass('checkbox') || element.parents('div').hasClass('radio')) {
                error.appendTo( element.parent().parent().parent() );
            }

            // Input with icons and Select2
            else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
                error.appendTo( element.parent() );
            }

            // Inline checkboxes, radios
            else if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
                error.appendTo( element.parent().parent() );
            }

            // Input group, styled file input
            else if (element.parent().hasClass('uploader') || element.parents().hasClass('input-group')) {
                error.appendTo( element.parent().parent() );
            }

            else {
                error.insertAfter(element);
            }
        },
        rules: {
            email: {
                email: true
            }
        }
    });
</script>

<!-- CATATAN -->
<?php
/**
 * contoh data {"jenis":1,"tipe":[1,2]}
 * Jenis Antrian
 * 1 - Pendaftaran
 * >> tipe 					>> sub_tipe
 *    0 -> default 			   1 -> Pasien Lama
 *    1 -> jaminan         	   2 -> Pasien Baru
 *    2 -> umum --> ke poli
 * 2 - Kasir
 * >> tipe
 *    0 -> default
 *    1 -> rajal
 *    2 -> ranap
 *    3 -> unit_penunjang
 *    4 -> apotek
 * 3 - Apotek
 * >> tipe
 *    0 -> default
 *    1 -> racikan
 *    2 -> non racikan
 * 4 - Unit Penunjang
 * >> tipe
 *    0 -> default
 *    1 -> Pasien Baru
 *    2 -> Pasien Lama
**/
?>