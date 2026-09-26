<?php

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
?>
<style>
    #tb-sbar_wrapper {
        height: 1400px !important;
        overflow-y: scroll;
    }

    .dataTables_scroll {
        height: 100% !important;
        max-height: 100%;
    }

    .select2-selection__clear:after {
        content: '' !important;
    }

    .range_custom{
		height:20px;
		padding:13px 12px;
	}
    .select2-selection__clear{
        position: absolute;
        top: 0;
        left: -10px;
        height: 100%;
        padding: 0 12px;
    }

    #tb-sbar_wrapper #menu-action-sbar th div.flex-menu-sbar{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        font-size: 12px;
        padding: 5px 10px;
    }

    #tb-sbar_wrapper #menu-action-sbar .link-action-sbar-table:first-child{
        padding-right: 10px;
    }

    #tb-sbar_wrapper #menu-action-sbar .link-action-sbar-table{
        padding-right: 10px;
        padding-left: 10px;
        margin: 0px;
        color: #34bfa3;
    }

    #tb-sbar_wrapper #menu-action-sbar .link-action-sbar-table:last-child{
        padding-left: 10px;
    }

    #tb-sbar_wrapper .link-action-sbar-table:hover{
        color: #1ca189;
    }

    #tb-sbar_wrapper .link-action-sbar-table:disabled{
        color: #1ca189;
    }

    .btnSearch,
    .btnClear{
        display: inline-block;
        vertical-align: top;
    }
</style>
<div class="row body">
	<div class="col-md-12">
		<div id="div-sbar">
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-white">
						<div class="panel-heading">
							<h5 class="panel-title"><?= $title ?></h5>
						</div>
						<div class="panel-heading clearfix">
							<div class="row">
								<div class="col-sm-12 ">
                                    <?=DocoHelpers::generateToolbar([
                                        'search' => [
                                            'attributes' => [
                                                'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                                                'data-table-id' => 'tb-sbar',
                                                'data-options' => 'click',
                                            ]
                                        ],
                                        'reset' => [
                                            'attributes' => [
                                                'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset-sbar',
                                                'data-table-id' => 'tb-sbar',
                                                'data-options' => 'click',
                                            ]
                                        ],
                                        'add' => [
                                            'title' => 'Input SBAR',
                                            'attributes' => [
                                                'data-options' => 'click',
                                                'id' => 'input-sbar',
                                                'data-width' => '75%',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_backdrop_sbar',
                                                'action' => '/' . $modul . '/' . $url . '/input-sbar?pendaftaran_id=' . $pendaftaranId,
                                            ],
                                        ],
                                        'pdf' => [
                                            'attributes' => [
                                                'data-options' => 'click',
                                                'id' => 'btn-print-sbar',
                                            ],
                                        ],
                                    ]);?>
								</div>
							</div>
						</div>
						<div class="panel-body">
							<br>
                            <table class="table table-bordered datatable-basic dataTable" id="tb-sbar" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="10">No</th>
                                        <th width="20%"><?= Yii::t('fe', 'Waktu Input') ?></th>
                                        <th width="20%"><?= Yii::t('fe', 'Dokter Tujuan') ?></th>
                                        <th width="50%"><?= Yii::t('fe', 'Inputan SBAR Pasien') ?></th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                    <tr id="menu-action-sbar">
                                        <th colspan="5">
                                            <div class="flex-menu-sbar">
                                                <div class="list-action-button-sbar-table">
                                                    <input type="hidden" id="filter-sbar-limit" value="5">
                                                </div>
                                                <div class="">
                                                    Total <span id="total-sbar-data">#</span> SBAR
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tr id="menu-action-sbar">
                                    <th colspan="5">
                                        <div class="flex-menu-sbar">
                                            <div class="list-action-button-sbar-table">
                                                <a href="#" class="link-action-sbar-table" data-event="show">Lihat SBAR Selanjutnya</a>
                                                <a href="#" class="link-action-sbar-table" data-event="hide" style="visibility: hidden;">Tutup SBAR yang sudah dibuka</a>
                                                <input type="hidden" id="filter-sbar-limit" value="5">
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="modal_backdrop_sbar" class="modal fade" style="z-index: 1041 !important; overflow-y: auto;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php
    $this->registerJs("
        var pendaftaranIdSbar = '" . $pendaftaranId . "';
        var pendaftaranIdDecrypt = '" . DocoHelpers::decrypt($pendaftaranId) . "';
        var url = '" . $url . "';
        var modul = '" . $modul . "/';
        var instalasiId = '".$instalasiId."';
        var createdBy = '".$createdBy."';
    ", View::POS_END);
    $this->registerJs($this->render('index.js'), View::POS_END);
?>
