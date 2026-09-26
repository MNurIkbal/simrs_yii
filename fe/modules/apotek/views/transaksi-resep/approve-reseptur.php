<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Reseptur'), 'url' => ['/apotek/informasi-reseptur']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $this->title)];

?>
<style type="text/css">
    .text-right{
        text-align: right;
    }
</style>
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
                    'back' => [
                        'attributes' => [
                            'href' => '/apotek/informasi-reseptur/#'
                        ]
                    ],
                    'update-reseptur' => [
                        'title'=>Yii::t('fe', 'Approve'),
                        'icon' => 'fa fa-floppy-o',
                        'attributes'=>[
                            'class' => 'save-edit',
                            'id' => $type == 'reseptur' ? 'update-reseptur' : 'update-resep',
                            'data-options' => 'click'
                        ]
                    ],
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batalkan Resep'),
                        'icon' => 'fa fa-times',
                        'method' => '#',
                        'attributes' => [
                            'id' => 'btn-batal-resep',
                            'data-options'=>'click'
                        ]
                    ],
                ])
                ?>
            </div>
            <div class="panel-body" style="padding:10px;">
                <!-- Informasi Resep -->
                <div class="col-md-3">
                    <div class="panel panel-default" id="informasi" style="margin-top:10px;">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Informasi Resep Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold">
                                        <?= $type == 'reseptur' ? Yii::t('fe', 'No Reseptur') : Yii::t('fe', 'No Resep') ?>
                                    </div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_resep ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'No pendaftaran') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_pendaftaran ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'No Rekam Medik') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_rekam_medik ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Nama pasien') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $nama_pasien ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Tanggal Lahir') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $tanggal_lahir ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'BB / TB') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $berat_badan."kg / ".$tinggi_badan."cm" ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Dokter resep') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $nama_pegawai ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Instalasi') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $instalasi_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Ruangan') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $ruangan_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Cara bayar') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $carabayar_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Penjamin') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $penjamin_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Diagnosa') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $diagnosa ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Alergi') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?php
                                        $string = '';
                                        if (isset($alergi) && !empty($alergi) && is_array($alergi)) {
                                            foreach ($alergi as $key => $value) {
                                                $valAlergi = $value['riwayat_alergi'];
                                                $replace = str_replace("-", "," , strip_tags($valAlergi));
                                                $replace = preg_replace('/["\[\]]/i', "", $replace);
                                                $list = explode(",", $replace);
                                                $list = array_filter($list);
                                                $cntList = count($list);
                                                $string = '';
                                                    if($cntList > 1) {
                                                        $string = '<ol>';
                                                        foreach ($list as $val) {
                                                            $string .= '<li>' . strip_tags($val) .'</li>';
                                                        }

                                                        $string .= '</ol>';
                                                    } else {
                                                        foreach ($list as $val) {
                                                            $string = $val;
                                                            if($string != strip_tags($string)){
                                                                $string = '';
                                                            }
                                                        }
                                                    }
                                            }
                                            echo $string;
                                        } else {
                                            echo '-';
                                        }
                                    ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Iter') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $iter ?></div>

                                    <input type="hidden" id="penjamin_id" class="form-control" name="penjamin_id" value="<?=$penjamin_id?>">

                                    <input type="hidden" id="kelaspelayanan_id" class="form-control" name="kelaspelayanan_id" value="<?=$kelaspelayanan_id?>" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <?= $this->render('manage-obat-approve-reseptur', [
                        'list_signa' => $list_signa,
                        'model' => $model,
                        'biayaadministrasi' => $biayaadministrasi,
                        'data_racikan' => $data_racikan,
                        'satuan_unit' => $satuan_unit,
                        'status_bayar' => $status_bayar
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop-lg" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->
<?php
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs('
        var ruangan_id = "'.$ruangan_id.'";
        var urutObatRs = "'.$urutObatRs.'";
        var id = "'.$decId.'";
        var transObat = '. $transApotek . ';
        var isBackdate = '. $checkBackdate . ';
        var ispembulatan = "'.$isPembulatan.'";
        var satuanpembulatan = "'.$satuanPembulatan.'";
        var display = "'.$display.'";
        var sum_subtotalNetto = 0;
        var biayaadministrasi = "'.$biayaadministrasi.'";
        var cache_label_track_edit = "'.$cacheLabelTrackEdit.'";
        var status_reseptur = "'.$statusReseptur.'"
        var status_bayar = "'.$status_bayar.'"
        var list_signa = '.$data_signa.'

        $(document).ready(function(){

            if(status_reseptur == "'.DocoConstants::STATUS_RESEPTUR_SUDAH_DIPROSES.'"){
                $(".save-edit").attr("disabled", "disabled");
                $("#btn-batal-resep").attr("disabled", "disabled");
            }

            $("#save-rs").on("click", function (e) {
                e.preventDefault();
                var _url = "/apotek/transaksi-resep/save-rs?id="+id;
                $(this).docoForm("click", {
                    url: _url,
                    data: {
                        totalharga_jual: $(".subTotalItem").val(),
                        totalharga_netto: sum_subtotalNetto,
                        keterangan: $("#keterangan").val(),
                        biayaadministrasi: docoHelper.convertToAngka($(".biayaAdmin").val())
                    },
                    success: function(data) {
                        setTimeout(function(){
                            $("#save-rs, #ulang, #tambah-obat").attr("disabled", "disabled");
                            $("#deleted").css("display","none");
                            $(".deleted").css("display","none");
                            $("#form-obat :input").prop("disabled", "disabled");
                        },500);

                        (new PNotify({
                            title: "Berhasil",
                            text: "Penjualan Resep Rumah Sakit dengan Nomor " + "<strong>" + data.response.nomor + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
                            addclass: "alert alert-success alert-arrow-right alert-styled-right",
                            type: "success",
                            buttons: {
                                closer: false,
                                sticker: false
                            },
                            hide: false,
                            confirm: {
                                confirm: true,
                                buttons: [
                                    {
                                        text: "Ya",
                                        addClass: "btn btn-xs btn-success",
                                    },
                                    {
                                        text: "Tidak",
                                        addClass: "btn btn-xs btn-danger",
                                    }
                                ]
                            },
                            history: {
                                history: false
                            }
                        })).get().on("pnotify.confirm", function() {
                            // Print
                            window.open("/apotek/informasi-reseptur/print-resep?id="+data.response.id+"&noresep="+data.response.nomor);
                        }).on("pnotify.cancel", function() {

                        });

                        setTimeout(function(){
                            window.location.href = "/apotek/informasi-reseptur/#";
                        }, 3000);
                    }
                })
            });

            $(document).on("click", "#btn-batal-resep", function(e){
                $(this).docoForm("click", {
                    url: "/apotek/informasi-reseptur/batal-resep?no_resep='.$nomor.'",
                    confirmMessage: "Apakah anda yakin ingin membatalkan resep ini ?",
                    title: "Sukses",
                    method: "POST",
                    type: "json",
                    success: function() {
                        window.location.replace("/apotek/informasi-reseptur");
                    }
                });

            });

            $("#batal-approve").click(function(e){
                e.preventDefault();
                $(this).docoForm("click", {
                    url: "/apotek/informasi-reseptur/batal-resep?no_resep='.$nomor.'",
                    confirmMessage: "Apakah anda yakin ingin membatalkan resep ini ?",
                    title: "Sukses",
                    method: "POST",
                    type: "json",
                    success: function() {
                        window.location.replace("/apotek/informasi-reseptur");
                    }
                });
            });
        });
    ');
?>