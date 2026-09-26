<?php
// Author : Ardi Pratama
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Tambah Instruksi') ?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'custom-back' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Kembali'),
                'icon' => 'fa fa-arrow-left',
                'attributes' => [
                    'id' => 'btn-back-terapi',
                    'data-options' => 'click'
                ],
            ]
        ]) ?>
    </div>
 
    <div class="panel-body">
        <div class="row">
            <div class="panel panel-flat">
            	<div class="panel-body">
            		<div class="row">
            			<div class="col-lg-6">
	            			<div class="form-group">
	            				<label class="control-label col-md-6"><?=Yii::t('fe', 'Jenis')?></label>
	            				<div class="col-md-6">
	            					<?= Select2::widget([
										'name' => 'jenis_terapi',
										'data' => ArrayHelper::map($data_jenisinstruksi, 'lookup_id', 'lookup_name'),
										'options' => [
											'id' => 'jenis_terapi',
											'class' => 'form-control input-sm',
											'placeholder' => Yii::t('fe', '-- Pilih --')
										],
									]) ?>
									<?= Html::hiddenInput('jenis_instruksi_flag', '', ['id' => 'jenis_instruksi_flag']) ?>
			            		</div>
		            		</div>
	            			<div class="form-group">
	            				<label class="control-label col-md-6"><?=Yii::t('fe', 'Catatan')?></label>
	            				<div class="col-md-6">
        							<?= Html::activeTextarea($modelInstruksi, 'catatan_instruksi') ?>
			            			<?= Html::textArea('catatan_terapi', '', ['class' => 'form-control', 'id' => 'catatan_terapi']) ?>
			            		</div>
		            		</div>
		            	</div>
            		</div>
            	</div>
            </div>
        </div>
        <div id="cppt-terapi-tindakan" class="row" hidden>
            <?= Yii::$app->controller->renderPartial('cppt/terapi_tindakan', [
                'id_ruangan' => $id_ruangan,
                'id_instalasi' => $id_instalasi,
                'model' => $model,
                'listRuangan' => $listRuangan,
                'listDiagnosa' => $listDiagnosa,
                'pegawai' => $pegawai,
                'modelInstruksi' => $modelInstruksi,
                'modelInstruksiTindakan' => $modelInstruksiTindakan,
                'modelBmhp' => $modelBmhp,
                'modelTindakanKomponen' => $modelTindakanKomponen,
                'modelTindakanPelayanan' => $modelTindakanPelayanan,
                'data_pasien' => $data_pasien,
                'data_dokter' => $data_dokter,
                'data_perawat' => $data_perawat,
                'data_tindakanruangan' => $data_tindakanruangan,
                'data_paket' => $data_paket,
                'data_obatalkes' => $data_obatalkes,
                'data_satuantindakan' => $data_satuantindakan,
                'konfig' => $konfig,
                'data_jenisinstruksi' => $data_jenisinstruksi,
                'count_riwayat' => $count_riwayat,
                'data_tindakanbmhp' => $data_tindakanbmhp
            ]) ?>
        </div>
        <div id="cppt-terapi-reseptur" class="row" hidden>
            <?= Yii::$app->controller->renderPartial('cppt/terapi_reseptur', [
                'model' => $model,
                'modelInstruksi' => $modelInstruksi,
                'modelReseptur' => $modelReseptur,
                'modelResepturDetailRacikan' => $modelResepturDetailRacikan,
                'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
                'listDiagnosa' => $listDiagnosa,
                'listDataSigna' => $listDataSigna,
                'listDataApotek' => $listDataApotek,
                'pegawai' => $pegawai,
                'data_pasien' => $data_pasien,
            ]) ?>
        </div>
        <div id="cppt-terapi-penunjang" class="row" hidden>
            <?= Yii::$app->controller->renderPartial('cppt/terapi_penunjang', [
                'modelPenunjang' => $modelPenunjang,
                'listInstalasiPenunjang' => $listInstalasiPenunjang,
                'data_pasien' => $data_pasien,
                'pegawai' => $pegawai,
            ]) ?>
        </div>
        <div id="cppt-terapi-biasa" class="row" hidden>

        </div>
    </div>
</div>

<?php // File
$this->registerJs($this->render('js/terapi.js'), View::POS_END);
?>