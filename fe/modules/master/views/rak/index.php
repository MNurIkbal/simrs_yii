<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Rak List');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
								<h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
								<?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
					</div>
                </div>
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
                        'edit'
                    ], '#tb-rak') ?>
            </div>
            <div class="panel-body">
             <div class="filter-form"></div>
             <table id="tb-rak" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                        	<th width="5%"></th>
                            <th width="10%"><?= Yii::t('fe', 'No') ?></th>
							<th><?= Yii::t("fe", "Nama Ruangan") ?></th>
							<th><?= Yii::t("fe", "Nama Rak Obat") ?></th>
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
    var table;
    
    $(document).on("click",".data-reset", function(){
        table.draw();
    });

    $(document).ready(function() {
        // Generate Table
        table = $("#tb-rak").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }
            ],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"],[3, "asc"]],
            stateSave:false, 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/rak/get-data",
            columns: [
            	{
                    title: "",
                    data: null,
                    defaultContent: "",
                    orderable: false,
                    searchable: false,
                    width: "4%"
                },
                {
                    title: "No.",
                    data: "rowNum",
                    orderable: false,
                    searchable: false
                },
                { data: "ruangan_nama",searchable:false },
                { title: "'.(\Yii::t("fe", "Nama Rak Obat")).'", data: "rakobat_nama" },
                { title: "'.(\Yii::t("fe", "Nama Ruangan")).'",data:"ruangan_id",visible:false}
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                4, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                    'ruangan_nama', 
                    '', 
                    $ruangan,
                    ['class' => 'form-control select2','prompt' => \Yii::t('fe', 'Semua Ruangan')]))).'\'
            ]
        ]);
    });

', View::POS_END, 'b-index');
?>