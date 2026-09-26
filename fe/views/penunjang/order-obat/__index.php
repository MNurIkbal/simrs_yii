<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$id = DocoHelpers::encrypt($id);
$pelayananId = DocoHelpers::encrypt($pelayananId);
$tindakanId = DocoHelpers::encrypt($tindakanId);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Tindakan dan Pemakaian Obat / Alkes Non - Reseptur'), 'url' => ['index']];
?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="column-1">
						<img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
					</div>
					<div class="column-2">
						<h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
						<?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
					</div>
				</div>
			</div>
			<div class="panel-toolbar clearfix">
				<?php 
				if($isLab){
					echo DocoHelpers::generateToolbar([
						'hasil' => [
							'title' => \Yii::t('fe', 'Kembali'),
							'icon' => 'fa fa-arrow-left',
							'attributes' => [
								'class' => 'btn btn-info btn-labeled btn-xs spa',
								'data-options' => 'click',
								'data-render' => 'hasil-lab?id=' . $id,
								'data-tab' => 'tab-non-rujukan',
								'data-target' => '#view-non-rujukan',
								'id' => 'btn-hasil'
							]
				  ]]);
				}else{
					echo Html::a('<b><i class="fa fa-arrow-left"></i></b>' . \Yii::t('fe', 'Kembali'), [
						$endPoint . $urlHasil .'/index?id='.$id.'&pelayananId='.$pelayananId.'&tindakanId='.$tindakanId
					], [
						'class' => 'btn btn-labeled btn-xs btn-info',
					]);

				}
				?>
			</div>
			<div class="panel-body">
				<?= Yii::$app->controller->renderpartial('//penunjang/order-obat/__tindakan', [
					'id' => $id,
					'pelayananId' => $pelayananId,
					'tindakanId' => $tindakanId,
					'model' => $model,
					'listInfo' => $bundleData['listInfo'],
					'list_pemeriksaan' => $bundleData['list_pemeriksaan'],
					'list_pegawai' => $bundleData['list_pegawai'],
					'status_periksa' => $bundleData['status_periksa'],
					'pendaftaran_id' => $pendaftaran_id,
					'endPoint' => $endPoint,
				]) ?>
				
				<?= Yii::$app->controller->renderpartial('//penunjang/order-obat/__tindakan_obat', [
					'id' => $id,
					'pelayananId' => $pelayananId,
					'tindakanId' => $tindakanId,
					'modelObat' => $modelObat,
					'listInfo' => $bundleData['listInfo'],
					'list_pemeriksaan' => $bundleData['list_pemeriksaan'],
					'list_pegawai' => $bundleData['list_pegawai'],
					'status_periksa' => $bundleData['status_periksa'],
					'pendaftaran_id' => $pendaftaran_id,
					'endPoint' => $endPoint,
					'arrayTindakan' => $arrayTindakan,
					'optionTindakanParent' => $optionTindakanParent,
					'instalasiPenunjang' => $instalasiPenunjang
				]) ?>
			</div>
		</div>
	</div>
</div>

<?php
$this->registerJs('
	var _statusSelesai = "'.DocoConstants::ST_SELESAI_PNNJG.'";
	var _status = "'.$status_periksa.'";
	var _id = "'.DocoHelpers::decrypt($id).'";
	var _pendaftaran_id = "'.$pendaftaran_id.'";
	var _kelaspelayanan_id = "'.$listInfo['kelaspelayanan_id'].'";
	var _penjamin_id = "'.$listInfo['penjamin_id'].'";
	var _pelayananId = "'.DocoHelpers::decrypt($pelayananId).'";
	var _endPoint = "'.$endPoint.'";
	var _ruangan_id = "'.$ruangan_id.'";
	var _pendaftaran_id = "'.$pendaftaran_id.'";
	var _arrayTindakan = '.json_encode($arrayTindakan).';
    var instalasiId = "'.ArrayHelper::getValue($listInfo, 'instalasiasal_id').'";
	var instalasiPenunjang = "'.json_encode($instalasiPenunjang).'";

	if (instalasiPenunjang.includes(instalasiId)) {
		instalasiId = 1;
	}
', View::POS_END, 'b-index');
?>
