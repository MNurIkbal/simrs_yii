<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-05 14:10:25
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-12 17:03:59
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;

?>
<?php 

$recoveryYa = '';
$recoveryTidak = 'hidden';

if(isset($data['info']['is_surgicalsavety']) && strtolower($data['info']['is_surgicalsavety']) == 'tidak'){
    $recoveryYa = 'hidden';
    $recoveryTidak = '';
}

?>
<div class="row" style="padding:5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-is_recovery">
            <label class="control-label col-md-5" for="postoperasiform-is_recovery"><?=$model->attributeLabels()['is_recovery']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['is_surgicalsavety']) ? $data['info']['is_surgicalsavety'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="recovery-ya <?=$recoveryYa?>">
    <div class="row" style="padding: 5px">
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-jam_masuk_rec">
                <label class="control-label col-md-4" for="intraoperasiform-jam_masuk_rec"><?=$model->attributeLabels()['jam_masuk_rec']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['jam_masuk_rec']) ? $data['info']['jam_masuk_rec'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group field-intraoperasiform-jam_keluar_rec">
                <label class="control-label col-md-4" for="intraoperasiform-jam_keluar_rec"><?=$model->attributeLabels()['jam_keluar_rec']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['jam_keluar_rec']) ? $data['info']['jam_keluar_rec'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="recovery-tidak <?=$recoveryTidak?>">
    <div class="row" style="padding: 5px">
        <div class="col-md-4">
            <div class="form-group field-postoperasiform-kembali_ruangan_id">
                <label class="control-label col-md-4" for="postoperasiform-kembali_ruangan_id"><?=$model->attributeLabels()['kembali_ruangan_id']?></label>
                <div class="col-md-6">
                    <b><?=isset($data['info']['kembali_ruangan']) ? $data['info']['kembali_ruangan'] : '';?></b>
                    <div class="help-block"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-kesadaran_umum">
            <label class="control-label col-md-4" for="postoperasiform-kesadaran_umum"><?=$model->attributeLabels()['kesadaran_umum']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['kes_umum']) ? $data['info']['kes_umum'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <?php 
    $class = 'hidden';
    if(isset($data['info']['kes_umum']) && strtolower($data['info']['kes_umum']) == 'lain-lain'){
        $class = '';
    }
    ?>
    <div class="col-md-4 <?=$class?>">
        <div class="form-group field-postoperasiform-kesadaran_umum_lain">
            <label class="control-label col-md-4" for="postoperasiform-kesadaran_umum_lain"><?=$model->attributeLabels()['kesadaran_umum_lain']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['kesadaran_umum_lain']) ? $data['info']['kesadaran_umum_lain'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-tingkat_kesadaran">
            <label class="control-label col-md-4" for="postoperasiform-tingkat_kesadaran"><?=$model->attributeLabels()['tingkat_kesadaran']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['tingkat_kes']) ? $data['info']['tingkat_kes'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <?php 
    $class = 'hidden';
    if(isset($data['info']['tingkat_kes']) && strtolower($data['info']['tingkat_kes']) == 'lain-lain'){
        $class = '';
    }
    ?>
    <div class="col-md-4 <?=$class?>">
        <div class="form-group field-postoperasiform-tingkat_kesadaran_lain">
            <label class="control-label col-md-4" for="postoperasiform-tingkat_kesadaran_lain"><?=$model->attributeLabels()['tingkat_kesadaran_lain']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['tingkat_kesadaran_lain']) ? $data['info']['tingkat_kesadaran_lain'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-jalan_napas">
            <label class="control-label col-md-4" for="postoperasiform-jalan_napas"><?=$model->attributeLabels()['jalan_napas']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['jln_napas']) ? $data['info']['jln_napas'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <?php 
    $class = 'hidden';
    if(isset($data['info']['jln_napas']) && strtolower($data['info']['jln_napas']) == 'lain-lain'){
        $class = '';
    }
    ?>
    <div class="col-md-4 <?=$class?>">
        <div class="form-group field-postoperasiform-jalan_napas_lain">
            <label class="control-label col-md-4" for="postoperasiform-jalan_napas_lain"><?=$model->attributeLabels()['jalan_napas_lain']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['jalan_napas_lain']) ? $data['info']['jalan_napas_lain'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-terapi_oksigen">
            <label class="control-label col-md-4" for="postoperasiform-terapi_oksigen"><?=$model->attributeLabels()['terapi_oksigen']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['terapi_oks']) ? $data['info']['terapi_oks'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <?php 
    $class = 'hidden';
    if(isset($data['info']['terapi_oks']) && strtolower($data['info']['terapi_oks']) == 'lain-lain'){
        $class = '';
    }
    ?>
    <div class="col-md-4 <?=$class?>">
        <div class="form-group field-postoperasiform-terapi_oksigen_lain">
            <label class="control-label col-md-4" for="postoperasiform-terapi_oksigen_lain"><?=$model->attributeLabels()['terapi_oksigen_lain']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['terapi_oksigen_lain']) ? $data['info']['terapi_oksigen_lain'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-l_mnt">
            <label class="control-label col-md-4" for="postoperasiform-l_mnt"><?=$model->attributeLabels()['l_mnt']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['l_mnt']) ? $data['info']['l_mnt'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-kulit_datang">
            <label class="control-label col-md-4" for="postoperasiform-kulit_datang"><?=$model->attributeLabels()['kulit_datang']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['kulit_dtg']) ? $data['info']['kulit_dtg'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <?php 
    $class = 'hidden';
    if(isset($data['info']['kulit_dtg']) && strtolower($data['info']['kulit_dtg']) == 'lain-lain'){
        $class = '';
    }
    ?>
    <div class="col-md-4 hidden unhider-kulitdatang">
        <div class="form-group field-postoperasiform-kulit_datang_lain">
            <label class="control-label col-md-4" for="postoperasiform-kulit_datang_lain"><?=$model->attributeLabels()['kulit_datang_lain']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['kulit_datang_lain']) ? $data['info']['kulit_datang_lain'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-kulit_keluar">
            <label class="control-label col-md-4" for="postoperasiform-kulit_keluar"><?=$model->attributeLabels()['kulit_keluar']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['kulit_klr']) ? $data['info']['kulit_klr'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <?php 
    $class = 'hidden';
    if(isset($data['info']['kulit_klr']) && strtolower($data['info']['kulit_klr']) == 'lain-lain'){
        $class = '';
    }
    ?>
    <div class="col-md-4 <?=$class?>">
        <div class="form-group field-postoperasiform-kulit_keluar_lain">
            <label class="control-label col-md-4" for="postoperasiform-kulit_keluar_lain"><?=$model->attributeLabels()['kulit_keluar_lain']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['kulit_keluar_lain']) ? $data['info']['kulit_keluar_lain'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-sirkulasi_badan">
            <label class="control-label col-md-4" for="postoperasiform-sirkulasi_badan"><?=$model->attributeLabels()['sirkulasi_badan']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['sirkulasi_bdn']) ? $data['info']['sirkulasi_bdn'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <?php 
    $class = 'hidden';
    if(isset($data['info']['sirkulasi_bdn']) && strtolower($data['info']['sirkulasi_bdn']) == 'lain-lain'){
        $class = '';
    }
    ?>
    <div class="col-md-4 <?=$class?>">
        <div class="form-group field-postoperasiform-sirkulasi_badan_lain">
            <label class="control-label col-md-4" for="postoperasiform-sirkulasi_badan_lain"><?=$model->attributeLabels()['sirkulasi_badan_lain']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['sirkulasi_badan_lain']) ? $data['info']['sirkulasi_badan_lain'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-area_luka">
            <label class="control-label col-md-4" for="postoperasiform-area_luka"><?=$model->attributeLabels()['area_luka']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['area_luka']) ? $data['info']['area_luka'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-is_skrining_nyeri">
            <label class="control-label col-md-4" for="postoperasiform-is_skrining_nyeri"><?=$model->attributeLabels()['is_skrining_nyeri']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['is_skrining_nyeri']) ? $data['info']['is_skrining_nyeri'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-ket_skrining">
            <label class="control-label col-md-4" for="postoperasiform-ket_skrining"><?=$model->attributeLabels()['ket_skrining']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['ket_skrining']) ? $data['info']['ket_skrining'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-skala_nyeri">
            <label class="control-label col-md-4" for="postoperasiform-skala_nyeri"><?=$model->attributeLabels()['skala_nyeri']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['skala_nyeri']) ? $data['info']['skala_nyeri'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-lokasi">
            <label class="control-label col-md-4" for="postoperasiform-lokasi"><?=$model->attributeLabels()['lokasi']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['lokasi']) ? $data['info']['lokasi'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-metode_nyeri">
            <label class="control-label col-md-4" for="postoperasiform-metode_nyeri"><?=$model->attributeLabels()['metode_nyeri']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['metod_nyeri']) ? $data['info']['metod_nyeri'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-resiko_jatuh">
            <label class="control-label col-md-4" for="postoperasiform-resiko_jatuh"><?=$model->attributeLabels()['resiko_jatuh']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['resiko_jatuh']) ? $data['info']['resiko_jatuh'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-barang_pasien">
            <label class="control-label col-md-4" for="postoperasiform-barang_pasien"><?=$model->attributeLabels()['barang_pasien']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['barang_pasien']) ? $data['info']['barang_pasien'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-4">
        <div class="form-group field-postoperasiform-is_pasanginfus">
            <label class="control-label col-md-4" for="postoperasiform-is_pasanginfus"><?=$model->attributeLabels()['is_pasanginfus']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['is_pasanginfus']) ? $data['info']['is_pasanginfus'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<?php 

$pasanginfusYa = '';

if(isset($data['info']['is_pasanginfus']) && strtolower($data['info']['is_pasanginfus']) == 'tidak'){
    $pasanginfusYa = 'hidden';
}

?>
<div class="pasanginfus-ya <?=$pasanginfusYa?>">
    <div class="row" style="padding: 5px">
        <div class="col-md-12">
            <br>
            <table id="table-pemasangan-infus" class="table table-striped table-condensed " style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th><?=Yii::t('fe', 'Jenis cairan infus')?></th>
                        <th><?=Yii::t('fe', 'Tanggal pemasangan')?></th>
                        <th><?=Yii::t('fe', 'Jumlah tetesan')?></th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-6">
        <div class="form-group field-postoperasiform-pemberitahu_perawat">
            <label class="control-label col-md-4" for="postoperasiform-pemberitahu_perawat"><?=$model->attributeLabels()['pemberitahu_perawat']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['pemberitahu_perawat']) ? date('d M Y H:i:s', strtotime($data['info']['pemberitahu_perawat'])) : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group field-postoperasiform-perawat_datang">
            <label class="control-label col-md-4" for="postoperasiform-perawat_datang"><?=$model->attributeLabels()['perawat_datang']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['perawat_datang']) ? date('d M Y H:i:s', strtotime($data['info']['perawat_datang'])) : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding: 5px">
    <div class="col-md-8">
        <div class="form-group field-postoperasiform-keterangan">
            <label class="control-label col-md-3" for="postoperasiform-keterangan"><?=$model->attributeLabels()['keterangan']?></label>
            <div class="col-md-6">
                <b><?=isset($data['info']['keterangan_post']) ? $data['info']['keterangan_post'] : '';?></b>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>

<?php 

$this->registerJs("
    var _id = '".$id."'
    var _tablepemasanganinfus;
    $(document).ready(function(){
        $('.stepy-finish').addClass('hidden')
        _tablepemasanganinfus = $('#table-pemasangan-infus').docoTabel({
            filter: false,
            displayLength: 10,
            paging: false,
            processing: true,
            serverSide: true,
            info: false,
            ajax: baseUrl+'bedah/informasi-pasien-operasi/get-view?type=pasanginfus&id='+_id,
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Jenis cairan infus',
                    data: 'obatalkes_nama',
                },
                {
                    title: 'Tanggal pemasangan',
                    data: 'tgl_pemasangan',
                },
                {
                    title: 'Jumlah tetesan',
                    data: 'jumlah_tetes',
                },
            ],
        });
    })
    ", View::POS_END, 'js');

?>
