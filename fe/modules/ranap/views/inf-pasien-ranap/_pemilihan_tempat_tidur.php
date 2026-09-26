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
        <?php foreach($getWarnaTempatTidur['warna_tempat_tidur'] as $key =>$val):?>
            <div class="col-md-2">
                <div class="square" style="background-color:<?php echo $val['kode_warna']?>"></div>
                <h6><?php echo $val['kettempattidur_nama']?></h6>
            </div>
        <?php endforeach ?>
    </div>
    <div class="row">
        <table id="example" class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%">
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
        let patternAction = link.match(/pemesanan-kamar/g);
        let jk = ".$jk.";
        const jenisTempatTidur = $(identifier).data('kettempattidur_id');
        const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
        const kamarruangan_id = $(identifier).data('kamarruangan_id');
        var jk_kamar;
        var allow_jk;
        var attr = $(identifier).data('allow_jk');
        if (typeof attr !== typeof undefined && attr !== false) {
            allow_jk = $(identifier).data('allow_jk');
        }
        
        
        if (parseInt(jenisTempatTidur) == 1) { 
            jk_kamar = 16;
        } else if (parseInt(jenisTempatTidur) == 2) {
            jk_kamar = 15;
        } else {
            jk_kamar = jk;
        }
        if (allow_jk && allow_jk != jk) {
            docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar fleksibel tidak sesuai dengan jenis kelamin'));
            return false;
        }
        if (jk != jk_kamar) {
            docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
            return false;
        } else {
            $('#modal_backdrop').modal('hide');
        }
        $(document).find('#kamartempattidur_id').val($(identifier).data('kamartempattidur_id'));
        $(document).find('#kamarruangan_id').val($(identifier).data('kamarruangan_id'));
        $(document).find('#nokamar').val($(identifier).data('kamarruangan_nokamar') + ' - ' + $(identifier).data('no_tempattidur'));
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
            ajax: baseUrl+'ranap/end-point/get-data-kamar?ruangan_id=".$ruangan_id."&jenis_id=".$jenis_id."&kelas_id=".$kelas_id."',
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
            $('.table-pilih-kamar .sorting').trigger('click');
        }, 100);
    });

        ", View::POS_END, 'js-kuning');

?>