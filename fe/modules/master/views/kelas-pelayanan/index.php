<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;

$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-white">
			<div class="panel-heading">
				<h3 class="panel-title"><b><?=$title;?></b></h3>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
			</div>
			<div class="panel-toolbar clearfix">                
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        // 'print',
                        // 'pdf',
                        // 'excel',
                        'add',
                        'detail',
                    ]);?>                
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">                        
                    </div>
                </div>

            	<table id="pelayanan_ruangan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">                                        
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php /* list data */ ?>
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>