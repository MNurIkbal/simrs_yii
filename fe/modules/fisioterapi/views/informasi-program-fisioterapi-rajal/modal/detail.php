<?php
    use yii\web\View;
    use yii\helpers\Html;
    use app\components\DocoHelpers;
    use kartik\widgets\DateTimePicker;
    use app\widgets\fisioterapi\DHHeaderProgramTerapi;
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
        margin-bottom : 20px;
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
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Detail Program Terapi</h5>
</div>
<div class="modal-body">
    <?php 
        echo DHHeaderProgramTerapi::widget([
            'id' => $id,
            'data' => $data,
            'scheduledetailDoctor' => $scheduledetailDoctor,
        ]);
    ?>
    <hr style="margin-top: 0px;">
    <div class="row">
        <div class="col-sm-7">
            <h5 class="modal-title" style="margin-bottom: 10px; font-weight: 600;">Daftar Terapi</h5>
            <table class="table table-bordered table-left" width="1200%">
                <thead>
                    <tr class="bg-inverse">
                        <th>Terapi</th>
                        <th>Kategori</th>
                        <th width="300">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($detail)): ?>
                        <?php foreach ($detail as $key => $value): ?>
                            <tr>
                                <td><?= $value['terapi'] ?></td>
                                <td><?= $value['kategori'] ?></td>
                                <td><?= $value['catatan'] ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3"><center>Data Kosong</center></td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-5 content-right">
            <h5 class="modal-title" style="margin-bottom: 10px; font-weight: 600;">Jadwal Terapi</h5>
            <table id="detail" class="table table-bordered table-right" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th>Penjadwalan</th>
                        <th>Realisasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['frekuensi'])): ?>
                        <?php $no = $kunjunganke = 1; ?>
                            <?php for ($x = 0; $x < $data['frekuensi']; $x++) : 
                                if ($x < count($terapi_schedule_fisio)){
                                    $tanggalPenjadwalan = $terapi_schedule_fisio[$x]['tgl_penjadwalan'];
                                    $idjadwal = $terapi_schedule_fisio[$x]['jadwalterapifisio_id'];
                                    $tanggalRealisasi = $terapi_schedule_fisio[$x]['tgl_realisasi'];
                                    $is_active = $terapi_schedule_fisio[$x]['is_active'];
                                } else {
                                    $tanggalPenjadwalan = '';
                                    $tanggalRealisasi = '-';
                                    $is_active = '';
                                    $idjadwal = '';
                                }
                            ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <?php if($tanggalPenjadwalan != '' && !is_null($tanggalPenjadwalan) && $tanggalRealisasi != '' && !is_null($tanggalPenjadwalan)) : ?>
                                    <td>
                                        <div class="form-group" >
                                            <?= DateTimePicker::widget([
                                                'name' => 'date',
                                                'type' => DateTimePicker::TYPE_INPUT,
                                                'value' => ($tanggalPenjadwalan != '' && !is_null($tanggalPenjadwalan)) ? date("d-M-Y", strtotime($tanggalPenjadwalan)) : $tanggalPenjadwalan,
                                                'options' => ['placeholder' => ''],
                                                'readonly' => true,
                                                'disabled' => (!empty($tanggalPenjadwalan) && $is_active == TRUE) ? true : false,
                                                'pluginOptions' => [
                                                    'format' => 'dd-M-yyyy',
                                                    'todayHighlight' => true,
                                                    'autoclose' => true,
                                                    'minView' => 2,
                                                ],
                                                'pluginEvents' =>[
                                                "changeDate" => "function(e) {
                                                    var dataDate = e.date.toLocaleString('en-US');
                                                    var id = $('#programterapi_id').val();
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: '/fisioterapi/informasi-program-fisioterapi/save?id='+id,
                                                        data: { 
                                                            date: dataDate,
                                                            kunjunganke: ".$kunjunganke."
                                                        },
                                                        success: function(data)
                                                        {
                                                            docoNotification('success', i18next.t('Berhasil'), i18next.t('Berhasil menambah jadwal terapi untuk nama pasien <b>".$data['nama_pasien']."</b> dengan no rekam medik <b>".$data['no_rekam_medik']."</b>'));
                                                        },
                                                        error: function(data){
                                                            alert('Gagal Disimpan')
                                                        }
                                                    });  
                                                }",
                                            ]    
                                            ]); 
                                            ?>
                                        </div>
                                    </td>
                                    <?php elseif($tanggalPenjadwalan == '') : ?>
                                    <td>
                                        <div class="form-group" >
                                            <?= DateTimePicker::widget([
                                                'name' => 'date',
                                                'value' => ($tanggalPenjadwalan != '' && !is_null($tanggalPenjadwalan)) ? date("d-M-Y", strtotime($tanggalPenjadwalan)) : $tanggalPenjadwalan,
                                                'options' => ['placeholder' => ''],
                                                'readonly' => true,
                                                'disabled' => (!empty($tanggalPenjadwalan) && $is_active == TRUE) ? true : false,
                                                'pluginOptions' => [
                                                    'format' => 'dd-M-yyyy',
                                                    'todayHighlight' => true,
                                                    'autoclose' => true,
                                                    'minView' => 2,
                                                ],
                                                'pluginEvents' =>[
                                                "changeDate" => "function(e) {
                                                    var dataDate = e.date.toLocaleString('en-US');
                                                    var id = $('#programterapi_id').val();
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: '/fisioterapi/informasi-program-fisioterapi/save?id='+id,
                                                        data: { 
                                                            date: dataDate,
                                                            kunjunganke: ".$kunjunganke."
                                                        },
                                                        success: function(data)
                                                        {
                                                            docoNotification('success', i18next.t('Berhasil'), i18next.t('Berhasil menambah jadwal terapi untuk nama pasien <b>".$data['nama_pasien']."</b> dengan no rekam medik <b>".$data['no_rekam_medik']."</b>'));
                                                        },
                                                        error: function(data){
                                                            alert('Gagal Disimpan')
                                                        }
                                                    });  
                                                }",
                                            ]    
                                            ]); 
                                            ?>
                                        </div>
                                    </td>
                                    <?php else : ?>
                                    <td>
                                        <div class="form-group" >
                                            <?= DateTimePicker::widget([
                                                'name' => 'date', 
                                                'value' => ($tanggalPenjadwalan != '' && !is_null($tanggalPenjadwalan)) ? date("d-M-Y", strtotime($tanggalPenjadwalan)) : $tanggalPenjadwalan,
                                                'options' => ['placeholder' => ''],
                                                'readonly' => true,
                                                'disabled' => (!empty($tanggalPenjadwalan) && $is_active == TRUE) ? true : false,
                                                'pluginOptions' => [
                                                    'format' => 'dd-M-yyyy',
                                                    'todayHighlight' => true,
                                                    'autoclose' => true,
                                                    'minView' => 2
                                                ],
                                                'pluginEvents' =>[
                                                "changeDate" => "function(e) {
                                                    var dataDate = e.date.toLocaleString('en-US');
                                                    var idJadwal = $('#jadwalfisioterapi_id').val();
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: '/fisioterapi/informasi-program-fisioterapi/update-jadwal?id=".$idjadwal."',
                                                        data: { 
                                                            date: dataDate,
                                                            kunjunganke: ".$kunjunganke."
                                                        },
                                                        success: function(data)
                                                        {
                                                            docoNotification('success', i18next.t('Berhasil'), i18next.t('Berhasil merubah jadwal terapi untuk nama pasien <b>".$data['nama_pasien']."</b> dengan no rekam medik <b>".$data['no_rekam_medik']."</b>'));
                                                            // alert(data)
                                                        },
                                                        error: function(data){
                                                            alert('Gagal Disimpan')
                                                        }
                                                    });  
                                                }",
                                            ]    
                                            ]); 
                                            ?>
                                        </div>
                                    </td>
                                    <?php endif ?>
                                        <td> <?= ($tanggalRealisasi != '-' && !is_null($tanggalRealisasi)) ? date("d-M-Y", strtotime($tanggalRealisasi)) : $tanggalRealisasi ?> 
                                    </td>
                                </tr>
                            <?php $no++;$kunjunganke++; ?>
                        <?php endfor ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
    $this->registerJs($this->render('js/detail.js'), View::POS_END);
?>