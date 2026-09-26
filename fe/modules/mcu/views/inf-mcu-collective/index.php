<?php
/**
 * @author     (Budi <budi@docotel.com>)
 * @description 
 */

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
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
                    <h3 class="panel-title"><b><?= $title; ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
					'search' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
							'data-table-id' => 'example',
                            'data-options' => 'click',
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar reset-mcu',
							'data-table-id' => 'example',
                            'data-options' => 'click',
                        ]
                    ],
					'detail' => [
                        'title' => \Yii::t('fe', 'Details'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
							'id' => 'data-detail',
                            'data-target'=> '/mcu/inf-mcu-collective/detail?id=',
                        ]
                    ],
                ], '#example');?>
            </div>

            <div class="panel-body">
        		<div class="table-wrapper table-scroll-x">
            		<table id="example" class="table table-striped table-condensed table-hover" style="width:100%;">
                		<thead>
							<tr class="bg-inverse">
								<th width="1"></th>
								<th width="1"></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
								<th></th> 
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
var _status_mcu = '.json_encode($status_mcu).';
var status_open = '.json_encode($status_open).';
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
