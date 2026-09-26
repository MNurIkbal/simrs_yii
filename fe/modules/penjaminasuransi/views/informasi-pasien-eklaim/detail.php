<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .table-expand {
        margin-top: 10px;
        padding: 10px !important;
        background: #fff;
        border: 1px solid #dddddd;
        border-radius: 8px;
    }

    .table-expand tr td {
        padding: 10px !important;
    }

    .table-expand tr.header-tbl {
        height: 60px;
    }

    .table-expand input[type=text]:disabled {
        background: #fcfbfa;
        color: #555;
        cursor: default;
    }

    .table-expand td.separator {
        width: 10px;
    }

    .table-expand td.title {
        font-weight: bold;
    }

    .prosedur-list {
        list-style: none;
    }

    .prosedur-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'proses' => [
                        'title' => \Yii::t('fe', 'Kirim Klaim Online'),
                        'icon' => 'fa fa-folder',
                        'attributes' => [
                            'data-target' => Url::to(['proses', 'id' => '']),
                            'data-conditions' => 'admisi,state',
                        ]
                    ]
                ]); ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">

                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center">No</th>
                            <th class="text-center">Masuk</th>
                            <th class="text-center">Pulang</th>
                            <th class="text-center">No. SEP</th>
                            <th class="text-center">Pasien</th>
                            <th class="text-center">CBG/ ICD Primary</th>
                            <th class="text-center">Spesial Group</th>
                            <th class="text-center">Tarif Klaim (Rp)</th>
                            <th class="text-center">Tarif RS (Rp)</th>
                            <th class="text-center">DC Kemkes / Status Kirim Online</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="header">
                            <td class="text-center">1</td>
                            <td class="text-center"><a href="#" style="text-decoration: underline;color:blue;">1 agustus 2019</a></td>
                            <td class="text-center">1 agustus 2019</td>
                            <td class="text-center">10237293132131</td>
                            <td class="text-center">Mila 012030132123</td>
                            <td class="text-center">Q-2-312-3</td>
                            <td class="text-center">YY-10-LL</td>
                            <td class="text-center">100.0000</td>
                            <td class="text-center">100.0000</td>
                            <td class="text-center"><i>Terkirim</i></td>
                        </tr>
                        <tr class="header">
                            <td class="text-center">2</td>
                            <td class="text-center"><a href="#" style="text-decoration: underline;color:blue;">1 agustus 2019</a></td>
                            <td class="text-center">1 agustus 2019</td>
                            <td class="text-center">10237293132131</td>
                            <td class="text-center">Mila 012030132123</td>
                            <td class="text-center">Q-2-312-3</td>
                            <td class="text-center">YY-10-LL</td>
                            <td class="text-center">100.0000</td>
                            <td class="text-center">100.0000</td>
                            <td class="text-center"><i>Terkirim</i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
    var name = 'test';
    $(document).ready(function() {

        var expand = `<tr class='child'><td colspan='10'>`;        

        

        

        // $('tr.header').click(function(){
        //     $('tr.child').remove();
        //     $(expand).insertAfter($(this).closest('tr'));
        // });

        $(document).on('click','tr.header', function(){
            var _this = $(this);
            var url = '/penjamin-asuransi/informasi-pasien-eklaim/eklaim?id=MzEy&admisi=MzM';
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data){

                    if(data != ''){
                        data = JSON.parse(data);
                        if(data.info) {
                            var info = data.info;
                            var tblProfile = `
                                <table style='width: 100%;' class='table table-expand' id='table-expand'>
                                    <tbody>
                                        <tr class='header-tbl'> 
                                            <td class='text-center' colspan='6'>
                                                <h3>E-Klaim INACBGS</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class='title'>Jaminan / Cara Bayar </td>
                                            <td class='separator'>:</td>
                                            <td>` + info.carabayar_nama + `</td>
                                            <td class='title'>No. Peserta</td>
                                            <td class='separator'>:</td>
                                            <td>
                                                <input type='text' value='`+info.no_peserta+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class='title'>Nomor Surat Eligibilitas Peserta (SEP) </td>
                                            <td class='separator'>:</td>
                                            <td>
                                                <input type='text' value='`+ info.nosep +`' class='form-control input-sm validate-minus delete-on-edit' disabled>
                                            </td>
                                            <td class='title'>Jenis Rawat </td>
                                            <td class='separator'>:</td>
                                            <td>` +info.jenis_rawat+ ` / `+ info.jeniskelas_nama +`</td>
                                        </tr>
                                        
                                        <tr>
                                            <td class='title'>Kelas Rawat</td>
                                            <td class='separator'>:</td>
                                            <td>`+info.kelas_rawat+`</td>
                                            <td class='title'>Tanggal Rawat</td>
                                            <td class='separator'>:</td>
                                            <td>`+info.tanggal_rawat+`</td>
                                            
                                        </tr>
                                         
                                        <tr>
                                            <td class='title'>Umur</td>
                                            <td class='separator'>:</td>
                                            <td>` + info.umur + `</td>
                                            <td class='title'>LOS (hari)</td>
                                            <td class='separator'>:</td>
                                            <td>`+info.los+`</td>
                                        </tr>
                                        
                                        <tr>
                                            <td class='title'>Berat Lahir (gram)</td>
                                            <td class='separator'>:</td>
                                            <td>`+info.berat_lahir+`</td>
                                            <td class='title'>ADL Score</td>
                                            <td class='separator'>:</td>
                                            <td>Sub Acute :  `+info.adl_subacute+` , Chronic : `+info.adl_cronic+` </td>
                                            
                                        </tr>
                                        <tr>
                                            <td class='title'>Cara Pulang</td>
                                            <td class='separator'>:</td>
                                            <td > ` + info.cara_pulang + ` </td>
                                            <td class='title'>DPJP</td>
                                            <td class='separator'>:</td>
                                            <td >`+info.dpjp+`</td>
                                        </tr>
                                        <tr>
                                            <td class='title'>Jenis Tarif </td>
                                            <td class='separator'>:</td>
                                            <td >`+info.tarif+`</td>
                                            <td class='title'>Tarif Rumah Sakit </td>
                                            <td class='separator'>:</td> 
                                            <td>`+info.tarif_rumah_sakit+`</td>
                                        </tr>
                                    </tbody>
                                </table>
                            `;

                            expand += tblProfile;
                        }
                        
                        var elDiagnosaIcd10 = '';
                        var elDiagnosaIcd9  = '';

                        if(data.diagnosa) {
                            var diagnosa = data.diagnosa;
                            if(diagnosa.diagnosa_icd_10.length) {
                                elDiagnosaIcd10 += `<ul class='prosedur-list'>`;
                                $.each(diagnosa.diagnosa_icd_10, function( index, value ) {
                                    elDiagnosaIcd10 += `<li>`;
                                    elDiagnosaIcd10 += `<div class='diagnosa-title'>`+value.diagnosa_nama+`</div><div class='diagnosa-label'>`;
                                    if(value.diagnosa_kode.length){
                                        $.each(value.diagnosa_kode, function(idx, val) {
                                            elDiagnosaIcd10 += val;
                                        });    
                                    } 
                                    elDiagnosaIcd10 += `</div></li>`;
                                });
                                elDiagnosaIcd10 += `</ul>`;
                            }

                            if(diagnosa.diagnosa_icd_9.length) {
                                elDiagnosaIcd9 += `<ul class='prosedur-list'>`;
                                $.each(diagnosa.diagnosa_icd_9, function( index, value ) {
                                    elDiagnosaIcd9 += `<li>`;
                                    elDiagnosaIcd9 += `<div class='diagnosa-title'>`+value.diagnosa_nama+`</div><div class='diagnosa-label'>`;
                                    if(value.diagnosa_kode.length){
                                        $.each(value.diagnosa_kode, function(idx, val) {
                                            elDiagnosaIcd9 += val;
                                        });    
                                    } 
                                    elDiagnosaIcd9 += `</div></li>`;
                                });
                                elDiagnosaIcd9 += `</ul>`;
                                 
                            }
                        }

                        var tblDiagnosa = `
                            <table style='width: 100%;' class='table table-expand' id='table-diagnosa'>
                                <tr class='header-tbl'> 
                                    <td class='text-center' colspan='3'>
                                        <h3>Diagnosa</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='title'>Diagnosa (ICD 10)</td>
                                    <td class='separator'>:</td>
                                    <td>`+ elDiagnosaIcd10 +`</td>
                                </tr>
                                <tr>
                                    <td class='title'>Diagnosa (ICD 9)</td>
                                    <td class='separator'>:</td>
                                    <td>`+ elDiagnosaIcd9 +`</td>
                                </tr>
                            </table>
                        `;

                        if(data.prosedur) {
                            var prosedur = data.prosedur;
                            var tblProsedur = `
                                <table style='width: 100%;' class='table table-expand' id='table-prosedur'>
                                    <tr class='header-tbl'> 
                                        <td class='text-center' colspan='9'>
                                            <h3>Prosedur</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Prosedur Non Bedah</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.prosedur_nonbedah+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Prosedur Bedah</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.bedah+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Konsultasi</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.konsultasi+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Tenaga Ahli</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.tenaga_ahli+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Keperawatan</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.keperawatan+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Penunjang</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.penunjang+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Radiologi</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.radiologi+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Laboratorium</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.laboratorium+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Pelayanan Darah</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.pelayanan_darah+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Rehabilitasi</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.rehabilitasi+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Kamar / Akomodasi</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.kamar_akomodasi+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Rawat Intensif</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.rawat_intensif+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Obat</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.obat+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Obat Kronis</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.obat_kronis+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Obat Kemoterapi</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.obat_kemoterapi+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Alkes</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.alkes+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>BMHP</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.bmhp+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                        <td class='title'>Sewa Alat</td>
                                        <td class='separator'>:</td>
                                        <td>
                                            <input type='text' value='`+prosedur.sewa_alat+`' class='form-control input-sm validate-minus delete-on-edit' disabled >
                                        </td>
                                    </tr>
                                </table>
                            `;
                        }

                        if(data.final_grouper) {
                            var grouper = data.final_grouper;
                            var tblGrouper = `
                                <table style='width: 100%;' class='table table-expand' id='table-grouper'>
                                    <tr class='header-tbl'> 
                                        <td class='text-center' colspan='5'>
                                            <h3>Hasil Grouper</h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Info 0</td>
                                        <td class='separator'>:</td>
                                        <td colspan='3'>`+grouper.infoTxt+`</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Jenis Rawat</td>
                                        <td class='separator'>:</td>
                                        <td colspan='3'>`+grouper.jenis_rawat+`</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Group</td>
                                        <td class='separator'>:</td>
                                        <td>`+grouper.group.nama+`</td>
                                        <td class='text-right'>`+grouper.group.kode+`</td>
                                        <td class='text-right'>`+grouper.group.harga+`</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Sub Acute</td>
                                        <td class='separator'>:</td>
                                        <td>`+grouper.subcute.nama+`</td>
                                        <td class='text-right'>`+grouper.subcute.kode+`</td>
                                        <td class='text-right'>`+grouper.subcute.harga+`</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Chronic</td>
                                        <td class='separator'>:</td>
                                        <td>`+grouper.chronic.nama+`</td>
                                        <td class='text-right'>`+grouper.chronic.kode+`</td>
                                        <td class='text-right'>`+grouper.chronic.harga+`</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Special Procedure</td>
                                        <td class='separator'>:</td>
                                        <td>`+grouper.spesial_prosedur.nama+`</td>
                                        <td class='text-right'>`+grouper.spesial_prosedur.kode+`</td>
                                        <td class='text-right'>`+grouper.spesial_prosedur.harga+`</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Special Prosthesis</td>
                                        <td class='separator'>:</td>
                                        <td>`+grouper.spesial_prosthesis.nama+`</td>
                                        <td class='text-right'>`+grouper.spesial_prosthesis.kode+`</td>
                                        <td class='text-right'>`+grouper.spesial_prosthesis.harga+`</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Special Investigation</td>
                                        <td class='separator'>:</td>
                                        <td>`+grouper.spesial_investigasi.nama+`</td>
                                        <td class='text-right'>`+grouper.spesial_investigasi.kode+`</td>
                                        <td class='text-right'>`+grouper.spesial_investigasi.harga+`</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Special Drug</td>
                                        <td class='separator'>:</td>
                                        <td>`+grouper.spesial_drug.nama+`</td>
                                        <td class='text-right'>`+grouper.spesial_drug.kode+`</td>
                                        <td class='text-right'>`+grouper.spesial_drug.harga+`</td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Status Data Klaim</td>
                                        <td class='separator'>:</td>
                                        <td>`+grouper.status_data_klaim+`</td>
                                        <td class='text-right'></td>
                                        <td class='text-right'></td>
                                    </tr>
                                    <tr>
                                        <td class='title'>Status Klaim</td>
                                        <td class='separator'>:</td>
                                        <td></td>
                                        <td class='text-right'></td>
                                        <td class='text-right'></td>
                                    </tr>
                                    <tr>
                                        <td class='title text-right' colspan='4'>Total</td>
                                        <td class='text-right'>Rp. 377,100</td>
                                    </tr>
                                </table>
                            `;
                        }
                        expand += tblProsedur;
                        expand += tblDiagnosa;
                        expand += tblGrouper;
                        expand += `</td></tr>`;
                        $('tr.child').remove();
                        $(expand).insertAfter(_this.closest('tr'));
                    }
                }
            });
        });
    });  
");
?>
