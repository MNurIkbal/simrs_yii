<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Pendidikan');
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
                                'action' => '/master/#/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/#/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#data-pendidikan');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php
                        echo Html::beginForm(null,'POST',[
                                'class' => 'form-filter',
                            ]);
                    ?>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Indexing :</label>
                            <?php
                                echo Html::textInput('indexing',null,[
                                    'class' => 'form-control',
                                    'placeholder' => 'Indexing'
                                ]);
                            ?>
                        </div>
                        <div class="col-md-4">
                            <label><?=Yii::t('fe', 'Pendidikan / Nama Lainnya')?> :</label>
                            <?php
                                echo Html::textInput('keyword',null,[
                                    'class' => 'form-control',
                                    'placeholder' => 'Pendidikan / nama lainnya'
                                ]);
                            ?>
                        </div>
                        <div class="col-md-4">
                            <label>Status :</label>
                            <?php
                                echo Html::dropDownList('is_active',1,$status,[
                                    'class' => 'select2',
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <?php
                            echo Html::submitButton('<i class="fa fa-search"></i>&nbsp;Cari',[
                                                    'class' => 'btn btn-default',
                                                    'style' => 'margin-right:5px;margin-top:5px;'
                                                ]);
                            echo Html::button('<i class="fa fa-refresh"></i>&nbsp;Ulang',[
                                                    'class' => 'btn btn-default reset-filter',
                                                    'style' => 'margin-top:5px;'
                                                ]);
                            ?>
                        </div>
                    </div>
                    <?php
                        echo Html::endForm();
                    ?>
                    <div class="form-group">
                        <div class="col-md-12">
                            <hr>



                        </div>
                    </div>

                    <table class="table table-striped table-condensed table-hover" style="width:100%" id="data-pendidikan"
                        data-source="<?=Url::home();?>master/pendidikan/get-data"
                        data-filter=".form-filter"
                        data-test="true"
                        >
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Indexing</th>
                                <th>Pendidikan</th>
                                <th>Nama Lainnya</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
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
 var tabel = $('#data-pendidikan').docoTabel({
        columns : [
            {data: 'rowNum', name : 'rowNum'},
            {data: 'indexing.indexing_nama', name : 'indexing_m.indexing_nama'},
            {data: 'pendidikan_nama', name:'pendidikan_nama'},
            {data: 'pendidikan_namalainnya',name : 'pendidikan_namalainnya'},
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
        ",
        View::POS_READY,
        'pendidikan');
?>
