<?php
// Author : Budi

use yii\web\View;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        // 'search',
                        'save' => [
                            'attributes' => [
                                'onClick' => null,
                                'data-options' => "click",
                                'id' => 'simpan-closing'
                            ]
                        ],
                        // 'pdf' => [
                        //     'attributes' => [
                        //         'id' => 'cetak-pdf',
                        //         'disabled' => true,
                        //         'data-table' => null,
                        //         'data-target' => null,
                        //         'class' => 'btn btn-info btn-labeled btn-xs',
                        //     ]
                        // ],
                        'reset'=> [
                            'attributes'=>[
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'kasir/tra-closing-kasir/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                        // 'reset' => [
                        //     'attributes' => [
                        //         'id' => 'reset-button',
                        //         'data-table' => null,
                        //         'data-target' => null,
                        //         'class' => 'btn btn-info btn-labeled btn-xs',
                        //     ]
                        // ],
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Tanggal Transaksi");?></th>
                                <th><?=\Yii::t("fe", "No Pembayaran");?></th>
                                <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                                <th><?=\Yii::t("fe", "Nama Pasien / No RM");?></th>
                                <th><?=\Yii::t("fe", "Transaksi");?></th>
                                <th><?=\Yii::t("fe", "Keterangan");?></th>
                                <th><?=\Yii::t("fe", "Penjamin");?></th>
                                <th><?=\Yii::t("fe", "Cara Bayar Non Tunai");?></th>
                                <th><?=\Yii::t("fe", "Total Tagihan");?></th>
                                <th><?=\Yii::t("fe", "Tunai");?></th>
                                <th><?=\Yii::t("fe", "Non-Tunai");?></th>
                                <th><?=\Yii::t("fe", "Dijamin");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="col-md-9">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b>Summary Closing</b></h6>
                            </div>

                            <div class="panel-body">
                            <?php 
                                $form = ActiveForm::begin([
                                    'id' => 'closing-kasir-form', 
                                    'action' => '/kasir/tra-closing-kasir/save-closing-kasir',
                                    'enableAjaxValidation'=>false, 
                                    'enableClientValidation'=>false,
                                    // 'type' => ActiveForm::TYPE_HORIZONTAL,
                                    'formConfig' => [
                                        'labelSpan' => 3, 
                                        'deviceSize' => ActiveForm::SIZE_SMALL
                                    ],
                                    'options' => [
                                        'skip-confirm' => "true"
                                    ]
                                ]); 
                            ?>
                            <div class="col-md-5">
                            <?php
                            echo $form->field($model, 'saldo_awal', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-5',
                                            'wrapper' => 'col-md-7'
                                    ],
                                    'addon' => [
                                        'prepend' => [
                                            'content' => 'Rp.'
                                        ]],
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('saldo_awal'),
                                        'class' => 'form-control input-sm text-right doco-number save-closing',
                                        'autocomplete' => "off",
                                        'id' => 'saldo_awal',
                                    ]);
                            echo $form->field($model, 'shift_id',[
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-5 required',
                                            'wrapper' => 'col-md-7'
                                        ],
                                    ])->dropDownList(ArrayHelper::map($shift, 'shift_id', 'shift_nama'),[
                                        'class' => 'select2  save-closing',
                                        'id' => 'list-shift',
                                        'tabindex' => 2,
                                        'prompt' => Yii::t('fe','--Pilih--')
                                    ])->label(Yii::t('fe','Shift'));
                            echo $form->field($model, 'catatan', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-5',
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->textarea([
                                    'class' => 'form-control input-sm save-closing',
                                    'autocomplete' => "off",
                                    'id' => 'catatan',
                                ]);
                            ?>
                            </div>
                            <div class="col-md-2">

                            </div>
                            <div class="col-md-5">
                            <?php
                            echo $form->field($model, 'total_tagihan', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-5',
                                        'wrapper' => 'col-md-7'
                                    ],
                                    'addon' => [
                                        'prepend' => [
                                            'content' => 'Rp.'
                                        ]],
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel('total_tagihan'),
                                    'class' => 'form-control input-sm text-right doco-number save-closing',
                                    'autocomplete' => "off",
                                    'id' => 'total_tagihan_transakasi',
                                    'readonly' => true
                                ]);
                        echo $form->field($model, 'tunai', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-5',
                                            'wrapper' => 'col-md-7'
                                        ],
                                        'addon' => [
                                            'prepend' => [
                                                'content' => 'Rp.'
                                            ]],
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('tunai'),
                                        'class' => 'form-control input-sm text-right doco-number save-closing',
                                        'autocomplete' => "off",
                                        'id' => 'tunai_transakasi',
                                        'readonly' => true
                                    ]);
                            echo $form->field($model, 'nontunai', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-5',
                                                'wrapper' => 'col-md-7'
                                            ],
                                            'addon' => [
                                                'prepend' => [
                                                    'content' => 'Rp.'
                                                ]],
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('nontunai'),
                                            'class' => 'form-control input-sm text-right doco-number save-closing',
                                            'autocomplete' => "off",
                                            'id' => 'nontunai_transakasi',
                                            'readonly' => true
                                        ]);
                            echo $form->field($model, 'dijamin', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-5',
                                                'wrapper' => 'col-md-7'
                                            ],
                                            'addon' => [
                                                'prepend' => [
                                                    'content' => 'Rp.'
                                                ]],
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('dijamin'),
                                            'class' => 'form-control input-sm text-right doco-number save-closing',
                                            'autocomplete' => "off",
                                            'id' => 'dijamin_transakasi',
                                            'readonly' => true
                                        ]);
                            ?>
                            </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>

                    <!-- detail closing dihapus di line ini -->
            </div>
        </div>
    </div>
</div>

<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php 
$this->registerJs('
    // Global Var
    var table;
    var _closing, _totalUang;
    var _cacheClosing = {};
    var _totalTagihan = '.$model->total_tagihan.';
    var _totalTunai = '.$model->tunai.';
    var _totalNonTunai = '.$model->nontunai.';
    var _totalPenjamin = '.$model->dijamin.';
    if (localStorage.getItem("closing-kasir")) {
        var _cacheClosing = JSON.parse(localStorage.getItem("closing-kasir"));
    }
    $(document).ready(function() {
        // Generate Table
        console.log(_totalTagihan)
        _totalClosing();
        
        $(".doco-number").trigger("change");
        $("#total_tagihan_transakasi").val(docoHelper.convertToRupiah(_totalTagihan))
        $("#tunai_transakasi").val(docoHelper.convertToRupiah(_totalTunai))
        $("#nontunai_transakasi").val(docoHelper.convertToRupiah(_totalNonTunai))
        $("#dijamin_transakasi").val(docoHelper.convertToRupiah(_totalPenjamin))
        
        // ajax table detail closing dihapus di line ini
        table = $("#example").docoTabel({
            filter: true,
            scrollX : true,
            sorting: [[1, "desc"]], 
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/tra-closing-kasir/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Transaksi")).'", 
                    data: "tglbuktibayar",
                    name: "tglbuktibayar",
                    searchable: false, 
                },
                {
                    title: "'.(\Yii::t("fe", "No Pembayaran")).'", 
                    data: "no_pembayaran",
                    name: "no_pembayaran",
                    searchable: false, 
                },
                {
                    title: "'.(\Yii::t("fe", "No Pendaftaran")).'", 
                    data: "no_pendaftaran", 
                    searchable:false, 
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pasien / No RM")).'", 
                    data: "nama_pasien_rm", 
                    searchable:false, 
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Transaksi")).'", 
                    data: "jenis", 
                    searchable:false, 
                },
                {
                    title: "'.(\Yii::t("fe", "Keterangan")).'", 
                    data: "keterangan", 
                    searchable:false, 
                },
                {
                    title: "'.(\Yii::t("fe", "Penjamin")).'", 
                    data: "penjamin_nama", 
                    searchable:false, 
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Cara Bayar Non Tunai")).'", 
                    data: "bayar_nontunai",  
                    searchable: false, 
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Total Tagihan (Rp.)")).'", 
                    data: "jmlpembayaran_rupiah", 
                    name: "jmlpembayaran", 
                    searchable: false, 
                    class:"text-right", 
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tunai (Rp.)")).'", 
                    data: "pembayaran_tunai_rupiah", 
                    name: "pembayaran_tunai", 
                    searchable: false, 
                    class:"text-right", 
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Non-Tunai (Rp.)")).'", 
                    data: "pembayaran_nontunai_rupiah", 
                    name: "pembayaran_nontunai", 
                    searchable: false, 
                    class:"text-right", 
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Dijamin (Rp.)")).'", 
                    data: "pembayaran_penjamin_rupiah", 
                    name: "pembayaran_penjamin", 
                    searchable: false, 
                    class:"text-right", 
                    orderable: false
                },
            ],
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                var tr = $(this);
                $.each(dataRows, function (key, val) {
                    // $("#total_tagihan_transakasi").val(docoHelper.convertToRupiah(val.total_tagihan))
                    // $("#tunai_transakasi").val(docoHelper.convertToRupiah(val.tunai))
                    // $("#nontunai_transakasi").val(docoHelper.convertToRupiah(val.nontunai))
                    // $("#dijamin_transakasi").val(docoHelper.convertToRupiah(val.dijamin))
                    // var _primary = val.primary;
                    // if (typeof _cacheClosing[_primary] != "undefined") {
                    //     table.row(":eq("+key+")").select();
                    // }
                })
            }
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'. date('d-M-Y') .'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'. date('d-M-Y') .'" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>\'
                ],
                [
                    9, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('ruangan_kasir', '', 
                            ArrayHelper::map($ruangan_kasir, 'ruangan_id', 'ruangan_nama'), 
                            [
                                'class' => 'form-control select2', 
                                'id' => 'ruangan_id',
                                'prompt' => \Yii::t('fe', '--Pilih--')
                            ]
                        )
                    )).'\'
                ],
                [
                    10, 
                    \''.(preg_replace("/[\n\t\r]/i", '', 
                        Html::dropDownList('shift', '', 
                            ArrayHelper::map($shift, 'shift_id', 'shift_nama'), 
                            [
                                'class' => 'form-control select2 shift', 
                                'prompt' => \Yii::t('fe', '--Pilih--')
                            ]
                        )
                    )).'\'
                ],
                [
                    11, 
                    
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '', 
                            DepDrop::widget(
                                [
                                    'name'=>'nama_pegawai',
                                    'options'=>[
                                        'id'=>'nama_pegawai',
                                        'class'=>'form-control select2 nama_pegawai',
                                    ],
                                    'pluginOptions'=>[
                                        'depends'=>['ruangan_id'],
                                        'placeholder'=>\Yii::t('fe', '--pilih--'),
                                        'url'=>Url::to(['dep-list-pegawai-ruangan'])
                                    ]
                                ]
                            )
                        )
                    ).'\'
                ],
            ]
        );

        dateRangeHelper(".startDate",".endDate",".targetDate");

        $(".daterange-basic").daterangepicker({
            // autoUpdateInput: true,
            startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
            endDate: "'.(date("d-M-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });
    });

    $("#ajax-form").submit(function(event){
        event.preventDefault();
        var _value = $(this).serializeArray();
        var _txt = docoHelper.convertToAngka($("#list-pecahan option:selected").text());
        _value.push({
            name : "pecahan",
            value : _txt
        })
        $(this).docoForm("submit",{
            data : _value,
            success : function(data) {
                $("#list-pecahan").val(null).trigger("change");
                $("#closingkasir-qty").val(null).trigger("change");
                _closing.draw();
                
            }
        });
    });

    var _totalClosing = function () {
        // total_closing_transakasi
        var _total = 0;
        $.each(_cacheClosing, function (key, val) {
            var _uangDiterima = val.uangditerima;
            _total += parseInt(_uangDiterima);
        });
        console.log(_total);
        $("#total_closing_transakasi").val(_total).trigger("change");
    }

    $(document).on("click", "#example tr", function(event){
        event.preventDefault();
        var tbl = $(this).hasClass("selected");
        console.log(_cacheClosing)
        var result = table.row(this).data();
        if (tbl) {
            _cacheClosing[result.primary] = {
                jmlpembayaran : result.jmlpembayaran,
                uangditerima : result.uangditerima
            };
        } else {
            delete _cacheClosing[result.primary];
        }
        localStorage.setItem("closing-kasir",JSON.stringify(_cacheClosing))
        _totalClosing();
    });

    $(document).on("click","#cetak-pdf", function (event) {
        event.preventDefault();
        var _id = $(this).attr("data-id");
        window.open("/kasir/tra-closing-kasir/cetak-closing?id="+_id);
    });

   $(document).on(\'click\',\'.delete\', function(event) {
        event.preventDefault();
        $(this).docoForm(\'delete\',{
            success : function (data) {
                _closing.draw();
            }
        });
    });
    $(document).on("click","#reset-button",function (e) {
        e.preventDefault();
            localStorage.setItem("closing-kasir",JSON.stringify({}));
            $("#list-pegawai").val(null).trigger("change");
            $("#list-pecahan").val(null).trigger("change");
            $(\'div\').removeClass(\'has-error\');
            $(\'span.help-block.error\').remove();
            $(\'div.help-block.error\').remove();
            $.ajax({
                url : "/kasir/tra-closing-kasir/clear-cache",
                type : "GET",
                dataType : "JSON",
                success : function (data) {

                }
            });
            $("#closingkasir-qty").val(0);
            _cacheClosing = {};
            _totalClosing();
            _closing.draw();
            table.draw();
    })

    //Melakukan perhitungan ulang apabila klik Muat Ulang
    $(document).on("click",".data-reset", function (e) {
        $(this).docoForm("click",{
            url : "/kasir/tra-closing-kasir/get-data-tagihan",
            skipConfirm : true,
            skipSuccessNotif :true,
            success : function (data) {
                $("#total_tagihan_transakasi").val(docoHelper.convertToRupiah(data.jmlpembayaran))
                $("#tunai_transakasi").val(docoHelper.convertToRupiah(data.pembayaran_tunai))
                $("#nontunai_transakasi").val(docoHelper.convertToRupiah(data.pembayaran_nontunai))
                $("#dijamin_transakasi").val(docoHelper.convertToRupiah(data.pembayaran_penjamin))
            }
        })
    })
    $(document).on("click","#simpan-closing", function (e) {
        e.preventDefault();
        var urlPost = $("#closing-kasir-form").attr("action");
        var dataPost = $("#closing-kasir-form").serializeArray();

        $(this).docoForm("click",{
            url : urlPost,
            method : "POST",
            type : "json",
            data : dataPost,
            success : function (data) {
                console.log(data.response);

                var id_parent = data.response.id_parent;
                // localStorage.setItem("closing-kasir",JSON.stringify({}));
                // $("#list-pegawai").val(null).trigger("change");
                // $("#cetak-pdf").prop("disabled", false);
                $("#cetak-pdf").attr({
                    "data-id" : data.response.id_parent
                });
                // _cacheClosing = {};
                // _totalClosing();
                $("#list-shift").val(null).trigger("change");
                $("#saldo_awal").val(0)
                $("#catatan").val(0)
                $("#total_tagihan_transakasi").val(0)
                $("#tunai_transakasi").val(0)
                $("#nontunai_transakasi").val(0)
                $("#dijamin_transakasi").val(0)
                table.draw();
                // _closing.draw();

            //     (new PNotify({
            //     title: "Berhasil",
            //     text: "Data Closing Kasir berhasil disimpan, apakah Anda ingin melakukan cetak?",
            //     addclass: "alert alert-success alert-arrow-right alert-styled-right",
            //     type: "success",
            //     buttons: {
            //         closer: false,
            //         sticker: false
            //     },
            //     hide: false,
            //     confirm: {
            //         confirm: true,
            //         buttons: [
            //             {
            //                 text: "Ya",
            //                 addClass: "btn btn-xs btn-success",
            //             },
            //             {
            //                 text: "Tidak",
            //                 addClass: "btn btn-xs btn-danger",
            //             }
            //         ]
            //     },
            //     history: {
            //         history: false
            //     }
            // })).get().on("pnotify.confirm", function() {
            //     window.open("/kasir/tra-closing-kasir/cetak-closing?id="+id_parent);
            // }).on("pnotify.cancel", function() {

            // });
            }
        });
    })
    
    $(document).on("keydown", null, "alt+s", function (event) {
        $(".data-save").click();
    });
    $(document).on("keydown", null, "alt+S", function (event) {
        $(".data-save").click();
    });
', View::POS_END, 'b-index');
?>
