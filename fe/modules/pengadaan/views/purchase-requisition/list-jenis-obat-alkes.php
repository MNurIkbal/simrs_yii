<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\helpers\Html;
use app\components\DocoHelpers;

?>

<?php 
$count_data_obat = count($jenisobatalkes['data_obat']);
$count_data_alkes = count($jenisobatalkes['data_alkes']);
$count_non_group = count($jenisobatalkes['non_group']);
?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">
        <?= Yii::t('fe', 'Pilih Jenis Obat Alkes') ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <div class="col-md-12">
        <div class="col-md-12" style="margin-bottom:10px">
            <?= Html::activeCheckbox($model, 'jenisobatalkes_id', [
                'id' => 'jenis-checkbox-all',
                'class' => 'check-all-jenis',
                'label' => ' <strong>Pilih Semua Jenis</strong>'
            ]) ?>
        </div>
    </div>
    <?php if($count_data_obat > 0) : ?>
        <div class="col-md-12">
            <div class="col-md-12">
                <h5>Obat</h5>
            </div>
            <div class="col-md-12" style="margin-bottom:10px">
                <?= Html::activeCheckbox($model, 'jenisobatalkes_id', [
                    'id' => 'jenis-checkbox-obat',
                    'class' => 'check-all-jenis-obat',
                    'label' => ' <strong>Pilih Semua</strong>'
                ]) ?>
            </div>
            <?php foreach($jenisobatalkes['data_obat'] as $key => $val) { ?>
                <div class="col-md-3">
                    <?= Html::activeCheckbox($model, 'jenisobatalkes_id', [
                        'id' => 'jenis-checkbox-'.$val['jenisobatalkes_id'],
                        'class' => 'jenis-checkbox jenis-obat',
                        'value' => $val['jenisobatalkes_id'],
                        'label' => $val['jenisobatalkes_nama'],
                        'data-id' => $val['jenisobatalkes_id'],
                        'data-nama' => $val['jenisobatalkes_nama'],
                        'data-group' => 'obat'
                    ]) ?>
                </div>
            <?php } ?>
        </div>
    <?php endif; ?>

    <?php if($count_data_alkes > 0) : ?>
        <div class="col-md-12">
            <hr>
            <div class="col-md-12">
                <h5>Alkes</h5>
            </div>
            <div class="col-md-12" style="margin-bottom:10px">
                <?= Html::activeCheckbox($model, 'jenisobatalkes_id', [
                    'id' => 'jenis-checkbox-alkes',
                    'class' => 'check-all-jenis-alkes',
                    'label' => ' <strong>Pilih Semua</strong>'
                ]) ?>
            </div>
            <?php foreach($jenisobatalkes['data_alkes'] as $key => $val) { ?>
                <div class="col-md-3">
                    <?= Html::activeCheckbox($model, 'jenisobatalkes_id', [
                        'id' => 'jenis-checkbox-'.$val['jenisobatalkes_id'],
                        'class' => 'jenis-checkbox jenis-alkes',
                        'value' => $val['jenisobatalkes_id'],
                        'label' => $val['jenisobatalkes_nama'],
                        'data-id' => $val['jenisobatalkes_id'],
                        'data-nama' => $val['jenisobatalkes_nama'],
                        'data-group' => 'alkes'
                    ]) ?>
                </div>
            <?php } ?>
        </div>
    <?php endif; ?>
    
    <?php if($count_non_group > 0) : ?>
        <div class="col-md-12">
            <hr>
            <div class="col-md-12">
                <h5>Non Group</h5>
            </div>
            <div class="col-md-12" style="margin-bottom:10px">
                <?= Html::activeCheckbox($model, 'jenisobatalkes_id', [
                    'id' => 'jenis-checkbox-nongroup',
                    'class' => 'check-all-jenis-nongroup',
                    'label' => ' <strong>Pilih Semua</strong>'
                ]) ?>
            </div>
            <?php foreach($jenisobatalkes['non_group'] as $key => $val) { ?>
                <div class="col-md-3">
                    <?= Html::activeCheckbox($model, 'jenisobatalkes_id', [
                        'id' => 'jenis-checkbox-'.$val['jenisobatalkes_id'],
                        'class' => 'jenis-checkbox jenis-nongroup',
                        'value' => $val['jenisobatalkes_id'],
                        'label' => $val['jenisobatalkes_nama'],
                        'data-id' => $val['jenisobatalkes_id'],
                        'data-nama' => $val['jenisobatalkes_nama'],
                        'data-group' => 'nongroup'
                    ]) ?>
                </div>
            <?php } ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('.modal-body').find('.jenis-checkbox, .check-all-jenis, .check-all-jenis-obat, .check-all-jenis-alkes, .check-all-jenis-nongroup').uniform()
    var count_jenis = {
        obat: <?= $count_data_obat ?>,
        alkes: <?= $count_data_alkes ?>,
        nongroup: <?= $count_non_group ?>,
        all: <?= $count_data_obat + $count_data_alkes + $count_non_group ?>
    };

    var check_all = {
        obat: false,
        jenis: false,
        nongroup: false,
        all: false
    };

    $.each(checked_jenis, function(index, value){
        $(`#jenis-checkbox-${value.jenisobatalkes_id}`).prop('checked', true).uniform('refresh')
        setCheckAll('obat')
        setCheckAll('alkes')
        setCheckAll('nongroup')
        setCheckAllJenis()
    })

    $(".check-all-jenis").on("click", function () {
        $(".check-all-jenis-obat, .check-all-jenis-alkes, .check-all-jenis-nongroup").prop("checked", $(this).is(":checked")).uniform("refresh")
        $(".jenis-obat, .jenis-alkes, .jenis-nongroup").prop("checked", $(this).is(":checked")).uniform("refresh")
        
        $('input:checkbox.jenis-obat').each(function () {
            setCheckedJenis($(this))
        });
        
        $('input:checkbox.jenis-alkes').each(function () {
            setCheckedJenis($(this))
        });

        $('input:checkbox.jenis-nongroup').each(function () {
            setCheckedJenis($(this))
        });
    })
    
    $(".check-all-jenis-obat").on("click", function () {
        $(".jenis-obat").prop("checked", $(this).is(":checked")).uniform("refresh")
        $('input:checkbox.jenis-obat').each(function () {
            setCheckedJenis($(this))
        });
    })
    
    $(".check-all-jenis-alkes").on("click", function () {
        $(".jenis-alkes").prop("checked", $(this).is(":checked")).uniform("refresh")
        $('input:checkbox.jenis-alkes').each(function () {
            setCheckedJenis($(this))
        });
    })
    
    $(".check-all-jenis-nongroup").on("click", function () {
        $(".jenis-nongroup").prop("checked", $(this).is(":checked")).uniform("refresh")
        $('input:checkbox.jenis-nongroup').each(function () {
            setCheckedJenis($(this))
        });
    })

    $('.jenis-checkbox').on("click", function() {
        setCheckedJenis($(this))
        setCheckAll($(this).attr("data-group"))
        setCheckAllJenis()
    })

    function setCheckedJenis(thisAttr) {
        var jenisobatalkes_id = thisAttr.attr('data-id')
        var jenisobatalkes_nama = thisAttr.attr('data-nama')
        var option = {
            'jenisobatalkes_id': jenisobatalkes_id,
            'jenisobatalkes_nama': jenisobatalkes_nama
        }

        var is_exist = checked_jenis.findIndex(item => item.jenisobatalkes_id == jenisobatalkes_id)
        if(thisAttr.is(":checked")) {
            if(is_exist < 0) {
                checked_jenis.push(option)
            }
        } else {
            checked_jenis.splice(is_exist, 1)
        }
        setCheckAllJenis()
    }

    function setCheckAll(group) { 
        var count_checked = $(".jenis-" + group + ":checked").length;
        if(count_jenis[group] == count_checked) {
            check_all[group] = true
        } else {
            check_all[group] = false
        }
        $(".check-all-jenis-" + group).prop("checked", check_all[group]).uniform("refresh")
    }
    
    function setCheckAllJenis() { 
        var count_obat = $(".jenis-obat:checked").length;
        var count_alkes = $(".jenis-alkes:checked").length;
        var count_nongroup = $(".jenis-nongroup:checked").length;
        var count_all_checked = count_obat + count_alkes + count_nongroup;
        if(count_jenis['all'] == count_all_checked) {
            check_all['all'] = true
        } else {
            check_all['all'] = false
        }
        $(".check-all-jenis").prop("checked", check_all['all']).uniform("refresh")
    }

    $("#btn-submit-jenisobat").on("click", function(e) {
        e.preventDefault();
        $("#modal_list_jenis_obat").modal("toggle");
    })
})
</script>
