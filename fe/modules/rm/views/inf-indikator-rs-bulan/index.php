<?php

/**
 * @Author: Aris
 * @Date:   2019-07-23 14:34:00
 * @Description:
 */

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

$this->title = isset($title) ? $title : Yii::t('fe', 'Informasi Indikator Rumah Sakit Per Bulan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rm'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                </div>
                <div class="column-2">
                    <h3 class="panel-title">
                        <b>
                            <?php
                            // echo Yii::$app->docoVars->workspace("modul_alias");
                            echo $this->title;
                            ?>
                        </b>
                    </h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                </div>
            </div>
            <!-- end -->
        </div>

        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'search',
                'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                /*'excel' => [
                    'title' => Yii::t('fe', 'Excel'),
                    'attributes'=>[
                        'data-target'=>Url::home().'rm/inf-indikator-rs-bulan/export-excel?'
                  ]
                ],*/
                
        ], '#inf-indikator-rs-bulan');?>
        </div>

      <div class="panel-body">
        <div class="row">
            <!--<div class="col-md-12 filter-form"></div>-->
        </div>
        <div class="advanced-filter">
        </div>
        <div class="row">
            <div class="col-md-12">
                <table id="table-indikator" class="table table-striped table-condensed table-hover" style="width:100%">
                   <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'Indikator')?></th>
                        <th><?=Yii::t('fe', '1')?></th>
                        <th><?=Yii::t('fe', '2')?></th>
                        <th><?=Yii::t('fe', '3')?></th>
                        <th><?=Yii::t('fe', '4')?></th>
                        <th><?=Yii::t('fe', '5')?></th>
                        <th><?=Yii::t('fe', '6')?></th>
                        <th><?=Yii::t('fe', '7')?></th>
                        <th><?=Yii::t('fe', '8')?></th>
                        <th><?=Yii::t('fe', '9')?></th>
                        <th><?=Yii::t('fe', '10')?></th>
                        <th><?=Yii::t('fe', '11')?></th>
                        <th><?=Yii::t('fe', '12')?></th>
                        <th><?=Yii::t('fe', 'Rata2 perTahun')?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>

            </table>
        </div>
    </div>
</div>
</div>
</div>

<?php

$listTahun = [];
for ($x = date('Y'); $x >= (date('Y') - 9) ; $x--) {
    $listTahun[$x] = $x;
}

$this->registerJs("
    var table;

    $(document).ready(function(){

    // Generate Table
        table = $('#table-indikator').docoTabel({
            filter: true,
            //add for handle checkbox
            
            sorting: [[0, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/inf-indikator-rs-bulan/get-data-indikator-rs-bulan?',
            columns: [
                {
                    title: '".(\Yii::t('fe', 'INDIKATOR'))."', 
                    data: 'name',
                    name: 'tahun',
                    orderable: false
                },
                {
                    title: '".(\Yii::t('fe', 'Jan'))."', 
                    data: '0',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '".(\Yii::t('fe', 'Feb'))."', 
                    data: '1',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Mar'))."', 
                    data: '2',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Apr'))."', 
                    data: '3',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Mei'))."', 
                    data: '4',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Jun'))."', 
                    data: '5',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Jul'))."', 
                    data: '6',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Agu'))."', 
                    data: '7',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Sep'))."', 
                    data: '8',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Okt'))."', 
                    data: '9',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Nov'))."', 
                    data: '10',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Des'))."', 
                    data: '11',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Rata2 perTahun'))."', 
                    data: '12',
                    searchable: false,
                    orderable: false,
                },
            ],

            
        });
        
        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, 
        [
            [
                0,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',

                    Html::dropDownList('tahun', '',$listTahun,
                        [
                            'id'     => 'filter_status_skl',
                            'class'  => 'form-control select2',
                            'style'  => 'width:100%;',
                        ]
                    )
                ))."<div>\"
            ],
            
        ],
        {
            //posisi kolom dan grid
            0:0,
        });
        

        });
        ")

        ?>