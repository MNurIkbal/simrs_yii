<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-17 10:17:42
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=  Yii::t('fe', 'Pencarian Lanjutan') ?></h5>
</div>
<div class="modal-body">
    <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'search',
            'reset',
        ], '#tb-pencarian-lanjutan') ?>
    </div>
    <hr>
    <div class="row">
        <div class="advanced-filter"></div>
    </div>
    <table id="tb-pencarian-lanjutan" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th><?= Yii::t("fe", "No") ?></th>
                <th><?= Yii::t("fe", "Info Pasien") ?></th>
                <th><?= Yii::t("fe", "Alamat") ?></th>
                <th><?= Yii::t("fe", "No. BPJS") ?></th>
                <th><?= Yii::t("fe", "Tanggal Terakhir Kunjungan") ?></th>
                <th><?= Yii::t("fe", "Aksi") ?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<?php
$this->registerJs("
    var table;

    $(document).ready(function() {
        generateFilter('', 'filter-form-pencarian-lanjutan');
        var _valAps = $('input[name=\"TipePasienForm[is_aps]\"]:checked').val();
        var _isAps = typeof _valAps != 'undefined' ? _valAps : '';
        table = $('#tb-pencarian-lanjutan').docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            order: false,
            ajax: baseUrl + 'pendaftaran/daftar/get-data-pasien?is_aps='+_isAps+'&is_bpjs={$is_bpjs}',
            columns: [
                {title: '".(\Yii::t('fe', 'No.'))."', data: 'no', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', 'Info Pasien'))."', data: 'info_pasien', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', 'Alamat'))."', data: 'alamat_pasien', searchable: false},
                {title: '".(\Yii::t('fe', 'No. BPJS'))."', data: 'nopeserta_bpjs'},
                {title: '".(\Yii::t('fe', 'Tanggal Terakhir Kunjungan'))."', data: 'tgl_pendaftaran', searchable: false},
                {title: '".(\Yii::t('fe', 'Aksi'))."', data: 'aksi', searchable: false, orderable: false},
                {title: '".(\Yii::t('fe', 'No. Rekam Medik'))."', data: 'no_rekam_medik', visible: false, orderable: false},
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."', data: 'nama_pasien', visible: false, orderable: false},
                {title: '".(\Yii::t('fe', 'Tanggal Lahir'))."', data: 'tanggal_lahir', visible: false, orderable: false},
                {title: '".(\Yii::t('fe', 'Alamat'))."', data: 'alamat_pasien', visible: false},
            ],
        });

        $('.dataTables_filter').hide();

        $('.filter-form-pencarian-lanjutan').datatableBootstrapFilter(table, [
            [8, filterTanggalLahir],
        ], {
            6:0,
            7:1,
            8:2,
            9:3,
            3:4
        });

        var pickadate_tanggal_lahir = $('#pickadate_tanggal_lahir').pickadate({
            editable: true,
            onClose: function() {
                $('.datepicker').focus();
            }
        });

        pickadate_tanggal_lahir.pickadate('picker');

        $('#modal_pencarian_lanjutan #filter_no_rekam_medik').focus();
    });

    var filterTanggalLahir = \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
        Html::textInput('tanggal_lahir', '', [
            'class' => 'form-control',
            'id' => 'pickadate_tanggal_lahir',
            'data-mask' => '99-99-9999'
        ])
    ))."</div>\";

    $(document).on('click', '#btn_pickadate_tanggal_lahir', function (event) {
        if (pickadate.get('open')) {
            pickadate.close();
        } else {
            pickadate.open();
        }
        event.stopPropagation();
    });

    function onClickBpjsPasien(ini) {
        var data = table.row($(ini).parents('tr')).data();
        var _asalRujukan = $('#asalrujukan_id').val();
        var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');
        var _pencarianBpjs = $(\"input[name='BpjsNewForm[jenis_rujukan]']:checked\").val();

        if (typeof _formPendaftaran.dataBpjs.mr != 'undefined') {
            _formPendaftaran.dataBpjs.mr.noMR = data.no_rekam_medik
            _noRm = data.no_rekam_medik
            _formPendaftaran.tipePasien.tipe_pasien = 1
            _formPendaftaran.dataReturnBpjs.pasien_baru = false
            _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm)
        }
        $('#modal_pencarian_lanjutan').modal('hide');
    }

    function onClickPilihPasien(ini) {
        var pasien_id = $(ini).data('id');
        var bpjs = $(ini).data('bpjs');
        var _parentTr = $(ini).closest('tr');
        var data = table.row($(ini).parents('tr')).data();
        var newOption = new Option(data.info_pasien.replace(/<br>/g,' / '), data.no_rekam_medik, false, false);
        
        $('#no_rekam_medik').append(newOption).trigger('change');
        $('#no_rekam_medik').val(data.no_rekam_medik).trigger('change');
        $('#modal_pencarian_lanjutan').modal('hide');

        getInfoPasien(pasien_id);
    }
", View::POS_END, 'pencarian_lanjutan');

$this->registerJs(''.$this->render('js/index.js'));
?>