<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-06-08 09:09:05
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-06-26 17:09:02
 */
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\helpers\Url;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Rawat Inap', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
// dump($modelDetail);die;

?>

<?php 
$form = ActiveForm::begin([
    'id' => 'form', 
    // 'enableAjaxValidation' => true,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>

<div class="panel-body">
<legend><?=Yii::t('fe', $title)?></legend>
	<div class="row">
		<div class="col-md-6">
			<?=$form->field($model, 'info_penyakit', ['labelOptions' => ['class' => 'text-left']])->textArea([
				'placeholder' => $model->getAttributeLabel('info_penyakit'), 'class' => 'form-control input-sm', 'rows' => 5
			]); ?>
			<?=$form->field($model, 'lama_perawatan', [
                'labelOptions' => ['class' => 'text-left'],
                'addon' => ['append' => ['content' => Yii::t('fe', 'Hari')]],
            ])->textInput([
                'class' => 'form-control input-sm docoNumberOnly', 
                'placeholder' => Yii::t('fe', $model->getAttributeLabel('lama_perawatan'))
            ]); ?>
            <?=$form->field($model, 'rencana_pulang', [
                'labelOptions' => ['class' => 'text-left'],
                'addon' => ['append' => ['content' => '<i class="fa fa-calendar"></i>']],
            ])->textInput([
                'class' => 'form-control input-sm date', 
                'placeholder' => Yii::t('fe', $model->getAttributeLabel('rencana_pulang'))
            ]); ?>
			<?=$form->field($model, 'rencana_perawatan', ['labelOptions' => ['class' => 'text-left']])->textArea([
				'placeholder' => $model->getAttributeLabel('rencana_perawatan'),'class' => 'form-control input-sm', 'rows' => 5]); ?>
			<?=$form->field($model, 'rencana_transportasi', ['labelOptions' => ['class' => 'text-left']])->textArea([
				'placeholder' => $model->getAttributeLabel('rencana_transportasi'),'class' => 'form-control input-sm', 'rows' => 5]); ?>
		</div>
	</div>
	<div class="row">
		<h4><?=Yii::t('fe', 'Edukasi Kesehatan untuk di rumah')?></h4>
		<?php foreach ($response['assesment'] as $key => $value) :  ?>
		<?php 
		$checked = '';
		if(!is_null($modelDetail->edukasi_kesehatan)) {
			if(in_array($value['lookupkeperawatan_id'], $modelDetail->edukasi_kesehatan)) {
				$checked =  'checked';
			}
		}
		?>
		<div class="col-md-12">
			<div class="col-md-2">
				<label for="" class="text-left control-label">
				<input id="edukasi_kesehatan_<?= $value['lookupkeperawatan_id'] ?>" type="checkbox" name="RencanaPulangDetailForm[edukasi_kesehatan][<?= $value['lookupkeperawatan_id'] ?>]" value="<?= $value['lookupkeperawatan_id'] ?>" class="edukasi_kesehatan" <?= $checked ?>>
				</label> <?= $value['lookup_name'] ?>
			</div>
			<?php if($value['lookupkeperawatan_id'] != 31) : ?>
			<div class="col-md-3">
				<?=$form->field($modelDetail, 'pemberi_edukasi['.$value['lookupkeperawatan_id'].']', ['labelOptions' => ['class' => 'text-left']])->textInput([
					'placeholder' => $model->getAttributeLabel('pemberi_edukasi'), 
					'class' => 'form-control input-sm', 
					'id' => 'pemberi_edukasi_'.$value['lookupkeperawatan_id'],
				])->label(false); ?>
			</div>
			<div class="col-md-3">
    			<?=$form->field($modelDetail, 'tgl_edukasi['.$value['lookupkeperawatan_id'].']', [
                    'labelOptions' => ['class' => 'text-left'],
                    'addon' => ['append' => ['content' => '<i class="fa fa-calendar"></i>']],
                ])->textInput([
                    'class' => 'form-control input-sm date', 
                    'id' => 'tgl_edukasi_'.$value['lookupkeperawatan_id'],
                    'placeholder' => Yii::t('fe', $modelDetail->getAttributeLabel('tgl_edukasi'))
                ])->label(false); ?>
            </div>
            <div class="col-md-3">
    			<?=$form->field($modelDetail, 'ppa['.$value['lookupkeperawatan_id'].']', ['labelOptions' => ['class' => 'text-right']])->dropDownList($response['dokter_ruangan'], [
    				'prompt' => 'Pilih', 'class' => 'form-control select-ppa', 'style' => 'padding:9px!important;', 'id' => 'ppa_'.$value['lookupkeperawatan_id']
    			]); ?>
			</div>

			<?php else : ?>
			<?php if(!isset($modelDetail->pemberi_edukasi[31])) : ?>
			<div class="col-md-3">
				<?=$form->field($modelDetail, 'pemberi_edukasi[31]', ['labelOptions' => ['class' => 'text-left']])->textInput([
					'placeholder' => $model->getAttributeLabel('pemberi_edukasi'), 
					'class' => 'form-control input-sm', 
					'id' => 'pemberi_edukasi_'.$value['lookupkeperawatan_id'],
					// 'value' => $v,
				])->label(false); ?>
			</div>
			<div class="col-md-3">
    			<?=$form->field($modelDetail, 'tgl_edukasi[31]', [
                    'labelOptions' => ['class' => 'text-left'],
                    'addon' => ['append' => ['content' => '<i class="fa fa-calendar"></i>']],
                ])->textInput([
                    'class' => 'form-control input-sm date', 
                    'id' => 'tgl_edukasi_'.$value['lookupkeperawatan_id'],
                    // 'value' => $modelDetail->tgl_edukasi[31][0],
                    'placeholder' => Yii::t('fe', $modelDetail->getAttributeLabel('tgl_edukasi'))
                ])->label(false); ?>
            </div>
            <div class="col-md-3">
    			<?=$form->field($modelDetail, 'ppa[31]', ['labelOptions' => ['class' => 'text-right']])
    			->dropDownList($response['dokter_ruangan'], [
    				'prompt' => 'Pilih',
    				'class' => 'form-control select-ppa', 
    				'style' => 'padding:9px!important;',
    				'id' => 'ppa_'.$value['lookupkeperawatan_id'],
    				// 'options' => [ $modelDetail->ppa[31][0] => ['selected' => true]]
    			]); ?>
			</div>
			<div class="col-sm-1 ">
				<div class="row">
					<div class="col-md-1">
						<?= Html::button("<i class='fa fa-plus'> </i>", ['class' => 'btn btn-success btn-append']) ?>
					</div>
				</div>
			</div>
			<div class="row isi-loop">
				<div class="col-sm-12 clone-div hidden" style="margin-top: 5px">
	                <div class="col-md-2">
						<div class="form-group">
							<div class="col-sm-8">
							</div>
						</div>			
					</div>
	                <div class="col-md-3">
						<div class="form-group">
							<div class="col-sm-8">
								<?= Html::textInput('', '', ['class'=>'form-control', 'placeholder' => 'Penerima Edukasi', 
								'data-name' => 'lainnya[pemberi_edukasi][]'])?>
							</div>
						</div>			
					</div>
					
					<div class="col-md-3">
						<div class="form-group">
							<div class="col-sm-8">
								<?=Html::textInput('', '', ['class'=>'form-control date', 'placeholder' => 'Tanggal Edukasi', 
								'data-name' => 'lainnya[tgl_edukasi][]'])?>
							</div>
						</div>			
					</div>
					
					<div class="col-md-3">
						<div class="form-group field-ppa_31">
							<label class="text-right control-label col-sm-4">PPA</label>
							<div class="col-sm-8">
								<?=Html::dropDownList('', [], $response['dokter_ruangan'], ['class'=>'form-control select-ppa', 'prompt' => 'Pilih', 
								'data-name' => 'lainnya[ppa][]'])?>
							</div>
						</div>			
					</div>

	                <div class="col-md-1">
		                <div class="form-group">
			                <?=Html::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-sm btn-info btn-append'])?>
			                <?=Html::button('<i class="fa fa-trash"></i>', ['class'=>'btn btn-sm btn-danger btn-remove'])?>
		            	</div>
	            	</div>
	            </div>
        	</div>
			<?php elseif(isset($modelDetail->pemberi_edukasi[31])) : ?>
			<?php foreach ($modelDetail->pemberi_edukasi[31] as $k => $v) : ?>
			<?php if($k == 0) : ?>
			<div class="col-md-3">
				<?=$form->field($modelDetail, 'pemberi_edukasi[31]', ['labelOptions' => ['class' => 'text-left']])->textInput([
					'placeholder' => $model->getAttributeLabel('pemberi_edukasi'), 
					'class' => 'form-control input-sm', 
					'id' => 'pemberi_edukasi_'.$k,
					'value' => $v,
				])->label(false); ?>
			</div>
			<div class="col-md-3">
    			<?=$form->field($modelDetail, 'tgl_edukasi[31]', [
                    'labelOptions' => ['class' => 'text-left'],
                    'addon' => ['append' => ['content' => '<i class="fa fa-calendar"></i>']],
                ])->textInput([
                    'class' => 'form-control input-sm date', 
                    'id' => 'tgl_edukasi_'.$k,
                    'value' => $modelDetail->tgl_edukasi[31][0],
                    'placeholder' => Yii::t('fe', $modelDetail->getAttributeLabel('tgl_edukasi'))
                ])->label(false); ?>
            </div>
            <div class="col-md-3">
    			<?=$form->field($modelDetail, 'ppa[31]', ['labelOptions' => ['class' => 'text-right']])
    			->dropDownList($response['dokter_ruangan'], [
    				'prompt' => 'Pilih',
    				'class' => 'form-control select-ppa', 
    				'style' => 'padding:9px!important;',
    				'id' => 'ppa_'.$k,
    				'options' => [$modelDetail->ppa[31][0] => ['selected' => true]]
    			]); ?>
			</div>
			<div class="col-sm-1 ">
				<div class="row">
					<div class="col-md-1">
						<?= Html::button("<i class='fa fa-plus'> </i>", ['class' => 'btn btn-success btn-append']) ?>
					</div>
				</div>
			</div>
			<div class="row isi-loop">
				<div class="col-sm-12 clone-div hidden" style="margin-top: 5px">
	                <div class="col-md-2">
						<div class="form-group">
							<div class="col-sm-8">
							</div>
						</div>			
					</div>

	                <div class="col-md-3">
						<div class="form-group">
							<div class="col-sm-8">
								<?= Html::textInput('', '', ['class'=>'form-control', 'placeholder' => 'Penerima Edukasi', 
								'data-name' => 'lainnya[pemberi_edukasi][]'])?>
							</div>
						</div>			
					</div>
					
					<div class="col-md-3">
						<div class="form-group">
							<div class="col-sm-8">
								<?=Html::textInput('', '', ['class'=>'form-control date', 'placeholder' => 'Tanggal Edukasi', 
								'data-name' => 'lainnya[tgl_edukasi][]'])?>
							</div>
						</div>			
					</div>
					
					<div class="col-md-3">
						<div class="form-group field-ppa_31">
							<label class="text-right control-label col-sm-4">PPA</label>
							<div class="col-sm-8">
								<?=Html::dropDownList('', [], $response['dokter_ruangan'], ['class'=>'form-control select-ppa', 'prompt' => 'Pilih', 
								'data-name' => 'lainnya[ppa][]'])?>
							</div>
						</div>			
					</div>

	                <div class="col-md-1">
		                <div class="form-group">
			                <?=Html::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-sm btn-info btn-append'])?>
			                <?=Html::button('<i class="fa fa-trash"></i>', ['class'=>'btn btn-sm btn-danger btn-remove'])?>
		            	</div>
	            	</div>
	            </div>
        	</div>
        	<?php else : ?>
        	<div class="row">
				<div class="col-sm-12">
	                <div class="col-md-2">
						<div class="form-group">
							<div class="col-sm-8">
							</div>
						</div>			
					</div>
	                <div class="col-md-3">
						<div class="form-group">
							<div class="col-sm-8">
								<?= Html::textInput('lainnya[pemberi_edukasi][]', $v, [
									'class'=>'form-control', 
									'placeholder' => 'Penerima Edukasi', 
									'data-name' => 'lainnya[pemberi_edukasi][]'
								])?>
							</div>
						</div>			
					</div>
					
					<div class="col-md-3">
						<div class="form-group">
							<div class="col-sm-8">
								<?=Html::textInput('lainnya[tgl_edukasi][]', $modelDetail->tgl_edukasi[31][$k], [
									'class'=>'form-control date', 
									'placeholder' => 'Tanggal Edukasi', 
									'data-name' => 'lainnya[tgl_edukasi][]'
								])?>
							</div>
						</div>			
					</div>
					
					<div class="col-md-3">
						<div class="form-group field-ppa_31">
							<label class="text-right control-label col-sm-4">PPA</label>
							<div class="col-sm-8">
								<?=Html::dropDownList('lainnya[ppa][]', $modelDetail->ppa[31][$k], $response['dokter_ruangan'], [
									'class'=>'form-control select-ppa', 
									'prompt' => 'Pilih', 
									'data-name' => 'lainnya[ppa][]',
									'style' => 'padding:9px!important;',
								])?>
							</div>
						</div>			
					</div>

	                <div class="col-md-1">
		                <div class="form-group">
			                <?=Html::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-sm btn-info btn-append'])?>
			                <?=Html::button('<i class="fa fa-trash"></i>', ['class'=>'btn btn-sm btn-danger btn-remove'])?>
		            	</div>
	            	</div>
	            </div>
        	</div>
			<?php endif; ?>
			<?php endforeach ?>
			<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
		
	</div><br><br><hr>
	<div class="row">
		<div class="col-sm-12">
			<?= Html::submitButton("<i class='fa fa-pencil'> Edit</i>", ['class' => 'btn btn-dark-turquise']) ?>
			<?= Html::button("<i class='fa fa-trash'> Hapus</i>", ['class' => 'btn btn-danger data-delete']) ?>
			<?= Html::button("<i class='fa fa-repeat'> Ulang</i>", ['class' => 'btn btn-aqua reset']) ?>
			<?= Html::a('<i class="fa fa-file-pdf-o"></i> '. Yii::t('fe', 'Cetak'), 
                    ['/ranap/dashboard/print-pdf?pasienadmisi_id='.$model->pasienadmisi_id], 
                    [
                        'class' => 'btn btn-crimson data-pdf',
                        'title' => Yii::t('fe', 'Cetak'),
                        'data-tooltip' => 'tooltip'
                    ]
                );
            ?>
		</div>
	</div>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs('
    var rencanapulang_id = "'.$model->rencanapulang_id.'";
    $(".btn-append").prop("disabled", true);

    $(".date").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });
	
	$("#form").docoForm("submit",{
        success : function(data) {
            window.location.reload();
        }
    });
	
	$(".reset").on("click", function(){
		resetForm($("#form"));
	});

    function resetForm($form) {
	    $form.find("input:text, input:password, input:file, select, textarea").val("");
	    $form.find("input:radio")
	         .removeAttr("checked").removeAttr("selected");
	    $(".select2").val(null).trigger("change");
	}

	$(document).on("click", ".data-delete", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            additional: "data-rm",
            url: "/ranap/dashboard/delete?id=" + rencanapulang_id,
            success : function (data) {
                window.location.reload();
            }
        });
        return false;
    });

    $("#edukasi_kesehatan_31").on("change", function(){
		if ($(this).is(":checked")) {
			$(".btn-append").prop("disabled", false);
		}
		else {
			$(".btn-append").prop("disabled", true);
		}
		
    });

    var count = 0;
	$(document).on("click",".btn-append", function(){
	    count++
	    var _clone = $(".clone-div").clone()
	                                .addClass("row-"+count)
	                                .removeClass("clone-div hidden")
	    _clone.find("input, select").each(function(k,v){
	        if($(this).hasClass("select-ppa") ){
	            $(this).addClass("select-ppa"+count)
	            setTimeout(function(){ $(".select-ppa"+count).select2(); }, 1)
	            
	        }
	        $(this).attr("name", $(this).attr("data-name"))
	    });
	    _clone.find(".btn-remove").attr("data-key", "row-"+count)

	    $(".isi-loop").append(_clone);
	    $(".date").pickadate({
	        applyClass: "bg-slate-600",
	        cancelClass: "btn-default",
	        locale: {
	            format: "DD-MMMM-YYYY"
	        }
	    });
	});

	$(document).on("click", ".btn-remove", function(){
	    $( "."+$(this).attr("data-key")).remove()
	})
');
?>