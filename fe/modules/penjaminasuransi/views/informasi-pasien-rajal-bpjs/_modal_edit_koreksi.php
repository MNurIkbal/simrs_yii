<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$disabled = false;
$state = ArrayHelper::getValue($diagnosa, 'state', false);
$statusKunjunganId = ArrayHelper::getValue($info, 'status_kunjungan_id');
$noRekamMedik = ArrayHelper::getValue($info, 'no_rekammedik');
$noPendaftaran = ArrayHelper::getValue($info, 'no_pendaftaran');
$namaPasien = ArrayHelper::getValue($info, 'nama_pasien');

if(!$state){
    $disabled = true;
}

$disableKoreksi = false;
if ($statusKunjunganId == 551) {
    $disableKoreksi = true;
}

$panel_info = '( ';
$panel_info .= $noRekamMedik;
$panel_info .= $noPendaftaran;
$panel_info .= $namaPasien;
$panel_info .= ' )';

?>
<style type="text/css">
    .tbl-koreksi tbody tr td {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
    }
    .select2-container .select2-selection--single{
        height: 100% !important;
        padding-right: 10px !important;
    }
    .select2-selection__rendered{
      word-wrap: break-word !important;
      text-overflow: inherit !important;
      white-space: normal !important;
    }
    .have-update{
        background: #b5e4b5 !important;
    }
    .padding-0{
        padding: 0px!important;
    }
    .btn-xsm{
        padding: 3px 6px !important;
    }
    p.dpjp{
        padding-left: 10px;
    }
    .info-pasien {
        display: none;
    }
    .panel-expandable {
        cursor: pointer;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="panel-toolbar clearfix">
       
    </div>
    <div class="panel-body">
    <div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'simpanKoreksi'=> [
                        'title'=>\Yii::t('fe', 'Simpan Koreksi'),
                        'icon'=>'fa fa-save',
                        'attributes'=>[
                            'id'   => 'btn-koreksi',
                            'data-options'=>'click',
                            'data-target'=>'form-koreksi-diagnosa',
                        ]
                    ]
                ]);?>
            </div>
            <div class="panel-body">
                <br>
                <!-- Info pasien and detail start here -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default" id="panel-info-pasien">
                            <div class="panel-heading panel-expandable">
                                <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien'); ?></b> <b class="panel-info"><?= $panel_info; ?></b></h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse" class="rotate-180"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body info-pasien">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Rekam Medik") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp;<?= $noRekamMedik ?> </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Lahir") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp;<?= isset($info['tgl_lahir']) ? date('d M Y', strtotime($info['tgl_lahir'])) : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Registrasi") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp;<?= isset($info['tgl_pendaftaran']) ? date('d M Y', strtotime($info['tgl_pendaftaran'])) : '-' ?>  </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Umur") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp; <?= isset($info['umur']) ? $info['umur'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Registrasi") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp;<?= isset($info['no_pendaftaran']) ? $info['no_pendaftaran'] : '-' ?>  </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter Pemeriksa") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp; <?= isset($info['dokter_nama']) ? $info['dokter_nama'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama pasien") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp;<?= isset($info['nama_pasien']) ? $info['nama_pasien'] : '-' ?>  </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kelas pelayanan") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp; <?= isset($info['kelas_nama']) ? $info['kelas_nama'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kelamin") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp;<?= isset($info['jenis_kelamin']) ? $info['jenis_kelamin'] : '-' ?>  </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara bayar") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp; <?= isset($info['carabayar_nama']) ? $info['carabayar_nama'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kasus Penyakit") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp; <?= isset($info['jeniskasuspenyakit_nama']) ? $info['jeniskasuspenyakit_nama'] : '-' ?> </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Penjamin") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp; <?= isset($info['penjamin_nama']) ? $info['penjamin_nama'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Ruangan") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp;<?= isset($info['ruangan_nama']) ? $info['ruangan_nama'] : '-' ?>  </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kasus penyakit") ?></b></label>
                                                <div class="col-sm-7">
                                                    <p><b>:</b>&nbsp;<?= isset($info['jeniskasuspenyakit_nama']) ? $info['jeniskasuspenyakit_nama'] : '-' ?>  </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-7">
                                                <label class="text-left control-label col-sm-4"><b><?= Yii::t("fe", "Dokter penanggung jawab") ?></b></label>
                                                <div class="col-sm-8">
                                                    <p class="dpjp"><b>:</b>&nbsp;<?= isset($info['dokter_nama']) ? $info['dokter_nama'] : '-' ?>  </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <?php 
                                        $filename = isset($info['photopasien']) ? !empty($info['photopasien']) ? '/media/img/pasien/'.$info['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                        ?>
                                        <?=Html::img($filename, ['style'=>'height: 150px;margin: 5px auto', 'class'=>'img-responsive'])?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'form-koreksi-diagnosa', 
                        'action' => "/penjamin-asuransi/informasi-pasien-rajal-bpjs/simpan-koreksi?id={$id}",
                        'enableAjaxValidation'=>false, 
                        'enableClientValidation'=>false,
                        'type' => ActiveForm::TYPE_VERTICAL,
                        'formConfig' => [
                            'labelSpan' => 3, 
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                        ]
                    ]); 
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading panel-expandable">
                                <h6 class="panel-title"><b><?= Yii::t('fe', 'Koreksi Diagnosa'); ?></b></h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div id="error_koreksi_diagnosa"></div>
                                        <table id="tbl-koreksi" class="table table-striped table-condensed table-hover tbl-koreksi" style="width:100%">
                                            <thead>
                                                <tr class="bg-inverse">
                                                    <th width="1">No</th>
                                                    <th><?=\Yii::t("fe", "Jenis Diagnosa");?></th>
                                                    <th><?=\Yii::t("fe", "Nama Diagnosa");?></th>
                                                    <th><?=\Yii::t("fe", "Koreksi Diagnosa");?></th>
                                                    <th></th>
                                                    <th><?=\Yii::t("fe", "INACBGS");?></th>
                                                    <th><?=\Yii::t("fe", "ICD Primary");?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php  
                                                    $no = 1;
                                                    $idTr = 1;

                                                    foreach ($diagnosa['detail'] as $key => $value) { 
                                                        $textIcd = isset($value['text']) ? $value['text'] : null; 
                                                        $kelompok = isset($diagnosa['mapping'][$key]) ? $diagnosa['mapping'][$key] : null;
                                                        $idIcd = isset($value['id']) ? $value['id'] : null; 
                                                        $default = $idIcd ? [$idIcd => $textIcd] : [];

                                                        if (isset($diagnosa['hasil_diagnosa'][$kelompok.'-'.$textIcd])) {
                                                            $default = $diagnosa['hasil_diagnosa'][$kelompok.'-'.$textIcd];  
                                                        }

                                                        $dataType = ($key == 'Tindakan/Operasi') ? 'ICD IX' : 'ICD X';
                                                        if($key != 'Utama') { 
                                                            $penyerta = isset($diagnosa['mapping']['Terapi']) ? $diagnosa['mapping']['Terapi'] : null;
                                                            $kelompok = ($key == 'Tindakan/Operasi') ? $penyerta : null;
                                                            if(is_array($value)) {
                                                                foreach ($value as $k => $v) {

                                                                    $textIcd = isset($v['text']) ? $v['text'] : null;
                                                                    $diagnosa_lama = isset($v['diagnosa_lama']) ? $v['diagnosa_lama'] : null;;
                                                                    $idIcd = isset($v['id']) ? $v['id'] : null;
                                                                    $default = isset($v['id']) ? [$idIcd => $textIcd] : [null => '--Pilih--'];
                                                                    
                                                                    $checkbox = (isset($v['is_inacbg']) && $v['is_inacbg']) ? 'checked' : '';
                                                                    if ($checkbox == 'checked') {
                                                                        if ($v['is_icdprimer']) {
                                                                            $radio = 'checked';
                                                                        } else {
                                                                            $radio = '';
                                                                        }
                                                                    }else {
                                                                        $radio = 'disabled';
                                                                    }

                                                                    if (isset($diagnosa['hasil_diagnosa'][$kelompok.'-'.$textIcd])) {
                                                                        $default = $diagnosa['hasil_diagnosa'][$kelompok.'-'.$textIcd];
                                                                    } else if (isset($diagnosa['hasil_diagnosa'][$kelompok.'-'.$k.' - '.$textIcd])) {
                                                                        $default = $diagnosa['hasil_diagnosa'][$kelompok.'-'.$k.' - '.$textIcd];
                                                                    }

                                                                    if (!$v) {
                                                                        $checkbox = 'disabled';
                                                                        $radio = 'disabled';
                                                                    }
                                                                    if($k == 0) { ?>
                                                                        <tr class="tr-diagnosa-<?= $kelompok ?>">
                                                                            <td width="1%" rowspan="<?= count($value) ?>" class="diagnosa-<?= $kelompok?>"><?= $no++ ?></td>
                                                                            <td width="1%" class="text-center diagnosa-<?= $kelompok?>" rowspan="<?= count($value) ?>"><?= $key ?></td>
                                                                            <td width="20%" class="diagnosa-nama"><?= $diagnosa_lama; ?></td>
                                                                            <td width="30%">
                                                                                <?= Html::dropDownList('koreksi_diagnosa[]', $idIcd, $default, [
                                                                                    'class' => 'koreksi-diagnosa',
                                                                                    'data-type' => $dataType,
                                                                                    'data-kelompok' => $kelompok,
                                                                                    'data-icd' => $idIcd,
                                                                                    'data-asal' => ($idIcd ? $textIcd : $k . ' - ' . $textIcd)
                                                                                ]) ?>
                                                                            </td>
                                                                             <td width="4%" class="padding-0">
                                                                                <?php if($key != 'Masuk'){?>
                                                                                    <button type="button" id="add-diagnosa-<?= $kelompok?>" class="btn btn-success btn-xsm"><i class="fa fa-plus"></i></button>
                                                                                    <button type="button" class="btn btn-danger btn-xsm diagnosa-reset"><i class="fa fa-trash"></i></button>
                                                                                <?php }?>
                                                                            </td>
                                                                            <td>
                                                                                <input type='checkbox' class='check-inacbg validate-update' name='FormKoreksi[is_inacbg][<?=$idIcd?>]' <?= $checkbox;?>>
                                                                            </td>
                                                                            <td>
                                                                                <?php if ($dataType !== 'ICD IX') { ?>
                                                                                    <input type='radio'  value='<?=$idIcd?>' class='radio-icdprimer validate-update' name='FormKoreksi[is_icdprimer]' data-type='<?= $dataType; ?>' data-kelompok='<?=$kelompok;?>' data-icd='<?= $idIcd; ?>' <?= $radio;?>>
                                                                                <?php } ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } else { ?>
                                                                        <tr class="tr-diagnosa-<?= $kelompok ?>">
                                                                            <td width="20%" class="diagnosa-nama"><?= $diagnosa_lama; ?></td>
                                                                            <td width="30%">
                                                                                <?= Html::dropDownList('koreksi_diagnosa[]', $idIcd, $default, [
                                                                                    'class' => 'koreksi-diagnosa',
                                                                                    'data-type' => $dataType,
                                                                                    'data-kelompok' => $kelompok,
                                                                                    'data-icd' => $idIcd,
                                                                                    'data-asal' => ($idIcd ? $textIcd : $k . ' - ' . $textIcd)
                                                                                ]) ?>
                                                                            </td>
                                                                            <td width="1%">
                                                                                <button type="button" class="btn btn-danger btn-xsm remove-diagnosa-<?= $kelompok?>"><i class="fa fa-trash"></i></button>
                                                                            </td>
                                                                            <td>
                                                                                <input type='checkbox' class='check-inacbg validate-update' name='FormKoreksi[is_inacbg][<?=$idIcd?>]' <?= $checkbox;?>>
                                                                            </td>
                                                                            <td>
                                                                                <input type='radio' value='<?=$idIcd?>' class='radio-icdprimer validate-update' name='FormKoreksi[is_icdprimer]' data-type='<?= $dataType; ?>' data-kelompok='<?=$kelompok;?>' <?= $radio;?>>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        <?php }else { 
                                                                $checkbox = (isset($value['is_inacbg']) && $value['is_inacbg']) ? 'checked' : '';
                                                                if ($checkbox == 'checked') {
                                                                    if ($value['is_icdprimer']) {
                                                                        $radio = 'checked';
                                                                    } else {
                                                                        $radio = '';
                                                                    }
                                                                }else {
                                                                    $radio = 'disabled';
                                                                }
                                                            ?>
                                                            <tr>
                                                                <td width="1%"><?= $no++ ?></td>
                                                                <td width="1%"class="text-center"><?= $key ?></td>
                                                                <td width="20%" class="diagnosa-nama"><?= isset($value['diagnosa_lama']) ? $value['diagnosa_lama'] : ''; ?></td>
                                                                <td width="30%">
                                                                    <?= Html::dropDownList('koreksi_diagnosa[]', $idIcd, $default, [
                                                                    'class' => 'koreksi-diagnosa',
                                                                    'data-type' => $dataType,
                                                                    'data-kelompok' => $kelompok,
                                                                    'data-icd' => $idIcd,
                                                                    'data-asal' => $textIcd
                                                                    ]) ?>
                                                                </td>
                                                                <td width="1%">
                   
                                                                </td>
                                                                <td width="1%">
                                                                    <input type='checkbox' class='check-inacbg validate-update' name='FormKoreksi[is_inacbg][<?=$idIcd?>]' <?= $checkbox;?>>
                                                                </td>
                                                                <td width="1%"> 
                                                                    <input type='radio' value='<?=$idIcd?>'  class='radio-icdprimer validate-update' name='FormKoreksi[is_icdprimer]' data-type='<?= $dataType; ?>' data-kelompok='<?=$kelompok;?>' <?= $radio;?>>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>   
                                                    <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    ActiveForm::end();
                ?>
            </div>
        </div>
    </div>
</div>

    </div>
</div>
<div class="modal-footer text-left">
    
</div>

<?php 

$this->registerJs("
    var table;
    var _id = '{$id}';
    var updateStatus = false;
    var disableEdit = '{$disableKoreksi}';

    $(document).ready(function(){
        var _detail = ". json_encode($diagnosa['detail']) .";
        var _disable = '{$disabled}';
        var _statusKunjungan = '{$status_kunjungan}'
        
        getDiagnosa();

        if(disableEdit) {
            $('.koreksi-diagnosa').attr('disabled', true);
            $('.btn-xsm').attr('disabled', true);
            $('.check-inacbg').attr('disabled', true);
            $('.radio-icdprimer').attr('disabled', true);
        }
       
        $(document).on('click','.check-inacbg', function(){
            if($(this).is(':checked')){
                $(this).closest('tr').find('.radio-icdprimer').attr('disabled', false)
            }else{
                var key = $(this).attr('data-key');
                var radioval = $(this).closest('tr').find('.radio-icdprimer').prop('checked')
                if(radioval){
                    $('input[name=\"FormKoreksi[is_icdprimer]\"]').prop('checked', false);
                }
                $(this).closest('tr').find('.radio-icdprimer').attr('disabled', true)
            }
        })
        
        function toggleDisable(status) {
            $('.koreksi-diagnosa').attr('disabled', status);
            $('.btn-xsm').attr('disabled', status);
            $('.check-inacbg').attr('disabled', status);
            $('.radio-icdprimer').attr('disabled', status);
        }

        $(document).on('click','#btn-koreksi', function (event) {
            event.preventDefault();
            toggleDisable(false);
            var _info = ". json_encode($diagnosa['data']) .";
            var _kunjunganId = ". json_encode($diagnosa['kunjungan_id']) .";
            var _infoPasien = ". json_encode($info) .";
            var _data = $('#form-koreksi-diagnosa').serializeArray();
            // toggleDisable(true);
            _data.push({
                name : 'dokter_nama',
                value : _info.dokter_nama
            });
            
            _data.push({
                name : 'disable_edit',
                value : disableEdit ? true : false
            });    
            
            _data.push({
                name : 'kunjungan_id',
                value : _kunjunganId
            });
            var _tmp = {};
            $.each($('.koreksi-diagnosa'), function (key,val) {
                _tmp[key] = {
                    diagnosa_asal : $(this).attr('data-icd'),
                    diagnosa_id : $(this).val(),
                    diagnosa_text : $(this).attr('data-asal'),
                    diagnosa_kelompok : $(this).attr('data-kelompok'),
                };
            });
            _data.push({
                name : 'data_koreksi',
                value : JSON.stringify(_tmp)
            });
            _data.push({
                name : 'total_data',
                value : $('.koreksi-diagnosa').length
            });
            _data.push({
                name : 'dokterdpjp_id',
                value : _infoPasien.dokter_kode
            });
            _data.push({
                name : 'pasien_id',
                value : _info.pasien_id
            });

            _data.push({
                name : 'edit_koreksi',
                value : true
            });

            $(this).docoForm('click',{
                // skipConfirm: true,
                data : _data,
                url : $('#form-koreksi-diagnosa').attr('action'),
                success: function (res) {
                    if(res.metadata.status == 200) {
                        window.location.reload()
                    }
                }
            });
        });

        $('.panel-expandable').on('click', function(e) {
            $(this).next().slideToggle();
            if($(this).find('.rotate-180').length !== 0) {
                $(this).find('.icons-list li a').removeClass('rotate-180');
            } else {
                $(this).find('.icons-list li a').addClass('rotate-180');
            }
            if($(this).next().filter('.info-pasien').length !== 0) {
                $('.panel-info').toggle();
            }
        });
    })
    " .$this->render('_modal_edit_koreksi.js'), View::POS_END, 'js' );

?>