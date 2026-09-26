<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', 'Detail Penerimaan Barang Manual');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi Penerimaan Barang Manual', 'url' => ['index']];
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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                                <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                <?= DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => '/gudang/inf-penerimaan-barang-manual'
                        ]
                    ],
                    'custom-print' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Cetak PDF'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id' => 'print-detail',
                            'data-target' => '/gudang/inf-penerimaan-barang-manual/export-pdf?id='.$id,
                            'data-options' => 'link',
                            'target' => "_blank"
                        ],
                    ],
                    'cetak-grn' => [
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak GRN'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id'=>'btn-print-grn',
                            'class'=>'print-grn',
                            'target'=>'_blank',
                            'data-options'=>'link',
                            'data-target' => '/gudang/inf-penerimaan-barang-manual/print-grn?id='.$id.'&type=barang'
                        ]
                    ],
                    'verifikasi' => [
                        'type'       => 'button',
                        'title'      => \Yii::t('fe', 'Verifikasi'),
                        'icon'       => 'fa fa-check',
                        'method'     => '#',
                        'attributes' => [
                            'id'            => 'btn-verifikasi',
                            'data-options'  => 'click',
                            'disabled'      => !$role_verifikasi,
                            'title'         => !$role_verifikasi ? \Yii::t('fe', 'Anda tidak memiliki role verifikator') : '',
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
               <!-- pannel detail pasien -->
               <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Penerimaan'); ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-4"><b>
                                                <?= Yii::t("fe", "Tanggal Penerimaan") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['tgl_penerimaan']) ? date('d-M-Y',strtotime($data['tgl_penerimaan'])) : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-4"><b><?= Yii::t("fe", "No Penerimaan") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['no_penerimaan']) ? $data['no_penerimaan']  : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-4"><b>
                                                <?= Yii::t("fe", "Status Verifikasi") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['status_verifikasi']) ? $data['status_verifikasi']  : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-4"><b>
                                                <?= Yii::t("fe", "Nama Supplier") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['supplier_nama']) ? $data['supplier_nama'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-4"><b><?= Yii::t("fe", "No Faktur") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['no_faktur']) ? $data['no_faktur']  : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-4"><b>
                                                <?= Yii::t("fe", "Tanggal Verifikasi") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['tgl_verifikasi']) || !is_null($data['tgl_verifikasi']) ? date('d-M-Y',strtotime($data['tgl_verifikasi'])) : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <table id="example" class="table table-striped table-condensed table-hover"
                            style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                        <th><?=\Yii::t("fe", "Qty Penerimaan");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                        <th><?=\Yii::t("fe", "Harga Netto");?></th>
                                        <th><?=\Yii::t("fe", "PPn (%)");?></th>
                                        <th><?=\Yii::t("fe", "Diskon (%)");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render("/alert-perubahan-harga/_modal.php") ?>

<?php

$this->registerJs("
    var penerimaan = '$id';
    var _tabel;
    $(document).ready(function(){
        table = $('#example').docoTabel({
            filter: true,
            sorting: false,
            displayLength: 10,
            sorting: [[1, 'asc']],
            processing: true,
            serverSide: true,
            ajax: baseUrl+'gudang/inf-penerimaan-barang-manual/get-data-detail?id={$id}',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Nama Barang',
                    data: 'barang_nama',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Qty Penerimaan',
                    data: 'qty_besar',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Satuan Besar',
                    data: 'satuan_besar',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Tanggal Kadaluarsa',
                    data: 'tgl_kadaluarsa',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Harga Netto',
                    data: 'harga_input_satuan',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'PPn (%)',
                    data: 'ppn',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Diskon (%)',
                    data: 'diskon',
                    searchable: false,
                    orderable: false
                },
            ],
        });
        $('.dataTables_filter').hide();
    });

    $(document).on('click', '#btn-verifikasi', function(){
        $(this).docoForm('click', {
            url     : `/gudang/inf-penerimaan-barang-manual/verifikasi-penerimaan?id=`+penerimaan,
            title   : `Sukses`,
            method  : 'POST',
            type    : 'JSON',
            success : function(resp){
                alertHarga(resp.response.penerimaansupp, true);
            },
            error   : function(resp){
                console.log('something went wrong');
            }
        });
    });

".$this->render('/alert-perubahan-harga/alert-harga.js', ["url"=>"inf-penerimaan-barang-manual"]), VIEW::POS_END, 'js-kunings');