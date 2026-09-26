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
<style lang="css">
    .container-info {
        margin-top: 10px;
        margin-right: 5px;
    }
    .head-info {
        background-color: #54be8b;
        color: white;
        border-radius: 3px 3px 0 0;
    }
    .body-info {
        background-color: #38de9e;
        color: white;
        padding: 7px;
        border-radius: 0 0 3px 3px;
    }
</style>
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
                            <li>
                                <?php
                                    echo Html::button(Yii::t('fe', 'Ubah Jenis Antrian'),[
                                        'class' => 'btn btn-info btn-md',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal_backdrop',
                                        'action' => '/antrian/panggil-antrian/ubah-jenis-antrian?loket_id='.DocoHelpers::encrypt($loket_id).'&jenisantrian_id='.DocoHelpers::encrypt($jenisantrian_id),
                                    ]);
                                ?>
                            </li>
                            <li>
                                <?php $direct = DocoHelpers::encrypt('antrian/panggil-antrian/index') ?>
                                <a
                                    class="btn btn-link"
                                    href="/antrian/panggil-antrian/pilih-loket?jenisantrian_id=<?= DocoHelpers::encrypt($jenisantrian_id); ?>&redirect=<?=$direct ?>"
                                >
                                    <?= Yii::t('fe', 'Pindah loket'); ?>
                                </a>

                                <?= Html::hiddenInput('loket', $loket_nama, ['id' => 'loket']); ?>
                                <?= Html::hiddenInput('jenis_antrian', $jenisantrian_id, ['id' => 'jenisantrian_id']); ?>
                            </li>
                        <?php endif; ?>
                        
                    </ul>
                </div>
            </div>

            <div class="panel panel-white">
                <div class="panel-heading">
                    <legend class="text-bold"><?=Yii::t('fe','Data kunjungan')?></legend>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <!-- <li><a data-action="collapse"></a></li> -->
                        </ul>
                    </div>
                </div>
                <div class="panel-body no-border">
                    
                    <div class='row'>
                        <div class="col-lg-6">
                            <div class="col-lg-6">
                                <?= Html::hiddenInput('antrian_id', '', ['id' => 'hide_antrian_id']); ?>
                                <?= Html::hiddenInput('limit_antrian', '', ['id' => 'hide_limit_antrian']); ?>
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <h2>No Antrian</h2>
                                        <span id="no_antrian" style="font-size: 60px;">
                                            <?php echo "-"; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        Panggilan Ke : <span id="jumlah_panggil"> 0 </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        Sisa Antrian : <span id="queue"> 0 </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="btn-group-vertical">
                                    <button id="btnNext" type="button" class="btn btn-lg btn-info">
                                        <i class="fa fa-arrow-right"></i> No. Berikutnya
                                    </button>
                                    <button id="btnPilih" type="button" class="btn btn-lg btn-success btnPilih">
                                        <i class="fa fa-check"></i> Pilih
                                    </button>
                                    <button id="btnPanggilUlang" type="button" class="btn btn-lg btn-primary btnPanggilUlang">
                                        <i class="fa fa-volume-up"></i> Panggil Ulang
                                    </button>
                                    <button id="btnLewati" type="button" class="btn btn-lg btn-warning" 
                                        data-confirm-message="<?= Yii::t('fe', 'Apakah anda yakin untuk lewati antrian?'); ?>">
                                        <i class="fa fa-share"></i> Lewati
                                    </button>
                                    <button id="btnBatal" type="button" class="btn btn-lg btn-danger btnBatal"
                                    data-confirm-message="<?= Yii::t('fe', 'Apakah anda yakin untuk membatalkan data ini?'); ?>">
                                        <i class="fa fa-times"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <legend>No Antrian yang di lewati</legend>
                                    <table 
                                        class="table datatable-basic table-striped table-hover dataTable no-footer" 
                                        id="table-antrian-lewati"
                                        style="width:100%;"
                                    >
                                        <thead>
                                            <tr class="bg-inverse">
                                                <th><?= Yii::t('fe', 'No antrian'); ?></th>
                                                <th><?= Yii::t('fe', 'Aksi'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="2" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                            </fieldset>
                        </div>
                    </div>
                    <!-- information antrian -->
                    <div class="row">
                        <div class="col-md-12">
                            <legend class="text-bold"><h6><?=Yii::t('fe', 'Informasi Sisa Antrian')?></h6></legend>
                            <div id="list-info"></div>
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
<?php
    $this->registerJs($this->render('js/pilih-antrian.js'));
?>
<script>
    /**
     * author = ali padilah
     * edit = nurhuda
     * refreshTable
     * digunakan untuk melakukan refresh tabel data ntrian yang terlewati
     * param [validasi] default true
     * digunakana untuk menonaktifkan tombol tabel
     */
    var panggilan_ke = 0;
    function refreshTable(validasi = true, batal_lewat = false) {
         var dfd = new $.Deferred();

        $.ajax({
            type: 'GET',
            url: '/antrian/panggil-antrian/component-antrian',
            dataType: 'JSON',
            beforeSend: function (res) {
                $('#queue').html('...');
            },
            success: function (res) {
                if (res.limit_antrian) {
                    $('#hide_limit_antrian').val(res.limit_antrian.lookup_value);
                } else {
                    $('#hide_limit_antrian').val('8');
                }
                
                if (!batal_lewat) {
                    if (res.count_sisa_antrian) {
                        $('#queue').html(res.count_sisa_antrian);
                        if (res == '0') {
                            $('#btnNext').attr('disabled', true);
                            $('#btnPilih').attr('disabled', true);
                            $('#btnPanggilUlang').attr('disabled', true);
                            $('#btnLewati').attr('disabled', true);
                            $('#btnBatal').attr('disabled', true);
                        }
                        if(validasi){
                            $('#btnPilih').attr('disabled', true);
                            $('#btnPanggilUlang').attr('disabled', true);
                            $('#btnLewati').attr('disabled', true);
                            $('#btnBatal').attr('disabled', true);
                        }
                    } else {
                        $('#queue').html('0');
                        $('#btnNext').attr('disabled', true);
                        $('#btnPilih').attr('disabled', true);
                        $('#btnPanggilUlang').attr('disabled', true);
                        $('#btnLewati').attr('disabled', true);
                        $('#btnBatal').attr('disabled', true);
    
                    // console.log("refresh tabel tahap lain");
    
                    }
                } else {
                    if (res.count_sisa_antrian) {
                        $('#queue').html(res.count_sisa_antrian);
                    } else {
                        $('#queue').html('0');
                    }
                }
                if (res.data_antrian_terlewat) {
                    generateTable('#table-antrian-lewati', res.data_antrian_terlewat);
                }

                dfd.resolve("tabel data resolve");

            }
        });

        return dfd.promise();
        
    }

    var pilihLewat = function (btn){
    var header = 'Perhatian !';
    var message ='Apakah anda yakin untuk menyimpan data ini ?';
    var label = { 
        buttons: {
            'No': 'btn btn-danger',
            'Yes': 'btn btn-success btn-yes'
        }
    };
    $.showQuestionDialog(header, message, label, function(reaction) {
        if (reaction == 'Yes') {
            hideQuestionDialog();
            $.ajax({
                url: '/antrian/panggil-antrian/pilih?antrian_id=' + antrian_id + '&lewati=1',
                method: "GET",
                type: "json",
                beforeSend: function(){
                    var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay-lewat"></div>';
                    var dialogTemplate = '<div id="confirm-dialog" class="confirm-dialog-lewat">';
                            dialogTemplate += '<div class="dialog-content">';
                                dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p class=\"confirm-message-text\"></p>';                                    
                            dialogTemplate += '</div>';
                        dialogTemplate += '</div>';

                        $('body').append(overlayTemplate);
                        $('body').append(dialogTemplate);
                        $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw"></i>&nbsp;Sedang memproses . . .');
                    },
                success: function (res) {
                    var promise = refreshTable();
                    promise.done(function(){
                        console.log("sukses melakukan refresh");

                        // $('#modal_backdrop').hide();
                        $('#btnNext').attr('disabled', false);
                        $('#btnPilih').attr('disabled', false);
                        $('#btnPanggilUlang').attr('disabled', false);
                        $('#btnLewati').attr('disabled', false);
                        $('#btnBatal').attr('disabled', false);

                        $('#no_antrian').html('-');
                        panggilan_ke = 0;
                        $('#jumlah_panggil').html('0');
                        $('#hide_antrian_id').val('');
                        
                        $('body').find('.confirm-dialog-overlay-lewat').remove()
                        $('body').find('.confirm-dialog-lewat').remove()
                        // send to form pendaftaran
                        /*$('.close').trigger('click');
                        $('.antrian-id').val(res.antrian_id).trigger('change');
                        $('.no-antrian').val(res.no_antrian);
                        $('.pasien-id').val(res.pasien_id).trigger('change');
                        $('body').find('.confirm-dialog-overlay-lewat').remove()
                        $('body').find('.confirm-dialog-lewat').remove()
                        */
                        
                        $('#no_antrian').html(res.response.no_antrian);
                        panggilan_ke = 1;
                        $('#jumlah_panggil').html(1);
                        $('#hide_antrian_id').val(res.response.antrian_id);
                        $('#btnPilih').attr('data-antrian_id', res.response.antrian_id);
                        $('#btnLewati').attr('data-antrian_id', res.response.antrian_id);
                        $('#btnBatal').attr('data-antrian_id', res.response.antrian_id);

                        // var text = res.response.teks_panggil;
                        // console.log(text);
                        // // console.log(res);
                        // var player = $("#playerAudio");
                        // var arrayText = text.split(" ");
                        // arrayText.push("stop");
                        // arrayText = arrayText.filter(Boolean);

                        // var index = 0;

                        // player[0].defaultPlaybackRate = 1;
                        // player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                        // player[0].play();

                        // player[0].addEventListener("ended", function () {
                        //     index = index + 1;

                        //     if (index < arrayText.length) {
                        //         player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
                        //         if (arrayText[index] == "stop") {
                        //             console.log("masuk");
                        //             // hapusAntrianAudio();
                        //         } else {
                        //             console.log(arrayText[index]);
                        //             player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                        //             player[0].play();
                        //         }
                        //     }
                        // });

                    });
                    // console.log("pilih lewat");

                    
                    
                }
            });
        } else {
            docoHelper.listen = false;
        }
    });
    var antrian_id = $(btn).attr('data-antrian_id');
    
}

var batalLewat = function (btn) {
    // event.preventDefault();
    var antrian_id = $(btn).attr('data-antrian_id');
    var recheck = false;
    var header = 'Perhatian !';
    var message = 'Apakah anda yakin untuk membatalkan data ini?';
    var label = {
        buttons: {
            'No': 'btn btn-danger',
            'Yes': 'btn btn-success btn-yes'
        }
    };
    $.showQuestionDialog(header, message, label, function (reaction) {
        if (reaction == 'Yes') {
            hideQuestionDialog();
            $.ajax({
                url: '/antrian/panggil-antrian/batal?antrian_id=' + antrian_id,
                method: "GET",
                type: "json",
                beforeSend: function () {
                    var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay-lewat"></div>';
                    var dialogTemplate = '<div id="confirm-dialog" class="confirm-dialog-lewat">';
                    dialogTemplate += '<div class="dialog-content">';
                    dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p class=\"confirm-message-text\"></p>';
                    dialogTemplate += '</div>';
                    dialogTemplate += '</div>';

                    $('body').append(overlayTemplate);
                    $('body').append(dialogTemplate);
                    $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw"></i>&nbsp;Sedang memproses . . .');
                },
                success: function (data) {

                    if (typeof $(this).data('antrian_id') !== 'undefined') {
                        antrian_id = $(this).data('antrian_id');
                        recheck = true;
                    }
                    refreshTable(true, true);
                    docoNotification('success', "Proses Berhasil", "Data berhasil dibatalkan"); 
                    $('#btnNext').attr('disabled', true);
                    $('#btnPilih').attr('disabled', false);
                    $('#btnPanggilUlang').attr('disabled', false);
                    $('#btnLewati').attr('disabled', false);
                    $('#btnBatal').attr('disabled', false);

                    // $('#no_antrian').html('-');
                    // panggilan_ke = 0;
                    // $('#jumlah_panggil').html('0');
                    // $('#hide_antrian_id').val('');
                    $('body').find('.confirm-dialog-overlay-lewat').remove()
                    $('body').find('.confirm-dialog-lewat').remove()
                }
            });
        } else {
            docoHelper.listen = false;
        }
    });

}

var disableLewati = function(){
    $('.btnLewati').attr('disabled', true)
}
var disableBatal = function(){
    $('.btnBatal').attr('disabled', true)
}

var test = function(){
    alert("testing");
}
</script>
