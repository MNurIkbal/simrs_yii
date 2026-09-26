<?php

/**
 * @Author: Sigit
 * @Date:   2018-06-06 09:24:42
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-06-06 16:35:05
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = Yii::t('fe', 'Supplier');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-white">
			<div class="panel-heading">
				<!-- breadcrumbs replace with this -->
				<div class="row">
						<div class="column-1">
								<img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
						</div>
						<div class="column-2">
								<h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
								<?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
						</div>
				</div>
				<!-- end -->
				<div class="heading-elements">
					<ul class="icons-list">
						<li><a data-action="collapse"></a></li>
					</ul>
				</div>
			</div>
			<div class="panel-toolbar clearfix">
				<?= DocoHelpers::generateToolbar([
					'search',
					'reset',
					'add',
					'edit' => [
						'attributes' => [
							'id' => 'btn-edit',
							'disabled' => 'disabled'
						]
					],
					'delete' => [
						'attributes' => [
							'id' => 'btn-delete',
							'data-additional' => 'data-supplier',
							'disabled' => 'disabled'
						]
					],
					'excel',
					'pdf' => [
						'attributes' => [
							'id' => 'btn-pdf',
							'class' => 'hidden'
						]
					],
				], '#tb-supplier') ?>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12 filter-form"></div>
				</div>
				<br>
				<table id="tb-supplier" class="table table-striped table-condensed table-hover" style="width:100%">
					<thead>
						<tr class="bg-inverse">
							<th width="5%"></th>
							<th><?= Yii::t('fe', 'No') ?></th>
							<th><?= Yii::t("fe", "Kode SUpplier") ?></th>
							<th><?= Yii::t("fe", "Nama Supplier") ?></th>
							<th><?= Yii::t("fe", "Alamat") ?></th>
							<th><?= Yii::t("fe", "No Telephone") ?></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php
$this->registerJs('
	// Global vars
	var no = "'.(\Yii::t("fe", "No.")).'";
	var kodeSupplier = "'.(\Yii::t("fe", "Kode Supplier")).'";
	var namaSupplier = "'.(\Yii::t("fe", "Nama Supplier")).'";
	var alamat = "'.(\Yii::t("fe", "Alamat")).'";
	var noTelphone = "'.(\Yii::t("fe", "No Telepon")).'";
	var updateUrl = "/master/supplier/update?id=";

	// Datatable language
	var emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
	var info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
	var infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
	var infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
	var lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
	var loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
	var processing = "'.(\Yii::t("fe", "Memproses...")).'";
	var search = "'.(\Yii::t("fe", "Cari:")).'";
	var zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
	var sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
	var sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")).'";
', View::POS_END, 'b-index');

// Register js file
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>