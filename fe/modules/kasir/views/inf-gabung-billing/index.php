<?php

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
						<h3 class="panel-title"><b><?= Yii::t('fe', $title); ?></b></h3>
						<?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
					</div>
				</div>
			</div>
			<div class="panel-toolbar clearfix">
				<?php
					$defaultBtn = [
						'search' => [
							'attributes' => [
								 'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
								 'data-table-id' => 'example',
								 'data-options' => 'click',
							]
					  	],
					  'reset' => [
							'attributes' => [
								 'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
								 'data-table-id' => 'example',
								 'data-options' => 'click',
							]
					  	],
						'tambah-gabung' => [
							'title' => \Yii::t('fe', 'Gabung Tagihan'),
							'icon' => 'fa fa-plus',
							'attributes' => [
								 'id' => 'tambah-gabung',
								 'data-popup'=>'tooltip',
								 'data-toggle'=>'modal',
								 'data-target'=>'#modal_backdrop',
								 'action' => '/kasir/inf-gabung-billing/tambah',
								 'data-width' => "85%",
							]
					   ],
						'detail-gabung' => [
							'title' => \Yii::t('fe', 'Detail Tagihan'),
							'icon' => 'fa fa-eye',
							'attributes' => [
								'id' => 'detail-gabung',
								'data-popup'=>'tooltip',
								'data-toggle'=>'modal',
								'data-target'=>'#modal_backdrop',
								'data-width' => "85%",
							]
						],
						'batal-gabung' =>[
							'type' => 'button',
							'title' => \Yii::t('fe', 'Batal'),
							'icon' => 'fa fa-close',
							'method' => '#',
							'attributes' => [
								'class'=>'data-batal-gabung',
								'data-options'=>'modal',
								'data-target' => '#modal_backdrop',
								'data-width' => '50%',
								'data-additional' => 'data-rm',
								'data-url' => '/kasir/inf-gabung-billing/batal?id=',
								'data-conditions'=>'gabungpelayanandetail_id'
							]
					  	],
						'rincian' => [
							'title' => 'Cetak Rincian',
							'icon' => 'fa fa-file-pdf-o',
							'method' => 'not-exist',
							'attributes' => [
								 'id'=>'cetak-rincian',
								 'data-options' => 'link',
								 'class'=>'spa',
								 'target'=>'_blank'
							]
					  	],
						'detail-rincian' => $btnDetailRincian,
						'invoice' => $btnInvoice,
						'detail-invoice' => $btnDetailInvoice,
						'cetak-kwitansi'=>[
							'type'=>'button',
							'title' => \Yii::t('fe', 'Cetak Kwitansi'),
							'icon' => 'fa fa-print',
							'method' => 'not-exist',
							'attributes' => [
								 'id' => 'cetak-kwitansi',
								 'data-options'=>'modal',
								 'data-target' => '#modal_backdrop',
								 'data-width' => '50%',
								 'data-url' => '/kasir/inf-gabung-billing/cetak-kwitansi?id=',
								 'data-conditions'=>'pembayaran_id'
							]
					  	],
					];
				?>
				<?=DocoHelpers::generateToolbar($defaultBtn,'#example');?>
			</div>

			<div class="panel-body">
				<div class="table-wrapper table-scroll-x">
					<table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
						<thead>
							<tr class="bg-inverse">
								<th width="1"></th>
								<th width="1">No</th>
								<th><?=\Yii::t("fe", "No Pendaftaran");?></th>
								<th><?=\Yii::t("fe", "Tanggal Gabung Tagihan");?></th>
								<th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
								<th><?=\Yii::t("fe", "Pasien - No. RM");?></th>
								<th><?=\Yii::t("fe", "Instalasi - Ruangan");?></th>
								<th><?=\Yii::t("fe", "Cara Bayar - Penjamin");?></th>
								<th><?=\Yii::t("fe", "No SEP");?></th>
								<th><?=\Yii::t("fe", "Status Bayar");?></th>
								<th><?=\Yii::t("fe", "Total Tagihan");?></th>
								<th><?=\Yii::t("fe", "Ref No Pendaftaran");?></th>
								<th><?=\Yii::t("fe", "Ref Penjamin");?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$this->registerJs('
var status_lunas = '.DocoConstants::STAT_BAYAR_LUNAS.';
var cetakInvoice = "/kasir/pembayaran-tagihan/cetak-invoice?id=";
var cetakRincian = "/kasir/inf-pasien-belum-bayar/cetak-rincian?id=";
var cetakDetailRincian = "/kasir/inf-pasien-belum-bayar/show-popup?id=";
var cetakDetailRincianReport = "/kasir/inf-pasien-belum-bayar/show-popup-detail-designer?id=";
',View::POS_END,'b-index');
$this->registerJs($this->render('js/index.js'),View::POS_END);
?>
