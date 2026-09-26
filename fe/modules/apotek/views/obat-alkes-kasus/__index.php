<?php

/**
 * @author Randy Vianda Putra
 * @copyright 3 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Obat Alkes Jenis Kasus Penyakit'), 'url' => ['index']];

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

            <div class="panel-body" style="padding:10px;">
                <div class="col-md-12 panel panel-default" style="margin-top:10px;">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h3>Data</h3>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'ajax-form', 
                                'options' => ['class' => 'form-horizontal'],
                                'action' =>['obat-alkes-kasus/create']
                            ]); 
                        ?>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Obat alkes') ?></label>
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <?= Html::activeDropDownList($model, 'obatalkes_id',
                                        ArrayHelper::map([], 'obatalkes_id', 'name'), [
                                            'class' => 'select2 autoObat',
                                            'prompt' => Yii::t('fe', '-- Pilih --')
                                        ]) 
                                    ?>
                                    <?= Html::hiddenInput('ObatAlkesKasusForm[obatalkes_id]', '', ['class' => 'id_obat']); ?>
                                    <span class="input-group-addon">
                                        <?php
                                            echo Html::a('<i class="fa fa-list-ul"></i>
                                                <i class="fa fa-search"></i>',
                                                Url::home().'apotek/obat-alkes-kasus/list-obat',[
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_backdrop'
                                            ]);
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Jenis kasus penyakit') ?></label>
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <?= Html::activeDropDownList($model, 'jeniskasuspenyakit_id',
                                        ArrayHelper::map([], 'jeniskasuspenyakit_id', 'name'), [
                                            'class' => 'select2 autoKasus',
                                            'prompt' => Yii::t('fe', '-- Pilih --')
                                        ])
                                    ?>
                                    <?= Html::hiddenInput('ObatAlkesKasusForm[jeniskasuspenyakit_id]', '', ['class' => 'id_kasus']); ?>
                                    <span class="input-group-addon">
                                        <?php
                                            echo Html::a('<i class="fa fa-list-ul"></i>
                                                <i class="fa fa-search"></i>',
                                                Url::home().'apotek/obat-alkes-kasus/list-penyakit',[
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_backdrop'
                                            ]);
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-md-offset-3">
                            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', "Simpan"), ['class' => 'btn bg-teal']); ?>
                            <?= Html::resetButton('<i class="fa fa-refresh"></i> '. Yii::t('fe', "Ulang"),['class' => 'btn btn-lime-green reset']); ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                <div class="col-md-12 panel panel-default" style="margin-top:10px;">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h1><?= Yii::t('fe', 'Tabel')?> <?= $this->title ?></h1>
                        </div>
                    </div>
                    <table id="example" class="table table-bordered table-condensed table-hover" style="width:100%"
                        data-source="<?=Url::home();?>apotek/obat-alkes-kasus/get-data"
                        data-filter=".form-filter">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th>Obat Alkes</th>
                                <th>Jenis Kasus Penyakit</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="3">Data tidak ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs($this->render('../assets/js/obat_alkes_kasus.js'));
?>