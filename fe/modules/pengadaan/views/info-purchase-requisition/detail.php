<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @Last Modified by:   Muhamad Lukman Hakim
 * @Last Modified time: 2021-02-25 17:30:00
 */

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\bootstrap\Modal;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    textarea {
        resize: none;
    }

    table {
        display: block;
        /*overflow-x: auto;*/
        white-space: nowrap;
    }

    #detail-pr thead tr th {
        text-align: center;
    }

    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        width: 50px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 50px;
        border: solid 0.2px;
    }

    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }

    .my-legend a {
        color: #777;
    }

    .square-sukses {
        height: 30px;
        width: 120px;
        background-color: #26A65B;
        color: #ffffff;
        padding: 5px 0 5px 10px;
        margin-right: 20px;
    }

    .square-batal {
        height: 30px;
        width: 70px;
        background-color: #D24D57;
        color: #ffffff;
        padding: 5px 0 5px 10px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?php $sort = isset($_GET['order']) ? $_GET['order'] : 'sorting_asc'; ?>
                <?=
                DocoHelpers::generateToolbar([
                    'custom-back' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'method' => '#',
                        'attributes' => [
                            'id' => 'btn-kembali',
                            'data-options' => 'click'
                        ]
                    ],
                    'export-pdf-serconn' => [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak'),
                        'attributes' => [
                            'id' => 'data-export-pdf-serconn',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'action' => $module . 'show-popup?id=' . $id . '&type=' . $type . '&order=' . $sort
                        ]
                    ],
                ]);
                ?>
                <?php
                if ($showBtnCancel) {
                    echo Html::button("<b><i class='fa fa-close'></i></b>&nbsp;Batal", [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-cancel'
                    ]);
                }

                if ($showBtnGenerate) {
                    echo Html::button("<b><i class='fa fa-cogs'></i></b>&nbsp;Generate PO", [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-generate-po-partial'
                    ]);
                }

                if ($showBtnApprove) {
                    echo ' ' . Html::button("<b><i class='fa fa-check'></i></b>&nbsp;Approve", [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-approve'
                    ]);
                }
                ?>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Info Purchase Request</h6>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Tanggal PR") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p>
                                        <b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'created_date', '-') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Nama Pegawai") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'pegawai', '-') ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "No. PR") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'no_pr', '-') ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Ruangan") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'ruangan', '-') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Status PR") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'status_pr', '-') ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Reference") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'reference', '-') ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Cito ") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'pr_cyto', '-') ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Tanggal Approval") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p>
                                        <b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'tgl_approve', '-') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Admin") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'pr_admin', '-') ?> </p>
                                </div>
                            </div>
                            <?php if (strtolower($type) != DocoConstants::JENIS_BARANG) { ?>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Consignment") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p>
                                        <b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'pr_consignment', '-') ?>
                                    </p>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Detail Obat</h6>
                    </div>
                    <div class="panel-body">
                        <table id="detail-pr" class="table table-striped table-condensed table-hover">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1%" rowspan="2">
                                        <input type="checkbox" class="checkbox-all-pr">
                                    </th>
                                    <th width="1%" rowspan="2">
                                        <?= \Yii::t("fe", "No"); ?>
                                    </th>
                                    <th rowspan="2">
                                        <?= \Yii::t("fe", "Kode Item"); ?>
                                    </th>
                                    <th rowspan="2" id="nama_item">
                                        <?= \Yii::t("fe", "Nama Item"); ?>
                                    </th>
                                    <th rowspan="2">
                                        <?= \Yii::t("fe", "Satuan"); ?>
                                    </th>
                                    <th rowspan="2">
                                        <?= \Yii::t("fe", "Konversi"); ?>
                                    </th>
                                    <th colspan="3">Pemakaian</th>
                                    <th rowspan="2">
                                        <?= \Yii::t("fe", "DOI"); ?>
                                    </th>
                                    <th rowspan="2">
                                        <?= \Yii::t("fe", "SSmin"); ?>
                                    </th>
                                    <th colspan="<?= strtolower($type) != 'barang' ? '3' : '2' ?>">Stok</th>
                                    <?php if ($header['status'] == DocoConstants::VAR_BELUM_APPROVED) { ?>
                                        <th colspan="3">Qty</th>
                                        <th rowspan="2">
                                            <?= \Yii::t("fe", "Catatan"); ?>
                                        </th>
                                        <th rowspan="2">
                                            <?= \Yii::t("fe", "Qty Final"); ?>
                                        </th>
                                        <th rowspan="2">
                                            <?= \Yii::t("fe", "Satuan Final"); ?>
                                        </th>
                                    <?php } else { ?>
                                        <th colspan="3">Qty</th>
                                        <th rowspan="2">
                                            <?= \Yii::t("fe", "Catatan"); ?>
                                        </th>
                                        <th rowspan="2">
                                            <?= \Yii::t("fe", "Status"); ?>
                                        </th>
                                        <th rowspan="2">
                                            <?= \Yii::t("fe", "Nomor PO"); ?>
                                        </th>
                                        <th rowspan="2">
                                            <?= \Yii::t("fe", "Alasan Batal"); ?>
                                        </th>
                                    <?php } ?>
                                    <th>&nbsp;</th>
                                </tr>
                                <tr class="bg-inverse">
                                    <th>
                                        <?= \Yii::t("fe", "7 Hari"); ?>
                                    </th>
                                    <th>
                                        <?= \Yii::t("fe", "14 Hari"); ?>
                                    </th>
                                    <th>
                                        <?= \Yii::t("fe", "30 Hari"); ?>
                                    </th>
                                    <th>
                                        <?= \Yii::t("fe", "Gudang"); ?>
                                    </th>
                                    <?php if (strtolower($type) != DocoConstants::JENIS_BARANG) { ?>
                                        <th>
                                            <?= \Yii::t("fe", "Farmasi"); ?>
                                        </th>
                                    <?php } ?>
                                    <th>
                                        <?= \Yii::t("fe", "R. Lain"); ?>
                                    </th>
                                    <th>
                                        <?= \Yii::t("fe", "Outs. PO"); ?>
                                    </th>
                                    <th>
                                        <?= \Yii::t("fe", "Suggestion"); ?>
                                    </th>
                                    <?php if ($header['status'] == DocoConstants::VAR_BELUM_APPROVED) { ?>
                                        <th>
                                            <?= \Yii::t("fe", "PR"); ?>
                                        </th>
                                    <?php } ?>
                                    <?php if ($header['status'] != DocoConstants::VAR_BELUM_APPROVED) { ?>
                                        <th>
                                            <?= \Yii::t("fe", "PR"); ?>
                                        </th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $objDetail = $detail;
                                foreach ($detail as $key => $_detail) :
                                    $objDetail[$key]['checked'] = false;
                                    $_detail['satuan'] = explode(" ", ArrayHelper::getValue($_detail, 'uom', '-'))[1];
                                ?>
                                    <tr id="<?= $key ?>">
                                        <td class="checkbox-pr">
                                            <input type="checkbox" id="check_<?= $key ?>">
                                        </td>
                                        <td><?= $no ?><span class="hidden span-detail-id"><?= ArrayHelper::getValue($_detail, 'purchasereqdetail_id', '-') ?></span></td>
                                        <td><?= ArrayHelper::getValue($_detail, 'item_kode', '-') ?></td>
                                        <td><?= ArrayHelper::getValue($_detail, 'item_nama', '-') ?></td>
                                        <td><?= ArrayHelper::getValue($_detail, 'satuan', '-') ?></td>
                                        <td><?= ArrayHelper::getValue($_detail, 'uom', '-') ?></td>
                                        <td style="text-align: right;"><?= $_detail['last_7'] ?></td>
                                        <td style="text-align: right;"><?= $_detail['last_14'] ?></td>
                                        <td style="text-align: right;"><?= $_detail['last_30'] ?></td>
                                        <td style="text-align: right;"><?= $_detail['doi'] ?></td>
                                        <td style="text-align: right;"><?= $_detail['ssmin'] ?></td>
                                        <td style="text-align: right;"><?= $_detail['stok_gudang'] ?></td>
                                        <?php if (strtolower($type) != DocoConstants::JENIS_BARANG) { ?>
                                            <td style="text-align: right;"><?= $_detail['stok_farmasi'] ?></td>
                                        <?php } ?>
                                        <td style="text-align: right;"><?= $_detail['stok_ruanganlain'] ?></td>
                                        <td style="text-align: right;"><?= $_detail['qty_outstanding'] ?></td>
                                        <td style="text-align: right;"><?= $_detail['qty_sugesstion'] ?></td>
                                        <?php if ($header['status'] == DocoConstants::VAR_BELUM_APPROVED) { ?>
                                            <td style="text-align: right;"><?= $_detail['qty_pr'] ?></td>
                                            <td><?= ArrayHelper::getValue($_detail, 'catatan', '-') ?></td>
                                            <td width="10%" class="qty-final">
                                                <input type="text" id="qty_final_<?= $key ?>" class="form-control doco-number" style="width: 100%; margin-bottom: 10px; text-align: right;" name="qty_final" value="<?= number_format(round(ArrayHelper::getValue($_detail, 'qty_input', '-')), 0, ",", ".") ?>" oninput="validasiQty(this.value)">
                                            </td>
                                            <td>
                                                <?= ArrayHelper::getValue($_detail, 'satuan', '-') ?>
                                            </td>
                                        <?php } else { ?>
                                            <td>
                                                <?= number_format(round(ArrayHelper::getValue($_detail, 'qty_input', '-')), 0, ",", ".") ?>
                                                <?= ArrayHelper::getValue($_detail, 'satuan', '-') ?>
                                            </td>

                                            <td><?= ArrayHelper::getValue($_detail, 'catatan', '-') ?></td>
                                            <td><?= ArrayHelper::getValue($_detail, 'status', '-') ?></td>
                                            <td>
                                                <?php if ($_detail['status_penerimaan'] == 'Dibatalkan') { ?>
                                                    <?= ArrayHelper::getValue($_detail, 'nomor_po', '-') . " (Batal)" ?>
                                                <?php } else { ?>
                                                    <?= ArrayHelper::getValue($_detail, 'nomor_po', '-') ?>
                                                <?php } ?>
                                            </td>
                                            <td><?= ArrayHelper::getValue($_detail, 'alasan', '-') ?></td>
                                        <?php } ?>
                                        <td class="detail-id"><?= ArrayHelper::getValue($_detail, 'purchasereqdetail_id', '-') ?></td>
                                    </tr>
                                <?php
                                    $no++;
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var type = "' . $type . '";
    var id = "' . $header["purchasereq_id"] . '";
    var status_pr = "'.$header['status'].'";

    function validasiQty(qty) {
        if(qty == 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Qty Final Tidak Boleh 0."));

            return false;
        }

        return true;
    };

    $(document).on("click","#btn-print", function (e) {
        e.preventDefault();
        var _action = $(this).attr("action");
        window.open(_action);
    });

    $(document).on("click", "#btn-kembali", function() {
        if(type == "BARANG") {
            window.location = "/pengadaan/info-purchase-requisition/" + type.toLowerCase();    
        } else {
            window.location = "/pengadaan/info-purchase-requisition/";
        }
        
    });

    $(document).on("click", "#nama_item", function() {
        const params = new URLSearchParams(window.location.search);

        var val_param = [];

        for (const param of params) {
            val_param.push(param[1]);
        }

        var type = val_param[0];
        var id_param = val_param[1];
        
        var sort_status = $(this).attr("class");
        
        window.history.pushState( {} , "", "?type=" + type + "&id=" + id_param + "&order=" + sort_status );

        var data_action = $("#data-export-pdf-serconn").attr("action");
        var extract_action = data_action.substr(0, data_action.indexOf("&order="));

        $("#data-export-pdf-serconn").attr("action", extract_action + "&order="+sort_status);
    });

    $(document).on("click","#btn-approve", function (e) {
        e.preventDefault();
        is_process = true;

        var list_obat = [];
        $("#detail-pr tr").each(function(){
            var currentRow=$(this);
            
            var qty_final = currentRow.find("td.qty-final input").val();
            var detail_id = currentRow.find("span.span-detail-id").text();

            if(detail_id != "") {
                if(!validasiQty(qty_final)) {
                    return is_process = false;
                }

                list_obat.push({
                    "detail_id": detail_id,
                    "qty_final": qty_final.replace(/\./g,"")
                });
            }
        });

        if(is_process) {
            var _data = {
                "id": id,
                "type": type,
                "qtyfinal_list": list_obat
            }

            $.ajax({
                type:"POST",
                url : "/pengadaan/info-purchase-requisition/approving-process",
                data : JSON.stringify(_data),
                contentType:"application/json;charset=utf-8",
                dataType: "json",
                success: function(data) {   
                    docoNotification("success", "Berhasil", "Data berhasil di simpan");
                    setTimeout(function() {
                        //location.reload();
                        if(type == "BARANG") {
                            window.location = "/pengadaan/info-purchase-requisition/" + type.toLowerCase();    
                        } else {
                            window.location = "/pengadaan/info-purchase-requisition/";
                        }
                    }, 3000);
                }, error: function (response) {
                    var data = response.responseJSON
                    docoNotification("error", "Proses Gagal", data.message);
                }
            });
        }
    })
', View::POS_END, 'b-index');
?>
<?php
Modal::begin([
    'header' => '<h5>Alasan Cancel PR</h5>',
    'id' => 'modal',
    'size' => 'modal-md',
]);
?>
<?php
$form = ActiveForm::begin([
    'id' => 'form-cancel-pr',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'validateOnSubmit' => false,
    'formConfig' => [
        'labelSpan' => 4,
        'deviceSize' => ActiveForm::SIZE_MEDIUM
    ],
    'options' => [
        'class' => 'form-horizontal',
        'role' => 'form',
    ]
]);
?>
<div class="row">
    <div class="col-sm-12">
        <textarea autofocus class="form-control" id="alasan_cancel" name="alasan_cancel" rows="3" placeholder="Alasan Cancel PR"></textarea>
    </div>
</div>
<?php ActiveForm::end(); ?>
<hr>
<div class="modal-footer">
    <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali", [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'data-dismiss' => 'modal'
    ]); ?>
    <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'id' => 'btn-submit-cancel'
    ]) ?>
</div>
<?php Modal::end(); ?>

<?php
$this->registerJs("
    var id = '" . DocoHelpers::decrypt($id) . "';
    var detail = " . json_encode($objDetail) . ";
    var belum_po = '" . DocoConstants::VAR_BELUM_PO . "';
    var belum_approved = '" . DocoConstants::VAR_BELUM_APPROVED . "';
    var type = '" . $type . "';
");

$this->registerJs($this->render('../assets/js/purchase-requisition/detail.js'));
?>
