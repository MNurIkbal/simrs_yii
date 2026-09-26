<?php

use yii\web\View;
// use yii\widgets\Pjax;
// use app\components\DocoController;
// use yii\helpers\Url;
// use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\bootstrap\Tabs;

$this->title = Yii::t('fe', 'Jenis Kasus Penyakit');
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
                      <h3 class="panel-title"><b><?= Yii::t('fe', 'Master').' '.Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
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
            <?php
            // echo Tabs::widget([
            //     'items' =>
            //         $this->context->getPjaxPage(),
            //     'options' => [
            //         'class'=>'nav nav-sm nav-tabs nav-tabs-solid nav-tabs-component nav-justified'
            //     ]
            // ]);
            ?>
            <div class='tabbable'>
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                    <li class="active" id="tab-jeniskasuspenyakit">
                        <a href="#view-jeniskasuspenyakit" data-toggle="tab" aria-expanded="true">
                            <b><?=Yii::t('fe', 'Jenis Kasus Penyakit')?></b>
                        </a>
                    </li>
                    <li id="tab-kasuspenyakitdiagnosa">
                        <a href="#view-kasuspenyakitdiagnosa" data-toggle="tab" aria-expanded="true">
                            <b><?=Yii::t('fe', 'Jenis Kasus Penyakit Diagnosa')?></b>
                        </a>
                    </li>
                    <li id="tab-kasuspenyakitruangan">
                        <a href="#view-kasuspenyakitruangan" data-toggle="tab" aria-expanded="true">
                            <b><?=Yii::t('fe', 'Jenis Kasus Penyakit Ruangan')?></b>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="view-jeniskasuspenyakit">
                    <div id="content-jeniskasuspenyakit">  </div>
                </div>
                <div class="tab-pane" id="view-kasuspenyakitdiagnosa">
                    <div id="content-kasuspenyakitdiagnosa">  </div>
                </div>
                <div class="tab-pane" id="view-kasuspenyakitruangan">
                    <div id="content-kasuspenyakitruangan">  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
// $.ajax({
//     type: 'GET',
//     url: '/master/jenis-kasus-penyakit/page-jenis-kasus-penyakit',
//     dataType: 'html',
//     contentType: 'application/html; charset=utf-8',
//     success: function(res){
//         $('#content-jeniskasuspenyakit').html(res);
//         $('.select2').select2();
//     },
// });
// $.ajax({
//     type: 'GET',
//     url: '/master/kasus-penyakit-diagnosa/index',
//     dataType: 'html',
//     contentType: 'application/html; charset=utf-8',
//     success: function(res){
//         $('#content-kasuspenyakitdiagnosa').html(res);
//         $('.select2').select2();
//     },
// });
// $.ajax({
//     type: 'GET',
//     url: '/master/kasus-penyakit-ruangan/index',
//     dataType: 'html',
//     contentType: 'application/html; charset=utf-8',
//     success: function(res){
//         $('#content-kasuspenyakitruangan').html(res);
//         $('.select2').select2();
//     },
// });

$(document).on('click','.delete', function(event) {
    event.preventDefault();
    $(this).docoForm('delete',{
        success : function (data) {
            // tabel.reload();
        }
    });
})
",View::POS_END,'Jeniskasuspenyakit');

$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
