<?php

/**
 * @Author: afil
 * @Date:   2018-01-09 14:45:17
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-12 15:09:48
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Kasus penyakit diagnosa');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', 'Cari'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-refresh"></i></b>'.Yii::t('fe', ' Muat Ulang'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-print"></i></b>'.Yii::t('fe', ' Print'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.Yii::t('fe', ' Cetak PDF'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_pdf(this.id,'.filter-form')",
                        'id' => 'pdf',
                        'data-sources' => "/rm/lap-kunjungan/export-pdf"
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_excel(this.id,'.filter-form')",
                        'id' => 'excel',
                        'data-sources' => "/rm/lap-kunjungan/export-excel"
                    ]);
                ?>
            
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
                            <label><?=Yii::t('fe', 'Jenis kasus penyakit')?> :</label>
                            <?php
                                echo Html::textInput('jeniskasuspenyakit_nama',null,[
                                    'class' => 'form-control',
                                    'placeholder' => Yii::t('fe', 'Nama jenis kasus penyakit')
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label><?=Yii::t('fe', 'Kode diagnosa')?> :</label>
                            <?php
                                echo Html::textInput('diagnosa_kode',null,[
                                    'class' => 'form-control',
                                    'placeholder' => Yii::t('fe', 'Kode diagnosa')
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label><?=Yii::t('fe', 'Nama diagnosa')?> :</label>
                            <?php
                                echo Html::textInput('diagnosa_nama',null,[
                                    'class' => 'form-control',
                                    'placeholder' => Yii::t('fe', 'Nama diagnosa')
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
                
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" id="data-jeniskasuspenyakitdiagnosa" 
                        data-source="<?=Url::home();?>rajal/jenis-kasus-penyakit-diagnosa/get-data"
                        data-filter=".form-filter"
                        data-test="true"
                        >
                        <thead>
                            <tr class="bg-inverse">
                                <th>No</th>
                                <th><?=Yii::t('fe', 'Jenis kasus penyakit')?></th>
                                <th><?=Yii::t('fe', 'Kode diagnosa')?></th>
                                <th><?=Yii::t('fe', 'Nama diagnosa')?></th>
                                <th><?=Yii::t('fe', 'Aksi')?></th>
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
    $this->registerJs($this->render('js/_jeniskasuspenyakitdiagnosa.js'));

    // $this->registerJsFile(
    //     '@web/js/bootstrap-switch.min.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );

    // $this->registerCssFile("@web/css/bootstrap-switch.min.css", [
    //     'depends' => [\yii\bootstrap\BootstrapAsset::className()],
    //     'media' => 'print',
    // ], 'css-print-theme');
?>