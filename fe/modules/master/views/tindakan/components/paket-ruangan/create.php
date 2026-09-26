<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Tindakan Ruangan
 * @copyright 26 April 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;
use kartik\widgets\ActiveForm;


// $this->title = $title;

?>

<style>
    .divider-vertical {
        height: 100px;
        /* any height */
        border-left: 1px solid gray;
        /* right or left is the same */
        float: left;
        /* so BS grid doesn't break */
        opacity: 0.5;
        /* optional */
        margin: 0 15px;
        /* optional */
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=
                DocoHelpers::generateToolbar([
                    'kembali' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'click',
                            'data-render' => 'paket-ruangan-tindakan',
                            'data-tab' => 'tab-paket-ruangan',
                            'data-target' => '#view-paket-ruangan',
                        ]
                    ],
                ], '#table-tindakan-ruangan');
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3><strong><?= Yii::t('fe', "Ubah Paket Ruangan") ?></strong></h3>
                    </div>
                </div>
                <?php
                $form = ActiveForm::begin([
                    'id' => 'tindakan-paket-form',
                    // 'action' => '/master/tindakan/simpan-paket',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    // 'type' => ActiveForm::TYPE_INLINE,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'role' => 'form',
                        'enctype' => 'multipart/form-data'
                    ]
                ]);
                ?>

                <?= $form->field($model, 'ruangan_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => Yii::t('fe', "Nama Ruangan"), 'class' => 'form-control input-sm', 'readonly' => true]); ?>

                <hr />

                <div class="form-group" style="margin-bottom:30px;">
                    <div class="col-sm-3">
                        <label><?= Yii::t('fe', "Tambah Paket Tindakan") ?></label>
                    </div>
                    <div class="col-sm-9">
                        <select id="auto-paket"></select>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12" style="margin-top:30px;">

                        <table id="tabel-tampung-paket-ruangan" class="table table-striped table-condensed table-hover" style="width:100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th>Paket</th>
                                    <th>Default</th>
                                    <th>Hapus</th>
                                </tr>
                            </thead>
                        </table>


                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    var tabel;
    var id_enkrip = "<?= $id_encrypt ?>";
    var id_plain = "<?= $id ?>";

    $(document).ready(function() {

        $('#auto-paket').select2({
            minimumInputLength: 2,
            ajax: {
                url: "<?= Url::home() . (Yii::$app->controller->module->id) . '/tindakan/auto-paket' ?>",
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                }
            }
        });


        var tampung_tindakan = {
            "data": [{
                id: 6,
                text: "Adm. Surat Keterangan Kematian",
                selected: true
            }],
            "draw": "2",
            "recordsTotal": 1,
            "recordsFiltered": 0
        };


        var emptyTable = '<?= (\Yii::t("fe", "Tidak ada data yang tersedia")) ?>';
        var info = '<?= (\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")) ?>';
        var infoEmpty = '<?= (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")) ?>';
        var infoFiltered = '<?= (\Yii::t("fe", "(disaring dari _MAX_ total data)")) ?>';
        var lengthMenu = '<?= (\Yii::t("fe", "Menampilkan _MENU_ data")) ?>';
        var loadingRecords = '<?= (\Yii::t("fe", "Memuat...")) ?>';
        var processing = '<?= (\Yii::t("fe", "Memproses...")) ?>';
        var search = '<?= (\Yii::t("fe", "Cari:")) ?>';
        var zeroRecords = '<?= (\Yii::t("fe", "Tidak ada data yang ditemukan")) ?>';
        var first = '<?= (\Yii::t("fe", "Pertama")) ?>';
        var last = '<?= (\Yii::t("fe", "Terakhir")) ?>';
        var next = '<?= (\Yii::t("fe", "Selanjutnya")) ?>';
        var previous = '<?= (\Yii::t("fe", "Sebelumnya")) ?>';
        var sortAscending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")) ?>';
        var sortDescending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")) ?>';

        tabel = $("#tabel-tampung-paket-ruangan").docoTabel({
            filter: false,
            sorting: [
                [2, "asc"]
            ],
            displayLength: 10,
            drawCallback: function() {
                $('.dataTables_scrollBody').scrollTop($('.dataTables_scrollBody')[0].scrollHeight);
            },
            serverSide: true,
            processing: true,
            scrollY: "200px",
            scrollCollapse: true,
            ajax: '/master/tindakan/get-detail-paket-ruangan?id=' + id_enkrip,
            columns: [{
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Paket",
                    data: "tipepaket_nama",
                    searchable: false
                },
                {
                    title: "Default",
                    data: "is_default"
                },
                {
                    title: "Hapus",
                    data: "hapus"
                },
            ],
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                paginate: {
                    first: first,
                    last: last,
                    next: next,
                    previous: previous
                },
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            }
        });

        $('#auto-paket').on('select2:select', function(e) {
            var data = e.params.data;
            $.post("/master/tindakan/simpan-paket-ruangan", {
                ruangan_id: id_plain,
                tipepaket_id: data.id
            }, function(data) {
                var $remote = $('#auto-paket');
                $remote.html('').select2('data', null);
                tabel.draw();
                if (data.response.status_2 == "200") {
                    docoNotification('success', data.response.title, data.response.text);
                }
                if (data.response.status_2 == "422") {
                    docoNotification('error', data.response.title, data.response.text);
                }
                if (data.response.status_2 == "500") {
                    docoNotification('error', data.response.title, data.response.text);
                }

            });

        });

    });

    var updateDefault = function(ruangan_id, tipepaket_id) {
        // alert(ruangan_id+" "+tipepaket_id);
        var is_default = 0;
        if ($("#paket_" + ruangan_id + "_" + tipepaket_id).prop('checked') == true) {
            is_default = 1;
        }
        $.post("/master/tindakan/update-paket-ruangan", {
            ruangan_id: ruangan_id,
            tipepaket_id: tipepaket_id,
            is_default: is_default,
            is_deleted: ""
        }, function(data) {
            var $remote = $('#auto-paket');
            $remote.html('').select2('data', null);
            tabel.draw();
            // console.log(data.response.status_2);
            if (data.response.status_2 == "200") {
                docoNotification('success', data.response.title, data.response.text);
            }
            if (data.response.status_2 == "422" || data.response.status_2 == "500") {
                docoNotification('error', data.response.title, data.response.text);
            }

        });
    }

    var hapusPaketRuangan = function(ruangan_id, tipepaket_id) {
        var is_default = 0;

        $.post("/master/tindakan/update-paket-ruangan", {
            ruangan_id: ruangan_id,
            tipepaket_id: tipepaket_id,
            is_default: is_default,
            is_deleted: "1"
        }, function(data) {
            var $remote = $('#auto-paket');
            $remote.html('').select2('data', null);
            tabel.draw();
            // console.log(data.response.status_2);
            if (data.response.status_2 == "200") {
                docoNotification('success', data.response.title, data.response.text);
            }
            if (data.response.status_2 == "422" || data.response.status_2 == "500") {
                docoNotification('error', data.response.title, data.response.text);
            }

        });
    }
</script>
