<?php

/**
 * @Author: Muhamad Lukman Hakim
 * @Date:   2022-01-25 08:25:24
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = Yii::t('fe', 'Akun Bank');
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
                            'data-additional' => 'data-bank',
                            'disabled' => 'disabled'
                        ]
                    ]
                ], '#tb-bank') ?>
            </div>
            <div class="panel-body">
                <div class="filter-form"></div>
                <table id="tb-bank" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="5%"><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Nama Bank") ?></th>
                            <th><?= Yii::t("fe", "Cabang") ?></th>
                            <th><?= Yii::t("fe", "No. Rekening") ?></th>
                            <th><?= Yii::t("fe", "Atas Nama") ?></th>
                            <th><?= Yii::t("fe", "Status Aktif") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
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
    var updateUrl = "/master/bank/update?id=";

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#tb-bank").docoTabel({
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
            sorting: [],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/bank/get-data",
            columns: [
            	{
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false,
                    width: "5%"
                },
                {
                    title: "No.",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width: "5%"
                },
                { title: "' . (\Yii::t("fe", "Nama Bank")) . '", data: "nama_bank" },
                { title: "' . (\Yii::t("fe", "Cabang")) . '", data: "cabang", searchable: false },
                { title: "' . (\Yii::t("fe", "No. Rekening")) . '", data: "no_rekening", searchable: false },
                { title: "' . (\Yii::t("fe", "Atas Nama")) . '", data: "nama_pemilikrek", searchable: false },
                { title: "' . (\Yii::t("fe", "Status")) . '", data: "is_active", class: "text-center"}
            ],
            initComplete: () => {
              $(".change-status").docoToggleSwitch({
                url: baseUrl+"master/bank/change-status",
                confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
                confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
                method: "GET",
              });
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                6,
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $is_active, ['class' => 'form-control select2']))).'\'
            ],
        ],{
          2:0,
          6:1,
        });
    });

    // Event click
	$(document).on("click", ".data-reload", function() {
		table.draw();
	});

	// Event click
	$(document).on("click", "#tb-bank tbody tr", function() {
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
		if ($("#tb-bank tr.selected").length == 0) {
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
