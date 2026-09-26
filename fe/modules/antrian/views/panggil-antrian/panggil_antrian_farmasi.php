<?php

/**
 * @author Arief Saputra
 * @description pilih antrian
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Panggil Antrian'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<input type="hidden" name="" class="params-header" value="<?=isset($param) ? $param : '' ?>">
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias") . ($loket_nama ? ' - ' . Yii::t('fe', 'Loket') . ' ' . $loket_nama : ''); ?></b></h3>
                      <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                  </div>
              </div>
              <!-- end -->
              <div class="heading-elements">
                    <ul class="icons-list">
                        <?php if ($param == 'rajal' || $param == 'penunjang') : ?>
                            <?php $direct = DocoHelpers::encrypt('antrian/panggil-antrian/index') ?>
                            <li>
                                <a
                                    class="btn btn-link"
                                    href="/antrian/panggil-antrian/pilih-loket?jenisantrian_id=<?= DocoHelpers::encrypt($jenisantrian_id); ?>&redirect=<?= $direct ?>"
                                >
                                    <?= Yii::t('fe', 'Pindah loket'); ?>
                                </a>

                                <?= Html::hiddenInput('loket', $loket_nama, ['id' => 'loket']); ?>
                            </li>
                        <?php endif; ?>
                        
                    </ul>
                </div>
            </div>

            <div class="panel panel-white">
                <div class="panel-body no-border">
                    <div class='row'>
                        <div class="col-lg-5">
                            <div class="header-ant-farmasi">
                                 <?=Yii::t('fe', 'Resep Racikan')?>
                            </div>
                            <div class='tabbable'>
                                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                                    <li class="active">
                                        <a href="#view-r-belum-selesai" data-toggle="tab" aria-expanded="true">
                                            <?=Yii::t('fe', 'Belum selesai')?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#view-r-selesai" data-toggle="tab" aria-expanded="true">
                                            <?=Yii::t('fe', 'Sudah selesai')?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="view-r-belum-selesai">
                                    <div id="content-r-belum-selesai">
                                        <table class="table table-striped table-condensed table-hover dataTable no-footer">
                                            <thead>
                                                <tr class="bg-inverse" role="row">
                                                    <td width="5px">No</td>
                                                    <td>No Antrian</td>
                                                    <td>Status</td>
                                                    <td>Proses</td>
                                                </tr>
                                            </thead>
                                            <tbody id="tb-r-blm">
                                                <?php $no = 1 ?>
                                                <?php foreach ($dt_antrian['racikan']['belum_proses'] as $value): ?>
                                                    <tr>
                                                        <td><?= $no ?></td>
                                                        <td><?= $value['no_antrian'] ?></td>
                                                        <td>
                                                            <?= empty($value['stat_antrian_farmasi']) ? Yii::t('fe', 'Belum Proses')
                                                                                                 : $value['stat_antrian_farmasi'] ?>
                                                            </td>
                                                        <td>
                                                            <?php if ($value['antrian_farmasi'] == $status_ambil-1): ?>
                                                                <span class="btn btn-primary" onclick="proses_antrian(this,<?= $value['antrian_id']; ?>);panggil_antrian(this,<?= $value['antrian_id']; ?>,'r')">
                                                                <i class="fa fa-volume-up"></i>
                                                                <?= empty($value['stat_proses_antrian_farmasi']) ? Yii::t('fe', 'Proses')
                                                                                                 : $value['stat_proses_antrian_farmasi'] ?>
                                                            </span>
                                                            <?php else: ?>
                                                                <span class="btn btn-success" onclick="proses_antrian(this,<?= $value['antrian_id']; ?>)">
                                                                <?= empty($value['stat_proses_antrian_farmasi']) ? Yii::t('fe', 'Proses')
                                                                                                 : $value['stat_proses_antrian_farmasi'] ?>
                                                            </span>
                                                            <?php endif ?>

                                                            <?php if ($value['antrian_farmasi'] == $status_ambil): ?>
                                                                <span class="btn btn-primary" onclick="panggil_antrian(this,<?= $value['antrian_id']; ?>,'r')">
                                                                    <?= Yii::t('fe', '<i class="fa fa-volume-up"></i> Panggil') ?>
                                                                </span>
                                                            <?php endif ?>
                                                        </td>
                                                    </tr>
                                                <?php $no++ ?>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="view-r-selesai">
                                    <div id="content-r-selesai">
                                        <table class="table table-striped table-condensed table-hover dataTable no-footer">
                                            <thead>
                                                <tr class="bg-inverse" role="row">
                                                    <td width="5px">No</td>
                                                    <td>No Antrian</td>
                                                    <td>Status</td>
                                                </tr>
                                            </thead>
                                            <tbody id="tb-r-sls">
                                                <?php $no = 1 ?>
                                                <?php foreach ($dt_antrian['racikan']['sudah_proses'] as $value): ?>
                                                    <tr>
                                                        <td><?= $no ?></td>
                                                        <td><?= $value['no_antrian'] ?></td>
                                                        <td>
                                                            <?= empty($value['stat_antrian_farmasi']) ? Yii::t('fe', 'Belum Proses')
                                                                                                 : $value['stat_antrian_farmasi'] ?>
                                                        </td>
                                                    </tr>
                                                <?php $no++ ?>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">&nbsp;</div>
                        <div class="col-lg-5">
                            <div class="header-ant-farmasi">
                                <?=Yii::t('fe', 'Resep Non Racikan')?>
                            </div>
                            <div class='tabbable'>
                                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                                    <li class="active">
                                        <a href="#view-nr-belum-selesai" data-toggle="tab" aria-expanded="true">
                                            <?=Yii::t('fe', 'Belum selesai')?>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="#view-nr-selesai" data-toggle="tab" aria-expanded="true">
                                            <?=Yii::t('fe', 'Sudah selesai')?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="view-nr-belum-selesai">
                                    <div id="content-nr-belum-selesai">
                                        <table class="table table-striped table-condensed table-hover dataTable no-footer">
                                            <thead>
                                                <tr class="bg-inverse" role="row">
                                                    <td width="5px">No</td>
                                                    <td>No Antrian</td>
                                                    <td>Status</td>
                                                    <td>Proses</td>
                                                </tr>
                                            </thead>
                                            <tbody id="tb-nr-blm">
                                                <?php $no = 1 ?>
                                                <?php foreach ($dt_antrian['non_racikan']['belum_proses'] as $value): ?>
                                                    <tr>
                                                        <td><?= $no ?></td>
                                                        <td><?= $value['no_antrian'] ?></td>
                                                        <td>
                                                            <?= empty($value['stat_antrian_farmasi']) ? Yii::t('fe', 'Belum Proses')
                                                                                                 : $value['stat_antrian_farmasi'] ?>
                                                            </td>
                                                        <td>
                                                            <?php if ($value['antrian_farmasi'] == $status_ambil-1): ?>
                                                                <span class="btn btn-primary" onclick="proses_antrian(this,<?= $value['antrian_id']; ?>);panggil_antrian(this,<?= $value['antrian_id']; ?>,'nr')">
                                                                <i class="fa fa-volume-up"></i>
                                                                <?= empty($value['stat_proses_antrian_farmasi']) ? Yii::t('fe', 'Proses')
                                                                                                 : $value['stat_proses_antrian_farmasi'] ?>
                                                            </span>
                                                            <?php else: ?>
                                                                <span class="btn btn-success" onclick="proses_antrian(this,<?= $value['antrian_id']; ?>)">
                                                                    <?= empty($value['stat_proses_antrian_farmasi']) ? Yii::t('fe', 'Proses')
                                                                                                     : $value['stat_proses_antrian_farmasi'] ?>
                                                                </span>
                                                            </span>
                                                            <?php endif ?>

                                                            

                                                            <?php if ($value['antrian_farmasi'] == $status_ambil): ?>
                                                                <span class="btn btn-primary" onclick="panggil_antrian(this,<?= $value['antrian_id']; ?>,'nr')">
                                                                    <?= Yii::t('fe', '<i class="fa fa-volume-up"></i> Panggil') ?>
                                                                </span>
                                                            <?php endif ?>
                                                        </td>
                                                    </tr>
                                                <?php $no++ ?>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="view-nr-selesai">
                                    <div id="content-nr-selesai">
                                        <table class="table table-striped table-condensed table-hover dataTable no-footer">
                                            <thead>
                                                <tr class="bg-inverse" role="row">
                                                    <td width="5px">No</td>
                                                    <td>No Antrian</td>
                                                    <td>Status</td>
                                                </tr>
                                            </thead>
                                            <tbody id="tb-nr-sls">
                                                <?php $no = 1 ?>
                                                <?php foreach ($dt_antrian['non_racikan']['sudah_proses'] as $value): ?>
                                                    <tr>
                                                        <td><?= $no ?></td>
                                                        <td><?= $value['no_antrian'] ?></td>
                                                        <td>
                                                            <?= empty($value['antrian_farmasi']) ? Yii::t('fe', 'Belum Proses')
                                                                                                 : $value['antrian_farmasi'] ?>
                                                        </td>
                                                    </tr>
                                                <?php $no++ ?>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


&nbsp;
<div class="clearfix">
</div>

<script>
    /**
     * author = ali.padilah@docotel.com
     * pemanggilan antrian farmasi
     */

    var _status_ambil = <?= $status_ambil?>;
    var _ruangan_id = <?= $ruanganId?>;

    function panggil_antrian(element,id,tipe) {
        $.ajax({
            type:'GET',
            url: '/antrian/panggil-antrian/panggil-antrian-farmasi?antrian_id='+id+'&tipe='+tipe,
            dataType: 'JSON',
            beforeSend: function(res) {

            },
            success: function(res) {

            }
        });
    }

    function proses_antrian(element,id) {
        var _text = $(element).html();
        $.ajax({
            type:'GET',
            url: '/antrian/panggil-antrian/proses-antrian-farmasi?antrian_id='+id,
            dataType: 'JSON',
            beforeSend: function(res) {
                ajaxLoading(element);
            },
            success: function(res) {
                console.log(res);
                docoNotification('success', 'Proses Berhasil !!', res.response.message);
                ajaxAfterLoading(element,_text);
                generateDataFarmasi(res.response,res.response.ruangan_id);
            },
            error: function(res) {
                docoNotification('warning', 'Proses Gagal !!', res.responseJSON.response.message);
                ajaxAfterLoading(element,_text);
            }
        });
    }

    function ajaxLoading(element) {
        $(element).attr('disabled', true);
        $(element).html("<i class=\"fa fa-spinner fa-pulse fa-1x fa-fw\"></i>");
    }

    function ajaxAfterLoading(element,text) {
        $(element).attr('disabled', false);
        $(element).html(text)
    }

    function generateDataFarmasi(data,ruangan) {
        if (ruangan == _ruangan_id) {
            $("#tb-r-blm").html("");
            $("#tb-r-sls").html("");
            $("#tb-nr-blm").html("");
            $("#tb-nr-sls").html("");

            var _tb_r_blm = "";
            if (data.racikan.belum_proses !== undefined || data.racikan.belum_proses.length != 0) {
                var _no = 1;
                data.racikan.belum_proses.forEach(function(element){

                    var _extend_panggil = "";
                    if (element.antrian_farmasi == _status_ambil) {
                        _extend_panggil = " <span class='btn btn-primary' onclick='panggil_antrian(this,"+element.antrian_id+",\"r\")'>"+
                                            "<i class='fa fa-volume-up'></i> Panggil"+
                                            "</span>";
                    }

                    if (element.antrian_farmasi == _status_ambil-1) {
                        _extend_button = "<span class='btn btn-primary' onclick='proses_antrian(this,"+element.antrian_id+");panggil_antrian(this,"+element.antrian_id+",\"r\")'>"+
                                            "<i class='fa fa-volume-up'></i> "+
                                            element.stat_proses_antrian_farmasi+
                                        "</span> "+
                                        _extend_panggil;
                    } else {
                        _extend_button = "<span class='btn btn-success' onclick='proses_antrian(this,"+element.antrian_id+")'>"+
                                            element.stat_proses_antrian_farmasi+
                                        "</span> "+
                                        _extend_panggil;
                    }
                   
                    _tb_r_blm +="<tr>"+
                                "<td>"+_no+"</td>"+
                                "<td>"+element.no_antrian+"</td>"+
                                "<td>"+element.stat_antrian_farmasi+"</td>"+
                                "<td>"+
                                    _extend_button+
                                "</td>"+
                            "</tr>";
                    _no++;
                });
            }

            var _tb_r_sls = "";
            if (data.racikan.sudah_proses !== undefined || data.racikan.sudah_proses.length != 0) {
                var _no = 1;
                data.racikan.sudah_proses.forEach(function(element){
                   
                    _tb_r_sls +="<tr>"+
                                "<td>"+_no+"</td>"+
                                "<td>"+element.no_antrian+"</td>"+
                                "<td>"+element.stat_antrian_farmasi+"</td>"+
                            "</tr>";
                    _no++;
                });
            }

            var _tb_nr_blm = "";
            if (data.non_racikan.belum_proses !== undefined || data.non_racikan.belum_proses.length != 0) {
                var _no = 1;
                data.non_racikan.belum_proses.forEach(function(element){

                    var _extend_panggil = "";
                    if (element.antrian_farmasi == _status_ambil) {
                        _extend_panggil = " <span class='btn btn-primary' onclick='panggil_antrian(this,"+element.antrian_id+",\"nr\")'>"+
                                            "<i class='fa fa-volume-up'></i> Panggil"+
                                            "</span>";
                    }

                    if (element.antrian_farmasi == _status_ambil-1) {
                        _extend_button = "<span class='btn btn-primary' onclick='proses_antrian(this,"+element.antrian_id+");panggil_antrian(this,"+element.antrian_id+",\"nr\")'>"+
                                            "<i class='fa fa-volume-up'></i> "+
                                            element.stat_proses_antrian_farmasi+
                                        "</span> "+
                                        _extend_panggil;
                    } else {
                        _extend_button = "<span class='btn btn-success' onclick='proses_antrian(this,"+element.antrian_id+")'>"+
                                            element.stat_proses_antrian_farmasi+
                                        "</span> "+
                                        _extend_panggil;
                    }
                   
                    _tb_nr_blm +="<tr>"+
                                "<td>"+_no+"</td>"+
                                "<td>"+element.no_antrian+"</td>"+
                                "<td>"+element.stat_antrian_farmasi+"</td>"+
                                "<td>"+
                                    _extend_button+
                                "</td>"+
                            "</tr>";
                    _no++;

                });
            }

            var _tb_nr_sls = "";
            if (data.non_racikan.sudah_proses !== undefined || data.non_racikan.sudah_proses.length != 0) {
                var _no = 1;
                data.non_racikan.sudah_proses.forEach(function(element){
                   
                    _tb_nr_sls +="<tr>"+
                                "<td>"+_no+"</td>"+
                                "<td>"+element.no_antrian+"</td>"+
                                "<td>"+element.stat_antrian_farmasi+"</td>"+
                            "</tr>";
                    _no++;
                });
            }


            $("#tb-r-blm").html(_tb_r_blm);
            $("#tb-r-sls").html(_tb_r_sls);

            $("#tb-nr-blm").html(_tb_nr_blm);
            $("#tb-nr-sls").html(_tb_nr_sls);
        }
    }
</script>

<?php
    $this->registerJs($this->render('../assets/js/pemanggilan-antrian-listener.js'));
?>
