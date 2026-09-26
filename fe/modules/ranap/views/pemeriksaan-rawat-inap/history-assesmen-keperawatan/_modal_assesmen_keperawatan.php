<?php
use app\components\DocoConstants;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>
<style type="text/css">
     .modal-dialog {
        width: 80% !important;
        /* padding-top: 5px; */
    }
    .modal-body {
        max-height: calc(100vh - 210px);
        overflow-y: auto;
    }
    .btn-view-assesmen {
        margin-bottom: 5px;
        margin-right: 5px;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="panel-toolbar clearfix">
    </div>
    <div class="panel-body">
        <div class="row">
            <table class="table table-bordered datatable-basic dataTable" id="table-history-assesmen" style="width:100%;">
                <thead class="text-center">
                    <tr class="bg-inverse">
                        <th width="8px">No</th>
                        <th><?= Yii::t('fe', 'Tanggal Assesmen') ?></th>
                        <th><?= Yii::t('fe', 'Perawat') ?></th>
                        <th><?= Yii::t('fe', 'Aksi') ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
<?php
$this->registerJs($this->render('js/__modal.js'), View::POS_END);
?>