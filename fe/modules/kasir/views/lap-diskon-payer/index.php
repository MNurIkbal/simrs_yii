<?php

/****
 * * @author: Budi
 * * @email: budi@sirs.co.id 
 * ? A product of PT. Docotel Teknologi
 * ! Powered by Sirs
 */

use yii\web\View;
use app\components\DHtml;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
	.flex-1 {
		display: none;
	}
</style>
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
						<h3 class="panel-title"><b><?= Yii::t('fe', $this->title); ?></b></h3>
						<?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
					'excel',
				]); ?>
			</div>
			<div class="panel-body">
				<div class="table-wrapper table-scroll-x">	
					<table id="example" class="table datatable-basic table-striped table-hover" style="width:100%">
						<thead>
							<tr class="bg-inverse">
								<th>No</th>
								<th><?= \Yii::t("fe", "Bill No"); ?></th>
								<th><?= \Yii::t("fe", "Discount Date"); ?></th>
								<th><?= \Yii::t("fe", "IP No."); ?></th>
								<th><?= \Yii::t("fe", "Patient Name"); ?></th>
								<th><?= \Yii::t("fe", "Authorized By"); ?></th>
								<th><?= \Yii::t("fe", "Bill Amt."); ?></th>
								<th><?= \Yii::t("fe", "Discount"); ?></th>
								<th><?= \Yii::t("fe", "Username"); ?></th>
								<th><?= \Yii::t("fe", "Remarks"); ?></th>
								<th><?= \Yii::t("fe", "Discharge Date"); ?></th>
								<th><?= \Yii::t("fe", "Discount Type"); ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$this->registerJs('
const caraBayar = ' . $caraBayar . ';
', View::POS_END, 'b-index');
$this->registerJs($this->render('index.js'), View::POS_END); ?>