<?php

/**
 * @author Randy Vianda Putra
 * @copyright 15 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
$this->title = $title;
// $this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->title?></h3>
                <?= Breadcrumbs::widget([
                        'homeLink' => [ 
                            'label' => Yii::t('yii', 'Home'),
                            'url' => Yii::$app->homeUrl,
                        ],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]);
                ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">                
                <div class="row">                   
                    <?php 
                    $form = ActiveForm::begin([
                        'id' => 'detailmutasiobatalkes-form',
                        'options' => [
                            'class' => 'form-horizontal',
                            'enableAjaxValidation' => true,                           
                            'role' => 'form',
                        ],
                    ]);
                    echo Html::hiddenInput('id', $id, ['id' => 'id']);
                    ?>
                    <div class="form-group">
                        <div class="col-md-6">
                            <?= Html::button('<i class="fa fa-print"></i> '. \Yii::t('fe','Print'), ['class' => 'btn btn-dodger-blue']) ?>
                        </div>
                    </div>
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Tanggal Penerimaan') ?></label>
                                        <div class="col-lg-9">
                                            <span class="form-control-static"><?= DocoHelpers::display_label($response['tglterima'], true, date('d M Y', strtotime($response['tglterima'])));  ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Instalasi Pengirim') ?></label>
                                        <div class="col-lg-9">
                                            <span class="form-control-static"><?= $response['instalasi_pengirim'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Nomor Penerimaan') ?></label>
                                        <div class="col-lg-9">
                                            <span class="form-control-static"><?= $response['noterimamutasi'] ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Ruangan Pengirim') ?></label>
                                        <div class="col-lg-9">
                                            <span class="form-control-static"><?= $response['ruangan_pengirim'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                <div class="row">                                       
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= Yii::t('fe', 'Tabel Penerimaan') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table id="example" class="table datatable-basic table-striped table-hover" data-filter=".form-filter">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Satuan");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
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
    </div>
</div>

<?php 
$this->registerJs('
var table;
var id = $("#id").val();
$(document).ready(function() {
    table = $("#example").docoTabel({
        filter: false,
        sorting: [[2, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"rajal/inf-penerimaan/get-data-detail?id=" + id,
        columns: [                
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'", data: "obatalkes_nama"},
            {title: "'.(\Yii::t("fe", "Qty")).'", data: "jmlterima"},
            {title: "'.(\Yii::t("fe", "Satuan")).'", data: "satuanunit_nama"},
            {title: "'.(\Yii::t("fe", "Tanggal Kadaluarsa")).'", data: "tglkadaluarsa"},
        ],            
    });
});
', View::POS_END, 'detail-penerimaan-obat'); ?>
