<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\LayarAntrianForm;
use Doco\master\controllers\LayarAntrianController;
use yii\widgets\Breadcrumbs;


$this->title = \yii::t('fe', 'Layar Antrian');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
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
												<h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
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
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/layarantrian/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/layarantrian/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-layarantrian');?>
            </div>




				<table class="table datatable-basic table-striped table-hover dataTable no-footer" id="table-layarantrian" data-source="<?=Url::home();?>master/layarantrian/get-data" data-filter=".form-filter" data-test="true">
					<thead>
                        <tr class="bg-inverse">
                            <th width="5%">No</th>
                            <th><?=Yii::t('fe', 'layarantrian_nama'); ?></th>
                            <th><?=Yii::t('fe', 'layarantrian_jenis'); ?></th>
                            <th><?=Yii::t('fe', 'layarantrian_latarbelakang'); ?></th>
                            <th width="5%">Status</th>

                    </thead>
                    <tbody>
                    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php
$this->registerJs("
    var tabelLayarAntrian = $('#table-layarantrian').docoTabel({
        columns : [
            {data: 'rowNum', name : 'rowNum'},
            {data: 'layarantrian_nama', name:'layarantrian_nama'},
            {data: 'jenis', name : 'jenis'},
            {data: 'layarantrian_latarbelakang', name : 'layarantrian_latarbelakang'},
            {data: 'status',name : 'is_active'},
            {data: 'aksi',name : 'aksi'}
        ],
        colNoOrder : [0,5]
    });

    var _test = function (bool) {
        if (bool) {
            tabel.reload(false);
        } else {
            tabel.reload();
        }
    }

    var _afterSave = function (bool) {
        tabel.reload();
    }

    $(document).on('click','.delete', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            success : function (data) {
                tabel.reload()
            }
        });
    })

    $('.reset-filter').on('click', function (e) {
        e.preventDefault();
        tabel.reset();
    });

    $('.select2', $('form.form-filter')).change(function (event) {
        event.preventDefault();
        tabel.reload();
    });

    $('form.form-filter').on('submit', function (e) {
        e.preventDefault();
        tabel.reload();
    });
",View::POS_END,'Layarantrian');
?>
