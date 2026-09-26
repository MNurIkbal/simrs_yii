<?php

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\bootstrap\Tabs;

$this->title = Yii::t('fe', 'Warna Dokumen');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="tab-content">
                <div class="tab-pane active" id="view-warnadokumen">
                    <div id="content-warnadokumen">  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
$.ajax({
    type: 'GET',
    url: '/master/warna-dokumen/page-warna-dokumen',
    dataType: 'html',
    contentType: 'application/html; charset=utf-8',
    success: function(res){
        $('#content-warnadokumen').html(res);
        $('.select2').select2();
    },
});
",View::POS_END,'warnadokumen');
?>
