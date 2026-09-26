<?php

/**
 * @author Johndoe
 * @copyright 23 January 2018
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
                        'id' => 'form',
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
                            <?=DocoHelpers::generateToolbar([
                                'back',
                                'print',                    
                            ]);?>
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
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Tanggal Pemesanan') ?></label>
                                        <div class="col-lg-9">
                                            <p class="form-control-static"><?= DocoHelpers::display_label($response['tgl_pesanbarang'], true, date('d M Y', strtotime($response['tgl_pesanbarang'])));  ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Tanggal Minta Dikirim') ?></label>
                                        <div class="col-lg-9">
                                            <p class="form-control-static"><?= DocoHelpers::display_label($response['tgl_mintadikirim'], true, date('d M Y', strtotime($response['tgl_mintadikirim']))) ?> </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Nomor Pemesanan') ?></label>
                                        <div class="col-lg-9">
                                            <p class="form-control-static"><?= $response['no_pemesanan'] ?></p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Ruangan Tujuan') ?></label>
                                        <div class="col-lg-9">
                                            <p class="form-control-static"><?= $response['ruangan_tujuan'] ?></p>
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
                            <h4 class="panel-title"><?= Yii::t('fe', 'Tabel Pemesanan') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
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
                                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                        <th><?=\Yii::t("fe", "Qty Pemesanan");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                                        <th><?=\Yii::t("fe", "Qty Pemesanan");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
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
        ajax: baseUrl+"kasir/inf-pemesanan-barang/get-data-detail?id=" + id,
        columns: [                
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Nama Barang")).'", data: "barang_nama"},
            {title: "'.(\Yii::t("fe", "Qty Pemesanan")).'", data: "qty_pesan"},
            {title: "'.(\Yii::t("fe", "Satuan Besar")).'", data: "satuan_besar"},
            {title: "'.(\Yii::t("fe", "Qty Pemesanan")).'", data: "qty_konversi"},
            {title: "'.(\Yii::t("fe", "Satuan Kecil")).'", data: "satuan_kecil"},
        ],            
    });
});
', View::POS_END, 'detail-pemesanan-obat'); ?>
