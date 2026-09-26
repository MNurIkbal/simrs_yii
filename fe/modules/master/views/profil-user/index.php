<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-03 14:31:56
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-08 16:22:05
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

$this->title = 'Profil Pengguna';
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
                        <li><a data-action="reload"></a></li>
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
                    ],'#data-profil');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="row"></div>
                    <div class="col-md-12 filter-form">
                    <div class="row">
                    <?php
                        echo Html::beginForm(null,'POST',[
                                'class' => 'form-filter',
                            ]);
                    ?>
                        <div class="form-group">
                            <div class="col-md-4">
                                <label>Nama :</label>
                                <?= Html::dropDownList('nama',null,
                                        ArrayHelper::map($data_nama['response']['data'], 'loginpemakai_id', 'nama_pemakai'),
                                        [
                                            'class' => 'select2 namaSelect',
                                            'prompt'=>'PILIH',
                                            'id'=>'selectnama'
                                        ]) ?>
                            </div>
                        </div>
                    </div>




                    <?php
                        echo Html::endForm();
                    ?>
                    </div>
                </div>
                <table id="data-profil" class="table table-striped table-condensed table-hover dataTable"
                        data-source="<?=Url::home();?>master/profil-user/get-data"
                        data-filter=".form-filter"
                        data-test="true">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th>Nama Pengguna</th>
                            <th>Jabatan</th>
                            <th>Akses Ruangan</th>
                            <th width="1">Aksi</th>
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
    var tabel = $('#data-profil').docoTabel({
        columns : [
            {data: 'rowNum', name : 'rowNum'},
            {data: 'nama_pemakai', name : 'nama_pemakai'},
            {data: 'jabatan_nama',name : 'jabatan_nama'},
            {data: 'ruangan_nama', name:'ruangan_nama'},
            {data: 'aksi',name : 'aksi'}
        ],
        colNoOrder : [0,4]
    });
    var _afterSave = function (bool) {
        tabel.reload();
    }
    $('form.form-filter').on('submit', function (e) {
        e.preventDefault();
        tabel.reload();
    });
    $('.reset-filter').on('click', function (e) {
        e.preventDefault();
        tabel.reset();
    });
    ", View::POS_END);
?>
