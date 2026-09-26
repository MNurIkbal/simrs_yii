<?php
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
?>

<style type="text/css">
.modal-open .modal {
    overflow-y: hidden !important;
}
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>

<div class="modal-body">
    <div class="panel-toolbar clearfix" style="margin-bottom:20px;">
        <?= DocoHelpers::generateToolbar([
          'search' => [
            'attributes' => [
              'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
              'data-table-id' => 'detail_ruangan',
              'data-options' => 'click',
            ]
          ],
          'reset' => [
            'attributes' => [
              'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset-detail-ruangan',
              'data-table-id' => 'detail_ruangan',
              'data-options' => 'click',
            ]
          ],
        ]); ?>
    </div>
    <div class="row">
        <div class="table-wrapper table-scroll-x">
            <table id="detail_ruangan" class="table table-striped table-condensed table-hover" style="width:100%;margin-top:20px;">
                <thead>
                    <tr class="bg-inverse">
                        <th style="width: 70;">No</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php
$this->registerJs('
    var id = "'.$id.'";
    var instalasiId = "'.$instalasiId.'";
',View::POS_END,'b-index');
$this->registerJs($this->render('detail.js'), View::POS_END);
?>
