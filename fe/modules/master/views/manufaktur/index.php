<?php

/**
 * @Author: Muhamad Lukman Hakim
 * @Date:   2021-01-22 11:20:42
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = Yii::t('fe', 'Manufaktur');
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
							'data-additional' => 'data-manufaktur',
							'disabled' => 'disabled'
						]
					]
				], '#tb-manufaktur') ?>
			</div>
			<div class="panel-body">
                <div class="filter-form"></div>
                <table id="tb-manufaktur" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                        	<th width="5%"></th>
                            <th><?= Yii::t('fe', 'No') ?></th>
							<th><?= Yii::t("fe", "Kode Manufaktur") ?></th>
							<th><?= Yii::t("fe", "Nama Manufaktur") ?></th>
							<th><?= Yii::t("fe", "No Kontak") ?></th>
							<th><?= Yii::t("fe", "Alamat") ?></th>
							<th><?= Yii::t("fe", "Status Aktif") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>

<?php
$this->registerJs('
    // Global Var
    var table;
    var updateUrl = "/master/manufaktur/update?id=";

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#tb-manufaktur").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[1, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/manufaktur/get-data",
            columns: [
            	{
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false,
                    width: "4%"
                },
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                { title: "'.(\Yii::t("fe", "Kode Manufaktur")).'", data: "kode" },
                { title: "'.(\Yii::t("fe", "Nama Manufaktur")).'", data: "nama" },
                { title: "'.(\Yii::t("fe", "Kontak")).'", data: "kontak", searchable: false },
                { title: "'.(\Yii::t("fe", "Alamat")).'", data: "alamat", searchable: false },
                { title: "'.(\Yii::t("fe", "Status Aktif")).'", data: "status_aktif", searchable: false }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, []);
    });

    // Event click
	$(document).on("click", ".data-reload", function() {
		table.draw();
	});

	// Event click
	$(document).on("click", "#tb-manufaktur tbody tr", function() {
		// Try catch
		try {
			// Get primary
			primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
		} catch (e) {
			// Make it false
			primaryKey = false;
		}
		console.log(primaryKey);
		// Assign to ubah
		$("#btn-edit").attr("action", updateUrl + primaryKey);

		// Check class selected
		if ($("#tb-manufaktur tr.selected").length == 0) {
			// Disable edit button
			$("#btn-edit").prop("disabled", true);
			$("#btn-delete").prop("disabled", true);
		}
		else {
			// Disable edit button
			$("#btn-edit").prop("disabled", false);
			$("#btn-delete").prop("disabled", false);
		}
	});

', View::POS_END, 'b-index');
?>
