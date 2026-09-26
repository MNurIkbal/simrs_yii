<?php
/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-04 09:35:29
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-19 09:52:32
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Nilai Rujukan Pemeriksaan Laboratorium');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['/master']];
$this->params['breadcrumbs'][] = $title;
?>

<?php
	$form = ActiveForm::begin([
		'id' => 'ajax-form',
		'type' => ActiveForm::TYPE_HORIZONTAL,
		'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
	]);
?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-white">
			<div class="panel-heading">
				<div class="row">
					<div class="column-1">
						<img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
					</div>
					<div class="column-2">
						<h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
						<?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
					</div>
				</div>
			</div> 
			<div class="panel-toolbar clearfix">
				<?=DocoHelpers::generateToolbar([
					'back',
					'edit' => [
						'attributes' => [
							'data-toggle' => 'modal',
							'data-target' => '#modal_backdrop',
							'action' => '/master/nilai-rujukan/update-hasil?id='.$id,
						]
					],
					'delete' => [
						'attributes' => [
							'data-additional' => 'data-rm',
							'data-target' => '/master/nilai-rujukan/delete-hasil?id='.$id,
							'class' => 'btn btn-info btn-labeled btn-xs data-delete',
							'data-id' => $id,
						]
					]
				]);?>
			</div>
			<div class="panel-body">
				<div class="row">
					<table class="table table-bordered table-condensed table-striped table-hover">
						<tr>
							<td><?=\Yii::t('fe', 'Nama Pemeriksaan');?></td>
							<td>
								<?=$attributes['detail']['nama_pemeriksaan'];?>
							</td>
						</tr>
						<tr>
							<td><?=\Yii::t('fe', 'Tambah Hasil');?></td>
							<td>
								<?=DocoHelpers::generateToolbar([
									'add' => [
										'icon' => 'fa fa-plus',
										'title' => '',
										'attributes' => [
											'data-toggle' => 'modal',
											'data-target' => '#modal_backdrop',
											'action' => '/master/nilai-rujukan/create?id='.$attributes['detail']['pemeriksaanlab_id'],
											'class' => 'btn bg-teal btn-sm',
										]
									],
								]);?>
							</td>
						</tr>
					</table>
				</div><br>
				<div class="row">
					<table class="table table-striped table-condensed table-hover" style="width:100%;display:none;">
						<thead>
							<tr class="bg-inverse">
									<th><?=\Yii::t("fe", "Jenis Kelamin");?></th>                                  
									<th><?=\Yii::t("fe", "Golongan Usia");?></th>
									<th><?=\Yii::t("fe", "Nilai Rujukan Min");?></th>
									<th><?=\Yii::t("fe", "Nilai Rujukan Max");?></th>
									<th><?=\Yii::t("fe", "Satuan");?></th>
									<th><?=\Yii::t("fe", "Nilai Kritis Min");?></th>
									<th><?=\Yii::t("fe", "Nilai Kritis Max");?></th>
									<th><?=\Yii::t("fe", "Keterangan");?></th>
							</tr>
						</thead>
						<tbody>
							<?php $labelJK = null; ?>
							<?php foreach ($jk as $keyJk => $valueJk) : ?>
									<?php $n = 0; ?>
									<?php foreach ($valueJk['data'] as $k => $value) : ?>
										<tr>
											<td>
													<?= Html::hiddenInput('NilaiRujukanForm[jenis_kelamin]', $valueJk['id']) ?>
													<?= $n == 0 ? $valueJk['label'] : null ?></td>
											<td><?= $value ?></td>
										<td><?= $form->field($model, 'parent[nilai_min]['.$keyJk.']['.$k.']')->textInput([
											'class' => 'form-control input-xs parent first-col', 
											'data-k-id' => $keyJk,
											'data-g-id' => $k,
											'data-field' => 'nilai_min',
											'value' => isset($parent[$valueJk['id']][$k]['nilai_min']) ? $parent[$valueJk['id']][$k]['nilai_min'] : null])
										->label(false); ?></td>
										<td><?= $form->field($model, 'parent[nilai_max]['.$keyJk.']['.$k.']')->textInput(['class' => 'form-control input-xs parent', 
											'data-k-id' => $keyJk,
											'data-g-id' => $k,
											'data-field' => 'nilai_max',
											'value' => isset($parent[$valueJk['id']][$k]['nilai_max']) ? $parent[$valueJk['id']][$k]['nilai_max'] : null])
										->label(false); ?></td>
										<td><?= $form->field($model, 'parent[satuan_hasillab]['.$keyJk.']['.$k.']')->textInput(['class' => 'form-control input-xs parent', 
											'data-k-id' => $keyJk,
											'data-g-id' => $k,
											'data-field' => 'satuan_hasillab',
											'value' => isset($parent[$valueJk['id']][$k]['satuan_hasillab']) ? $parent[$valueJk['id']][$k]['satuan_hasillab'] : null])
										->label(false); ?></td>
										<td><?= $form->field($model, 'parent[nilaikritis_min]['.$keyJk.']['.$k.']')->textInput(['class' => 'form-control input-xs parent', 
											'data-k-id' => $keyJk,
											'data-g-id' => $k,
											'data-field' => 'nilaikritis_min',
											'value' => isset($parent[$valueJk['id']][$k]['nilaikritis_min']) ? $parent[$valueJk['id']][$k]['nilaikritis_min'] : null])
										->label(false); ?></td>
										<td><?= $form->field($model, 'parent[nilaikritis_max]['.$keyJk.']['.$k.']')->textInput(['class' => 'form-control input-xs parent', 
											'data-k-id' => $keyJk,
											'data-g-id' => $k,
											'data-field' => 'nilaikritis_max',
											'value' => isset($parent[$valueJk['id']][$k]['nilaikritis_max']) ? $parent[$valueJk['id']][$k]['nilaikritis_max'] : null])
										->label(false); ?></td>
										<td><?= $form->field($model, 'parent[keterangan]['.$keyJk.']['.$k.']')->textInput(['class' => 'form-control input-xs parent', 
											'data-k-id' => $keyJk,
											'data-g-id' => $k,
											'data-field' => 'keterangan',
											'value' => isset($parent[$valueJk['id']][$k]['keterangan']) ? $parent[$valueJk['id']][$k]['keterangan'] : null])
										->label(false); ?></td>
										</tr>
										<?php $n++; ?>
									<?php endforeach; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="clear"><br></div>
				</div>
				<div class="row">
					<?php foreach ($attributes['nilai_rujukan'] as $keyRujukan => $valueRujukan) : ?>
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-white panel-collapsed">
								<div class="panel-heading">
									<h3 class="panel-title"><?= Html::radio('nama_rujukan', false, [
										'value' => DocoHelpers::encrypt($keyRujukan), 
										'class' => 'nama_rujukan',
									]) ?> <strong style="font-size: 14px;"><?=Yii::t('fe','Nama Hasil : ')?> </strong> 
									<strong style="font-size: 14px;"><?=$keyRujukan;?></strong>  </h3>
									<div class="heading-elements">
										<ul class="icons-list">
												<li><a data-action="collapse"></a></li>
										</ul>
									</div>
								</div>
								<div class="panel-body">
									<table class="table table-striped table-condensed table-hover" style="width:100%">
										<thead>
											<tr class="bg-inverse">
												<th width="10%"><?=\Yii::t("fe", "Jenis Kelamin");?></th>                                  
												<th width="15%"><?=\Yii::t("fe", "Golongan Usia");?></th>
												<th width="10%"><?=\Yii::t("fe", "Nilai Rujukan Min");?></th>
												<th width="10%"><?=\Yii::t("fe", "Nilai Rujukan Max");?></th>
												<th width="10%"><?=\Yii::t("fe", "Satuan");?></th>
												<th width="10%"><?=\Yii::t("fe", "Nilai Kritis Min");?></th>
												<th width="10%"><?=\Yii::t("fe", "Nilai Kritis Max");?></th>
												<th width="10%"><?=\Yii::t("fe", "Keterangan");?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											$labelJk = '';
											$show = false;
											$arrJk = [];
											foreach ($valueRujukan as $val) :
												foreach ($val as $row) :
													if ($labelJk != $row['jenis_kelamin']) {
														$labelJk = $row['jenis_kelamin'];
														$show = true;
													} else {
														$show = false;
													}
													if (!isset($golongan_umur[$row['golonganumur_id']])) {
														continue;
													}
													?>
													<tr>
														<td style="font-weight:bold;"><?= $show ? (isset($jk[$row['jenis_kelamin']]['label']) ? strtoupper($jk[$row['jenis_kelamin']]['label']) : null) : '' ?></td>
														<td><?= isset($golongan_umur[$row['golonganumur_id']]) ? $golongan_umur[$row['golonganumur_id']] : '' ?></td>
														<td>
															<?= $form->field($model, 'nilai_min['.$row['nilairujukan_id'].']['.$row['jenis_kelamin'].']['.$row['golonganumur_id'].']')->textInput([
																'class' => 'form-control input-xs hasil', 
																'data-id' => $row['nilairujukan_id'].'-'.$row['jenis_kelamin'].'-'.$row['golonganumur_id'],
																'data-field' => 'nilai_min',
																'value' => $row['nilai_min']
															])->label(false); ?>
														</td>
														<td><?= $form->field($model, 'nilai_max['.$row['nilairujukan_id'].']['.$row['jenis_kelamin'].']['.$row['golonganumur_id'].']')->textInput([
																'class' => 'form-control input-xs hasil', 
																'data-id' => $row['nilairujukan_id'].'-'.$row['jenis_kelamin'].'-'.$row['golonganumur_id'],
																'data-field' => 'nilai_max',
																'value' => $row['nilai_max']
															])->label(false); ?>
														</td>
														<td><?= $form->field($model, 'satuan_hasillab['.$row['nilairujukan_id'].']['.$row['jenis_kelamin'].']['.$row['golonganumur_id'].']')->textInput([
																'class' => 'form-control input-xs hasil', 
																'data-id' => $row['nilairujukan_id'].'-'.$row['jenis_kelamin'].'-'.$row['golonganumur_id'],
																'data-field' => 'satuan_hasillab',
																'value' => $row['satuan_hasillab']
															])->label(false); ?>
														</td>
														<td><?= $form->field($model, 'nilaikritis_min['.$row['nilairujukan_id'].']['.$row['jenis_kelamin'].']['.$row['golonganumur_id'].']')->textInput([
																'class' => 'form-control input-xs hasil', 
																'data-id' => $row['nilairujukan_id'].'-'.$row['jenis_kelamin'].'-'.$row['golonganumur_id'],
																'data-field' => 'nilaikritis_min',
																'value' => $row['nilaikritis_min']
															])->label(false); ?>
														</td>
														<td><?= $form->field($model, 'nilaikritis_max['.$row['nilairujukan_id'].']['.$row['jenis_kelamin'].']['.$row['golonganumur_id'].']')->textInput([
																'class' => 'form-control input-xs hasil', 
																'data-id' => $row['nilairujukan_id'].'-'.$row['jenis_kelamin'].'-'.$row['golonganumur_id'],
																'data-field' => 'nilaikritis_max',
																'value' => $row['nilaikritis_max']
															])->label(false); ?>
														</td>
														<td><?= $form->field($model, 'keterangan['.$row['nilairujukan_id'].']['.$row['jenis_kelamin'].']['.$row['golonganumur_id'].']')->textInput([
																'class' => 'form-control input-xs hasil', 
																'data-id' => $row['nilairujukan_id'].'-'.$row['jenis_kelamin'].'-'.$row['golonganumur_id'],
																'data-field' => 'keterangan',
																'value' => $row['keterangan']
															])->label(false); ?>
														</td>
													</tr>
												<?php endforeach; ?>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
					<?= Html::hiddenInput('NilaiRujukanForm[pemeriksaanlab_id]', $attributes['detail']['pemeriksaanlab_id']);?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php ActiveForm::end(); ?>
<?php 
	$this->registerJs("
		var dataId = ".$id.";
		$(document).ready(function () {
			_check();
		});
		$(document).on('click','#btn-simpan-custom', function(e){
			e.preventDefault();
			$('#ajax-form').submit();
		});
		
		$('.data-edit').prop('disabled', true);
		$('.data-delete').prop('disabled', true);
		$('#ajax-form').docoForm('submit',{
			success : function(data) {
				window.location.reload();
			}
		});

		var _check = function () {
			$.each($('.first-col'),function () {
				var _value = $(this).val();
				if (_value == '') {
					
				}
			});
		}

		var action = $('.data-edit').attr('action');
		var action_delete = $('.data-delete').attr('action');

		$(document).on('click', '.nama_rujukan', function(e) {
			var val = $('.nama_rujukan:checked').val();
			$('.data-edit').attr('action',action+'&nama='+val);
			$('.data-delete').attr('action',action+'&nama='+val);
			$('.data-edit').prop('disabled', false);
			$('.data-delete').prop('disabled', false);
		});

		$(document).on('click', '.data-delete', function(e) {
			var val = $('.nama_rujukan:checked').val();
			e.preventDefault();
			$(this).docoForm('delete',{
				additional: 'data-rm',
				url: baseUrl+'master/nilai-rujukan/delete-hasil?id='+dataId+'&nama='+val,
				success : function (data) {
					window.location.reload();
				}
			});
			return false;
		});

		$('.hasil').on('change', function() {
			var id = $(this).attr('data-id');
			var field = $(this).attr('data-field');
			var val = $(this).val();
			$().docoForm('click',{
				url: baseUrl+'master/nilai-rujukan/update-form',
				data: {id:id,value:val,field:field},
				skipConfirm: true,
			})
		});

		$('.parent').on('change', function() {
			var k_id = $(this).attr('data-k-id');
			var g_id = $(this).attr('data-g-id');
			var field = $(this).attr('data-field');
			var val = $(this).val();
			$().docoForm('click',{
				url: baseUrl+'master/nilai-rujukan/update-parent?id=' + dataId,
				data: {
					k_id:k_id,
					g_id:g_id,
					value:val,
					field:field
				},
				skipConfirm: true,
			})
		});
		$('.data-reset').on('click', function(){
			$('#ajax-form')[0].reset();
		});
		", View::POS_END, 'js-kuning'
	);
?>