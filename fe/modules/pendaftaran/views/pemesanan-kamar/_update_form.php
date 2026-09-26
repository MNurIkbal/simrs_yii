<?php

/**
 * @author Naufal Ziyad L
 * @Date 29/01/2018
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
        	<div class="panel-heading">
        		<h3 class="panel-title"><b><?= $this->title; ?></b></h3>
        		<?php echo Breadcrumbs::widget([
                      'homeLink' => [ 
                                      'label' => Yii::t('fe', 'Home'),
                                      'url' => Yii::$app->homeUrl,
                                 ],
                      'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                   ]); 
                ?>
        	</div>
        	<div class="panel-body" style="padding:10px;">
			<?php 
			    $form = ActiveForm::begin([
			        'id' => 'form-pemesanan-kamar', 
			        'type' => ActiveForm::TYPE_HORIZONTAL,
			        'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
			    ]); 
			?>
			<fieldset class="content-group">
			<div class="row" >  
				<div class="col-md-11 panel panel-flat" style="margin-left:15px">       
					<legend class="text-bold">Data Pasien</legend>
					<div class="col-md-4" style="margin-left:5%">
						<!-- <?= $form->field($modelPasien, 'no_rekam_medik') ?> -->
						<?= $form->field($modelPasien, 'jenisidentitas')->dropDownList($ddlJenisID, ['id'=>'jenisidentitas','prompt'=>'— PILIH —']) ?>
						<?= $form->field($modelPasien, 'no_identitas_pasien', [
						    'addon' => [
						        'append' => [
						            ['content' => '<i class="fa fa-list "></i>'],
						        ],
						    ] ]) ?>
						<?= $form->field($modelPasien, 'namadepan')->dropDownList($ddlNamaDepan, ['id'=>'namadepan','prompt'=>'— PILIH —']) ?>
						<?= $form->field($modelPasien, 'nama_pasien', [
						    'addon' => [
						        'append' => [
						            ['content' => '<i class="fa fa-list "></i>'],
						        ],
						    ] ]) ?>
						<!-- <?= $form->field($modelPasien, 'nama_bin') ?> -->
						<?= $form->field($modelPasien, 'tempat_lahir') ?>
						<?= $form->field($modelPasien, 'tanggal_lahir', [
						    'addon' => [
						        'append' => [
						            ['content' => '<i class="fa fa-calendar "></i>'],
						        ],
						    ] ])->textInput(['class' => 'pickadate']) ?>
						<?= $form->field($modelPasien, 'umur')->staticInput(); ?>
						<?= $form->field($modelPasien, 'jeniskelamin')->radioList($ddlJenisKelamin, ['inline'=>true]); ?>
						<?= $form->field($modelPasien, 'statusperkawinan')->dropDownList($ddlStatusPerkawinan, ['id'=>'statusperkawinan','prompt'=>'— PILIH —']) ?>
						<?= $form->field($modelPasien, 'nama_ibu') ?>
					</div>
					<div class="col-md-4" style="margin-left:15%">
						<?= $form->field($modelPasien, 'alamat_pasien')->textArea(); ?>
						<?= $form->field($modelPasien, 'rt'); ?>
						<?= $form->field($modelPasien, 'rw'); ?>

						<?= $form->field($modelPasien, 'propinsi_id')->dropDownList($ddlPropinsi, ['id'=>'propinsi_id','prompt'=>'— PILIH —']) ?>
						<?= $form->field($modelPasien, 'kabupaten_id')->widget(DepDrop::classname(), [
						    'options'=>['id'=>'kabupaten_id'],
						    'pluginOptions'=>[
						        'depends'=>['propinsi_id'],
						        'placeholder'=>'-- PILIH --',
						        'url'=>Url::to(['/master/kabupaten/list-kabupaten'])
						    ]
						]); ?>
						<?= $form->field($modelPasien, 'kecamatan_id')->widget(DepDrop::classname(), [
						    'options'=>['id'=>'kecamatan_id'],
						    'pluginOptions'=>[
						        'depends'=>['kabupaten_id'],
						        'placeholder'=>'-- PILIH --',
						        'url'=>Url::to(['/master/kecamatan/list-kecamatan'])
						    ]
						]); ?>
						<?= $form->field($modelPasien, 'kelurahan_id')->widget(DepDrop::classname(), [
						    'options'=>['id'=>'kelurahan_id'],
						    'pluginOptions'=>[
						        'depends'=>['kecamatan_id'],
						        'placeholder'=>'-- PILIH --',
						        'url'=>Url::to(['/master/kelurahan/list-kelurahan'])
						    ]
						]); ?>
						<?= $form->field($modelPasien, 'no_telepon_pasien'); ?>
						<?= $form->field($modelPasien, 'no_mobile_pasien'); ?>
						<?= $form->field($modelPasien, 'pekerjaan_id')->dropDownList($ddlPekerjaan, ['id'=>'pekerjaan_id','prompt'=>'— PILIH —']) ?>
						<?= $form->field($modelPasien, 'warga_negara')->dropDownList($ddlWargaNegara, ['id'=>'warga_negara','prompt'=>'— PILIH —']) ?>
						<?= $form->field($modelPasien, 'agama')->dropDownList($ddlAgama, ['id'=>'agama','prompt'=>'— PILIH —']) ?>
					</div>
				</div>
			</div>
				
				<div class="row" >  
					<div class="col-md-11 panel panel-flat" style="margin-left:15px">       
						<legend class="text-bold">Data Pemesanan</legend>
					<div class="col-md-4" style="margin-left:5%">
						<!-- <?= $form->field($modelPasien, 'no_rekam_medik') ?> -->
						<?= $form->field($modelPasien, 'no_identitas_pasien')->staticInput(); ?> <!-- Butuh Konfirmasi SA -->
						<?= $form->field($modelRuangan, 'ruangan_id')->dropDownList($ddlRuangan, ['id'=>'ruangan_id','prompt'=>'— PILIH —']) ?> <!-- Butuh Konfirmasi SA -->
						<?= $form->field($modelRuangan, 'ruangan_id')->dropDownList($ddlRuangan, ['id'=>'ruangan_id','prompt'=>'— PILIH —']) ?> <!-- Butuh Konfirmasi SA -->
						<?= $form->field($modelRuangan, 'ruangan_id')->dropDownList($ddlRuangan, ['id'=>'ruangan_id','prompt'=>'— PILIH —']) ?> <!-- Butuh Konfirmasi SA -->
						<?= $form->field($modelPasien, 'tgl_rekam_medik', [
						    'addon' => [
						        'append' => [
						            ['content' => '<i class="fa fa-calendar "></i>'],
						        ],
						    ] ])->textInput(['class' => 'pickadate']) ?>
						<?= $form->field($modelPasien, 'alamat_pasien')->textArea(); ?>
					</div>
				</div>
			</div>
			</fieldset>
			<?php ActiveForm::end(); ?>
        	</div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
	$('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',
        });

	function take_snapshot() {
			// take snapshot and get image data
			Webcam.snap( function(data_uri) {
				$('#profilePict').attr('src',data_uri);
			} );
		}

	$('#panggilAntian').on('click', function(){
		$('.modal-antrian').modal('show');
	});

	$('#open-camera').on('click',function(){
		$('.camera-modal-sm').modal('show');

		Webcam.set({
			width: 200,
			height: 200,
			image_format: 'jpeg',
			jpeg_quality: 90
		});
		Webcam.attach( '#my_camera' );
	});
",View::POS_END,'daftar-rajal');
?>