<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-05 10:46:57
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 17:14:01
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;

?>
<style type="text/css">
    .blured-row{
        color: red;
    }
</style>
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Data operasi')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-dokter" class="collapse-click rotate-180" data-toggle="collapse" data-target="#panel-data-operasi"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-operasi" aria-expanded="true" class="collapse in">
    <div class="row" style="padding: 5px">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-is_surgicalsavety">
                <label class="control-label col-md-6" for="intraoperasiform-is_surgicalsavety"><?=$model->attributeLabels()['is_surgicalsavety']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['is_surgicalsavety']) ? $data['info']['is_surgicalsavety'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="padding: 5px">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-masuk_kamar">
                <label class="control-label col-md-6" for="intraoperasiform-masuk_kamar"><?=$model->attributeLabels()['masuk_kamar']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['masuk_kamar']) ? $data['info']['masuk_kamar'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="padding: 5px">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-mulai_anastesi">
                <label class="control-label col-md-6" for="intraoperasiform-mulai_anastesi"><?=$model->attributeLabels()['mulai_anastesi']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['mulai_anastesi']) ? $data['info']['mulai_anastesi'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-selesai_anastesi">
                <label class="control-label col-md-6" for="intraoperasiform-selesai_anastesi"><?=$model->attributeLabels()['selesai_anastesi']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['selesai_anastesi']) ? $data['info']['selesai_anastesi'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="padding: 5px">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-mulai_operasi">
                <label class="control-label col-md-6" for="intraoperasiform-mulai_operasi"><?=$model->attributeLabels()['mulai_operasi']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['mulai_operasi']) ? $data['info']['mulai_operasi'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-selesai_operasi">
                <label class="control-label col-md-6" for="intraoperasiform-selesai_operasi"><?=$model->attributeLabels()['selesai_operasi']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['selesai_operasi']) ? $data['info']['selesai_operasi'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- List Dokter -->
<div class="row" style="display:none">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Data tim operasi')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-tim-operasi" class="collapse-click" data-toggle="collapse" data-target="#panel-data-tim-operasi"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-tim-operasi" aria-expanded="true" class="collapse">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-dokterbedah_id">
                <label class="control-label col-md-6" for="intraoperasiform-dokterbedah_id"><?=$model->attributeLabels()['dokterbedah_id']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['dok_bedah']) ? $data['info']['dok_bedah'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-dokteranastesi_id">
                <label class="control-label col-md-6" for="intraoperasiform-dokteranastesi_id"><?=$model->attributeLabels()['dokteranastesi_id']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['dok_anastesi']) ? $data['info']['dok_anastesi'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <br>
            <table id="table-tim-operasi" class="table table-striped table-condensed " style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th><?=Yii::t('fe', 'Nama pegawai')?></th>
                        <th><?=Yii::t('fe', 'Posisi tim operasi')?></th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <br>
</div>
<!-- End List Dokter -->
<!-- Detail Operasi -->
<div class="row mt-20">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'List dan Detail operasi')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-list-detail-operasi" class="collapse-click" data-toggle="collapse" data-target="#panel-data-list-detail-operasi"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-list-detail-operasi" aria-expanded="true" class="collapse">
<div class="row mt-20">
    <div class="col-md-12">
        <br>
        <table id="table-item-operasi" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <br>
        <br>
    </div>
</div>
<div class="row">
    <br>
    <div class="col-md-12">
        <legend><?=Yii::t('fe', 'Penggunaan Alat Bedah')?></legend>
    </div>
    <div class="col-md-12">
        <table class="table table-striped table-condensed" id="tindakan-luar-bedah-table" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th data-key="no">No</th>
                    <th data-key="nama_operasi"><?=Yii::t('fe', 'Nama Tindakan')?></th>
                    <th data-key="qty"><?=Yii::t('fe', 'Qty')?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<!-- Row for set instrumen -->
<div class="row">
    <br>
    <div class="col-md-12">
        <legend><?=Yii::t('fe', 'Set Instrumen')?></legend>
    </div>
    <div class="col-md-12">
        <table id="table-set-instrumen" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th data-key="no">No</th>
                    <th data-key="nama_operasi"><?=Yii::t('fe', 'Jenis Alat')?></th>
                    <th data-key="persediaan"><?=Yii::t('fe', 'Persediaan')?></th>
                    <th data-key="tambahan"><?=Yii::t('fe', 'Tambahan')?></th>
                    <th data-key="terpakai"><?=Yii::t('fe', 'Terpakai')?></th>
                    <th data-key="sisa"><?=Yii::t('fe', 'Sisa')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <br>
    </div>
    <hr>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-penunjang_khusus_id">
            <label class="control-label col-md-6" for="intraoperasiform-penunjang_khusus_id"><?=$model->attributeLabels()['penunjang_khusus_id']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['penunjang_khusus']) ? $data['info']['penunjang_khusus'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-8">
        <div class="form-group field-intraoperasiform-perlengkapan_pribadi">
            <label class="control-label col-md-3" for="intraoperasiform-perlengkapan_pribadi"><?=$model->attributeLabels()['perlengkapan_pribadi']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['perlengkapan_pribadi']) ? $data['info']['perlengkapan_pribadi'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-is_diathermy">
            <label class="control-label col-md-6" for="intraoperasiform-is_diathermy"><?=$model->attributeLabels()['is_diathermy']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['is_diathermy']) ? $data['info']['is_diathermy'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-posisi_elektroda">
            <label class="control-label col-md-6" for="intraoperasiform-posisi_elektroda"><?=$model->attributeLabels()['posisi_elektroda']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['pos_elektroda']) ? $data['info']['pos_elektroda'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-kondisi_kulit_sebelum">
            <label class="control-label col-md-6" for="intraoperasiform-kondisi_kulit_sebelum"><?=$model->attributeLabels()['kondisi_kulit_sebelum']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['kulit_sebelum']) ? $data['info']['kulit_sebelum'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-kondisi_kulit_setelah">
            <label class="control-label col-md-6" for="intraoperasiform-kondisi_kulit_setelah"><?=$model->attributeLabels()['kondisi_kulit_setelah']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['kulit_setelah']) ? $data['info']['kulit_setelah'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-posisi_operasi">
            <label class="control-label col-md-6" for="intraoperasiform-posisi_operasi"><?=$model->attributeLabels()['posisi_operasi']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['pos_operasi']) ? $data['info']['pos_operasi'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-fiksasi_balon">
            <label class="control-label col-md-6" for="intraoperasiform-fiksasi_balon"><?=$model->attributeLabels()['fiksasi_balon']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['fiksasi_balon']) ? $data['info']['fiksasi_balon'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-kateter_urin">
            <label class="control-label col-md-6" for="intraoperasiform-kateter_urin"><?=$model->attributeLabels()['kateter_urin']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['kateter_urin']) ? $data['info']['kateter_urin'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group field-intraoperasiform-pemakaian_implan">
            <label class="control-label col-md-4" for="intraoperasiform-pemakaian_implan"><?=$model->attributeLabels()['pemakaian_implan']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['pemakaian_implan']) ? $data['info']['pemakaian_implan'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-pencucian_operasi">
            <label class="control-label col-md-6" for="intraoperasiform-pencucian_operasi"><?=$model->attributeLabels()['pencucian_operasi']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['cuci_operasi']) ? $data['info']['cuci_operasi'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- End Detail Operasi -->

<!-- List BPMHP -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Penggunaan BMHP')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-penggunaan-bmhp" class="collapse-click" data-toggle="collapse" data-target="#panel-penggunaan-bmhp"></a></li>
        </ul>
    </div>
</div>
<div id="panel-penggunaan-bmhp" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <br>
        <table id="table-penggunaan-bmhp" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Jenis alat')?></th>
                    <th><?=Yii::t('fe', 'Persediaan')?></th>
                    <th><?=Yii::t('fe', 'Tambahan')?></th>
                    <th><?=Yii::t('fe', 'Terpakai')?></th>
                    <th><?=Yii::t('fe', 'Sisa')?></th>
                    <th><?=Yii::t('fe', 'Ditagihkan')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <br>
    </div>
</div>
</div>
<!-- End List BPMHP -->

<!-- List Cairan -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Penggunaan cairan')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-penggunaan-cairan" class="collapse-click" data-toggle="collapse" data-target="#panel-data-penggunaan-cairan"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-penggunaan-cairan" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <br>
        <table id="table-penggunaan-cairan" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Kegiatan')?></th>
                    <th><?=Yii::t('fe', 'Cairan masuk')?></th>
                    <th><?=Yii::t('fe', 'Cairan keluar')?></th>
                    <th><?=Yii::t('fe', 'Keterangan')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-lokasi_drainvacum">
            <label class="control-label col-md-6" for="intraoperasiform-lokasi_drainvacum"><?=$model->attributeLabels()['lokasi_drainvacum']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['lokasi_drainvacum']) ? $data['info']['lokasi_drainvacum'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-lokasi_drainpenrose">
            <label class="control-label col-md-6" for="intraoperasiform-lokasi_drainpenrose"><?=$model->attributeLabels()['lokasi_drainpenrose']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['lokasi_drainpenrose']) ? $data['info']['lokasi_drainpenrose'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-lokasi_drainselang">
            <label class="control-label col-md-6" for="intraoperasiform-lokasi_drainselang"><?=$model->attributeLabels()['lokasi_drainselang']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['lokasi_drainselang']) ? $data['info']['lokasi_drainselang'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-intraoperasiform-is_jaringantubuh">
            <label class="control-label col-md-6" for="intraoperasiform-is_jaringantubuh"><?=$model->attributeLabels()['is_jaringantubuh']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['is_jaringantubuh']) ? $data['info']['is_jaringantubuh'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<?php 

$hideJaringan = (strtolower($data['info']['is_jaringantubuh']) == 'ya') ? '' : 'hidden';

?>
<div class="pa-jaringan-tubuh <?=$hideJaringan?>">
    <div class="row" style="padding: 5px">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-jenis_jaringan">
                <label class="control-label col-md-6" for="intraoperasiform-jenis_jaringan"><?=$model->attributeLabels()['jenis_jaringan']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['jenis_jaringan']) ? $data['info']['jenis_jaringan'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="padding: 5px">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-is_diserahkan">
                <label class="control-label col-md-6" for="intraoperasiform-is_diserahkan"><?=$model->attributeLabels()['is_diserahkan']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['is_diserahkan']) ? $data['info']['is_diserahkan'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
        <?php 
        $hidePenerima = (strtolower($data['info']['is_diserahkan']) == 'ya') ? '' : 'hidden';
        ?>
        <div class="penerima-pemberi <?=$hidePenerima?>">
            <div class="col-md-4">
                <div class="form-group field-intraoperasiform-penerima">
                    <label class="control-label col-md-6" for="intraoperasiform-penerima"><?=$model->attributeLabels()['penerima']?></label>
                    <div class="col-md-6">
                        <b><?=isset($data['info']['penerima']) ? $data['info']['penerima'] : '';?></b>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group field-intraoperasiform-pegawai_pemberi_id">
                    <label class="control-label col-md-6" for="intraoperasiform-pegawai_pemberi_id"><?=$model->attributeLabels()['pegawai_pemberi_id']?></label>
                    <div class="col-md-6">
                        <b><?=isset($data['info']['pegawai_pemberi']) ? $data['info']['pegawai_pemberi'] : '';?></b>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- End List Cairan -->

<!-- List Alat yang ditinggal -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Alat yang ditinggal dalam tubuh')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-alat-tubuh" class="collapse-click" data-toggle="collapse" data-target="#panel-data-alat-tubuh"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-alat-tubuh" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <br>
        <table id="table-alat-ditubuh" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Jenis alat')?></th>
                    <th><?=Yii::t('fe', 'Jumlah')?></th>
                    <th><?=Yii::t('fe', 'Lokasi')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <br>
        <br>
    </div>
</div>
</div>
<!-- End List Alat yang ditinggal -->

<!-- List pemeriksaan pelengkap -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Pemeriksaan pelengkap')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-pemeriksaan-pelengkap" class="collapse-click" data-toggle="collapse" data-target="#panel-data-pemeriksaan-pelengkap"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-pemeriksaan-pelengkap" aria-expanded="true" class="collapse">
<div class="row">
    <div class="col-md-12">
        <br>
        <table id="table-pemeriksaan-pelengkap" class="table table-striped table-condensed " style="width: 100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Nama pemeriksaan')?></th>
                    <th><?=Yii::t('fe', 'Nama jaringan')?></th>
                    <th><?=Yii::t('fe', 'Ukuran')?></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <br>
        <br>
    </div>
</div>
</div>
<!-- End List pemeriksaan pelengkap -->

<!-- List konsultasi -->
<div class="row">
    <div class="col-md-11">
        <legend><?=Yii::t('fe', 'Konsultasi tindakan')?></legend>
    </div>
    <div class="col-md-1" style="padding-top: 5px">
        <ul class="icons-list">
            <li><a data-action="collapse" id="collapse-konsultasi-tindakan" class="collapse-click" data-toggle="collapse" data-target="#panel-data-konsultasi-tindakan"></a></li>
        </ul>
    </div>
</div>
<div id="panel-data-konsultasi-tindakan" aria-expanded="true" class="collapse">
    <div class="row">
        <div class="col-md-12">
            <br>
            <table id="table-konsul-tindakan" class="table table-striped table-condensed " style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th><?=Yii::t('fe', 'Tindakan')?></th>
                        <th><?=Yii::t('fe', 'Bagian')?></th>
                        <th><?=Yii::t('fe', 'Nama dokter')?></th>
                        <th><?=Yii::t('fe', 'Alasan')?></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <br>
            <br>
        </div>
    </div>
</div>

<?php 

$this->registerJs(' 
    var _id = "'.$id.'"
    var _tableoperasi;
    var _tableitemoperasi;
    var _tablepenggunaancairan;
    var _tablealatditubuh;
    var _tablepemeriksaanpelengkap;
    var _tablekonsultindakan;
    var _tablepenggunaanbmhp;
    var _tabletindakanluarbedah;
    var _tableinstrumen;
    '.$this->render('../../js/inpostview.js'), View::POS_END, 'js');

?>