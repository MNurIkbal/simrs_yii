<?php

/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */

use yii\web\View;
use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
use Doco\fisioterapi\components\FisioHelper;
use app\widgets\fisioterapi\DHHeaderProgramTerapi;

$frekuensi = ArrayHelper::getValue($data, 'frekuensi');
?>

<style>
    .table-left td {
        border-left: none !important;
        border-right: none !important;
    }
    .table-left th {
        border: none !important;
    }
    .table-right td {
        border-left: none !important;
        border-right: none !important;
    }
    .table-right th {
        border: none !important;
    }
    .modal-body {
        margin-top: -10px;
    }
    .content-right {
        margin-bottom : 5px;
    }
    .content-right 
    .dataTables_wrapper 
    .dataTables_scroll {
        overflow-x: hidden;
    }
    .content-right 
    .dataTables_wrapper 
    .dataTables_scroll {
        border: 0.1px solid #bbb;
    }
    .kv-datetime-remove {
        display: none !important;
    }
    .update-jadwal {
        float: right;
        margin-top: 14px;
        margin-right: -6px;
    }
    .mb-5px {
        margin-bottom: 5px;
    }
    .d-none {
        display: none;
    }
    .d-inline {
        display: inline;
    }.picker_modal-one{
        bottom: 35px !important;
    }.picker__button--clear{
     display: none !important;
    }#modalProgramTerapi > .modal-dialog{
        width: 70%;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Detail Program Terapi</h5>
</div>

<div class="modal-body">
    <?php
        $form = ActiveForm::begin([
            'id' => 'update-jadwal-form',
            'enableClientValidation' => false,
            'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
        ]);
    ?>
    <?= 
        DHHeaderProgramTerapi::widget([
            'id' => $id,
            'data' => $data,
            'scheduledetailDoctor' => $scheduledetailDoctor,
        ]);
    ?>

    <hr>

    <!-- Daftar Terapi -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title" style="font-weight: 600;">Daftar Terapi</h5>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-left">
                <thead>
                    <tr class="bg-inverse">
                        <th width="30%">Terapi</th>
                        <th width="5%">Qty.</th>
                        <th width="30%">Kategori</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($detail)): ?>
                        <?php foreach ($detail as $key => $value): ?>
                            <tr>
                                <?php
                                    $qtyPemeriksaan = ArrayHelper::getValue($value, 'qty_pemeriksaan', 1);
                                    $qtyPemeriksaan = $qtyPemeriksaan ? $qtyPemeriksaan : 1;
                                    $isPaket = ArrayHelper::getValue($value, 'is_paketfisio');
                                    if ($isPaket == true) {
                                        $parent = ArrayHelper::getValue($value, 'daftartindakan_parent');
                                        $child = ArrayHelper::getValue($value, 'terapi');
                                        $merge = $parent.' ('.$child.')';
                                        echo '<td><span>'.htmlspecialchars($merge).'</span></td>';
                                    } else {
                                        echo '<td><span>'.htmlspecialchars(ArrayHelper::getValue($value, 'terapi')).'</span></td>';
                                    }
                                ?>
                                <td><?= $qtyPemeriksaan ?></td>
                                <td><span><?= htmlspecialchars(ArrayHelper::getValue($value, 'kategori')) ?></span></td>
                                <td><?= htmlspecialchars(ArrayHelper::getValue($value, 'catatan')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3"><center>Data Kosong</center></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Jadwal Terapi -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title" style="font-weight: 600;">Jadwal Terapi</h5>
            <span style="color: red; font-size: 10px;">* Pengisisan harus dilakukan secara berurutan</span>
        </div>
        <div class="panel-body">
            <div class="text-center" id="loading-schedule"></div>
            <div style="float:right" class="lihat-jadwal-wrap">
                <button  data-target="#modal-order-pemeriksaan" type="button" class="btn btn-info" data-btntrigger="modal" action="/fisioterapi/informasi-jadwal-terapi/jadwal-fisioterapi?pegawai_id=<?= ArrayHelper::getValue($data_penjadwalan, '0.pegawai_id') ?>" data-toggle="modal" data-options="modal" class="btn-transparent btn-jadwal-list" style="margin-bottom:10px;">
                Lihat jadwal
                </button>
            </div>
            <table class="table table-hover" id="tbl-schedule" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th style='width: 1px'>No</th>
                        <th width="25%">Tanggal</th>
                        <th>Estimasi Jam Mulai</th>
                        <th>Estimasi Jam Selesai</th>
                        <th width="20%">Realisasi</th>
                        <th width="30%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $fieldName = "schedule";
                    $no = 1;
                    $i = 0;
                    $status = ArrayHelper::getValue($scheduledetailDoctor, 'status_program_fisio_nama');
                    $caraBayar = ArrayHelper::getValue($scheduledetailDoctor, 'carabayar_id');
                    $bayar = ArrayHelper::getValue($scheduledetailDoctor, 'bayar');
                    $is_paket = ArrayHelper::getValue($scheduledetailDoctor, 'is_paket');
                    $class = 'form-control';
                    $btnClass = '';

                    // Pengkondisian disabled ubah jadwal program
                    $paramIsDisabledSetSchedule = [
                        'status' => $status,
                        'is_paket' => $is_paket,
                        'caraBayar' => $caraBayar,
                        'bayar' => $bayar
                    ];
                    $isDisabled = FisioHelper::isDisabledSetScheduleProgram($paramIsDisabledSetSchedule);
                    if ($isDisabled == true) {
                        $class .= ' disabled';
                        $btnClass .= ' disabled';
                    }

                    foreach ($data_penjadwalan as $jadwal):
                        $strTglAwal = null;
                        $fieldName = "schedule_details";
                        $realisasi = ArrayHelper::getValue($jadwal, 'realisasi');
                        $statusKunjunganId = ArrayHelper::getValue($jadwal, 'status_kunjungan_id');
                        $keteranganDropOut = ArrayHelper::getValue($jadwal, 'keterangan_drop_out');
                        $number = $i + 1;
                        $dateTimePicker = Html::textInput("tanggal-$i", null,[
                            'class' => 'input-sm pickadate-input '.$class,
                            'id' => "schedule-$i",
                            'data-jadwalId' => ArrayHelper::getValue($jadwal, 'jadwal_id'),
                            'data-kunjunganKe' => ArrayHelper::getValue($jadwal, 'kunjunganke'),
                            'data-pegawaiId' => ArrayHelper::getValue($jadwal, 'pegawai_id'),
                            'data-ke' => "$i"
                        ]);
                        $jamMulai = Html::textInput("jam_mulai-$i", null,[
                            'class' => 'input-sm jam_mulai '.$class,
                            'readonly' => true,
                            'id' => "jamMulai-$i",
                            'data-ke' => "$i"
                        ]);

                        $jamSelesai = Html::textInput("jam_selesai-$i", null,[
                            'class' => 'input-sm jam_selesai '.$class,
                            'readonly' => true,
                            'id' => "jamSelesai-$i",
                            'data-ke' => "$i"
                        ]);
                        ?>
                        <tr style="<?= $jadwal['style'] ?>">
                        <td><?= $number ?></td>
                        <td><div class='input-group' style='width:100%'><?= $dateTimePicker ?></div></td>
                        <td><div class='input-group' style='width:100%'><?= $jamMulai ?></div></td>
                        <td><div class='input-group' style='width:100%'><?= $jamSelesai ?></div></td>
                        <td id='<?= "statusKunjunganId-$i" ?>'><?= $jadwal['btnJadwal'] ?></td>
                        <td>
                            <span class="input-keterangan-drop-out-<?= $i ?>">
                                <?= Html::textarea('alasan_dropout', htmlspecialchars($keteranganDropOut), [
                                    'id' => "alasan-dropout-$i",
                                    'class' => "form-control mb-5px d-none"
                                ]) ?>
                            </span>
                        </td>
                        </tr>
                        <?php
                        Html::hiddenInput('id', $id, [
                            'id' => 'programterapi_id'
                        ]);
                        $no++;
                        $i++;
                        endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 content-right">
            <?php ActiveForm::end(); ?>
            <div class="update-jadwal">
                <button type="button" id="btn-update-jadwal" class="btn btn-info btn-labeled btn-xs btn-custom-save<?= $btnClass ?>"><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
            </div>
        </div>
    </div>
</div>

<?php
    $phpVars = [
        'maksFrekuensi' => $frekuensi,
        'dataPerjadwalan' => $data_penjadwalan,
        'fieldName' => $fieldName,
        'maxKeteranganDropOut' => $maxKeteranganDropOut,
    ];
    $this->registerJsVar('phpVars', $phpVars);
    $this->registerJs($this->render('js/detail.js'), View::POS_END);
?>