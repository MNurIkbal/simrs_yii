<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Url;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'ajax-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-2">
            <div class="square red"></div>
            <h6>Isi Perempuan</h6>
        </div>
        <div class="col-md-2">
            <div class="square green"></div>
            <h6>Kosong Perempuan</h6>
        </div>
        <div class="col-md-2">
            <div class="square black"></div>
            <h6>Isi Laki-laki</h6>
        </div>
        <div class="col-md-2">
            <div class="square orange"></div>
            <h6>Kosong Laki - laki</h6>
        </div>
        <div class="col-md-2">
            <div class="square grey"></div>
            <h6>Dibersihkan</h6>
        </div>
        <div class="col-md-2">
            <div class="square purple"></div>
            <h6>Telah Dipesan</h6>
        </div>
    </div>
    <div class="row">
        <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="80">No</th>
                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                    <th><?=\Yii::t("fe", "Kamar");?></th>
                    <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                </tr>
            </thead>
            <tbody>

                <!-- <tr>
                    <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                </tr> -->
            </tbody>
        </table>
    </div>
</div>

<?php

 $this->registerJs("
        // Global Var
    var table;

    function pilihKamar(identifier){
        let link = document.URL;
        let patternAction = link.match(/pindah-kamar/g);
        let jk;
        if (patternAction) {
            if (patternAction[0] == 'pindah-kamar') {
                const isCheck = $('#infopasienranapform-jenis_kelamin').val();
                if (!isCheck) {
                    docoNotification('error', i18next.t('Perhatian'), i18next.t('Jenis kelamin belum dipilih!'));
                    $('#modal_backdrop').modal('hide');
                    return false;
                }
                jk = isCheck;
            }
        } else {
            jk = $('input[name=\'infopasienranapform[jenis_kelamin]\']').val();
            if (jk == '') {
                docoNotification('error', i18next.t('Perhatian'), i18next.t('Jenis kelamin belum dipilih!'));
                $('#modal_backdrop').modal('hide');
                return false;
            }
        }
        const jenisTempatTidur = $(identifier).data('kettempattidur_id');
        const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
        const kamarruangan_id = $(identifier).data('kamarruangan_id');
        var jk_kamar;
        
        // cek if kamar fleksible
        if  (parseInt(kamarruangan_jenis) == 342) {
            var jk_res;
            $.ajax({ 
                url: '/ranap/inf-pasien-ranap/cek-kamar-fleksibel?kamarruangan_id=' + kamarruangan_id,
                type: 'GET',
                success: function(res) {
                    if (typeof res.response.jenis_kelamin !== 'undefined') {
                        jk_res = res.response.jenis_kelamin;
                    } else {
                        jk_res = jk;
                    }
                    jk_kamar = jk_res;
                    if (jk != jk_kamar) {
                        docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
                        return false;
                    } else {
                        $('#modal_backdrop').modal('hide');
                    }
                    $(document).find('#no_tempattidur').val($(identifier).data('kamartempattidur_id'));
                    $(document).find('#kamarruangan_nokamar').val($(identifier).data('kamarruangan_id'));
                    $(document).find('#ruangan_nama').val($(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));
                }
            });
        } else {
            if (parseInt(jenisTempatTidur) == 1) { 
                jk_kamar = 'Perempuan';
            } else if (parseInt(jenisTempatTidur) == 2) {
                jk_kamar = 'Laki-laki';
            } else {
                jk_kamar = jk;
            }
            if (jk != jk_kamar) {
                docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
                return false;
            } else {
                $('#modal_backdrop').modal('hide');
            }
            $(document).find('#no_tempattidur').val($(identifier).data('kamartempattidur_id'));
            $(document).find('#kamarruangan_nokamar').val($(identifier).data('kamarruangan_id'));
            $(document).find('#ruangan_nama').val($(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));
        }
    }

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $('#example').docoTabel({
            filter: false,
            sorting: [[1, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'ranap/inf-pasien-ranap/get-data-kamar?ruangan_id=".$ruangan_id."&jenis_id=".$jenis_id."&kelas_id=".$kelas_id."',
            oLanguage: {
                sLengthMenu: '".(\Yii::t('fe', 'dt_length_menu'))."',
                sZeroRecords: '".(\Yii::t('fe', 'dt_zero_records'))."',
                sEmptyTable: '".(\Yii::t('fe', 'dt_empty_table'))."',
                sInfoFiltered: '".(\Yii::t('fe', 'dt_info_filtered'))."',
                sInfoEmpty: '".(\Yii::t('fe', 'dt_info_empty'))."',
                sInfo: '".(\Yii::t('fe', 'dt_info'))."',
                oPaginate: {
                    sFirst: '".(\Yii::t('fe', 'dt_first_page'))."',
                    sPrevious: '".(\Yii::t('fe', 'dt_previous_page'))."',
                    sNext: '".(\Yii::t('fe', 'dt_next_page'))."',
                    sLast: '".(\Yii::t('fe', 'dt_last_page'))."'
                }
            },
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_nama'},
                {title: '".(\Yii::t('fe', 'Kamar'))."', data: 'kamarruangan_nokamar'},
                {title: '".(\Yii::t('fe', 'No Tempat Tidur'))."', data: 'datakamar'},
            ]
        });

        $('.dataTables_length').hide();
        $('.dataTables_info').hide();
        $('.dataTables_paginate').hide();
        setTimeout(function () {
            $('.sorting').trigger('click');
        }, 100);
    });

        ", View::POS_END, 'js-kuning');

?>