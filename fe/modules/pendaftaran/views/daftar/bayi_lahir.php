<?php

/**
 * @author Budi
 * @description UI Pendaftaran Bayi Lahir
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
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
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
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li class="hidden"><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel panel-white">
                <div class="panel-body no-border">
                    <div class="alert alert-info">
                        <strong>Alt + S</strong> <?= Yii::t('fe', ' Simpan pasien / simpan kunjungan') ?><br>
                        <strong>Alt + Q</strong> <?= Yii::t('fe', ' Panggil Antrian') ?><br>
                        <strong>Alt + W</strong> <?= Yii::t('fe', ' Ubah Jenis Antrian') ?><br>
                        <strong>F8</strong><?= Yii::t('fe', ' Pencarian Lanjutan') ?><br>
                        <strong>F9</strong> <?= Yii::t('fe', ' Pasien Lama / Pasien Baru') ?>
                    </div>
                
                <legend class="text-bold">Data Pasien</legend>
                <div id="data-bayi"></div>
                <?php echo Yii::$app->controller->renderPartial('partial/_pasienBayi', $packFormPasien);?>

                <legend><?=Yii::t('fe','Data kunjungan')?></legend>
                    <div class="row">
                        <?php
                        echo Yii::$app->controller->renderPartial(
                                'partial/_formkunjunganranapBayi',
                                array_merge($packFormKunjunganRanap, ['param'=>$param])
                            );
                        ?>
                    </div>
                    <div class="row form-karcis">
                        <div class="col-md-12">
                            <?= Yii::$app->controller->renderPartial('partial/_tarifkarcis',[]);?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-6">
                    <button type="submit" class="btn btn-info btn-sm btn-labeled simpan-btn"><b class="fa fa-lg fa-floppy-o"></b> <?=Yii::t('fe','Simpan')?></button>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-warning btn-sm btn-labeled reset-btn"><b class="fa fa-lg fa-refresh"></b> <?=Yii::t('fe','Muat ulang')?></button>
                </div>

            </div>
        </div>
    </div>
</div>


&nbsp;
<div class="clearfix">
</div>


<?= Yii::$app->controller->renderPartial('partial/_sepuluhterakhir', ['param'=>$param]);?>

<div class="modal fade camera-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="row">
                <div id="my_camera" class="col-md-12"></div>
            </div>
        </div>
    </div>
</div>

<div id="modal_pencarian_lanjutan" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$data_pemesanan_kamar = isset($data_pemesanan_kamar) ? $data_pemesanan_kamar : '{}';
$this->registerJs('
    const data_pemesanan = '.$data_pemesanan_kamar. ';
    const url = document.URL;
    $(".panel-kunjungan").hide();
    $(".panel-pj").hide();
    $(".form-karcis").hide();
    // $(".panel-bpjs-new").hide();
    // $(".btn-form-bpjs").hide();
    $(".simpan-btn").hide();
    const id_booking = getParameterByName("id_booking", url)
    if (id_booking != null) {
        if (typeof data_pemesanan !== "undefined") {
            const pendaftaran_id = data_pemesanan.pendaftaran_id;
            if (data_pemesanan.bookingkamar_id != null) {
                $("#is_booked").prop("checked", true);
                $("#bookingkamar_no").val(data_pemesanan.no_pemesanan);
                $("#bookingkamar_id").val(data_pemesanan.bookingkamar_id).trigger("change");
                $("#jeniskasuspenyakit_id").val(data_pemesanan.jeniskasuspenyakit_id);

                setTimeout(function () {
                    const data = {
                        id: data_pemesanan.pasien_id,
                        text: data_pemesanan.no_rekam_medik + " - " + data_pemesanan.nama_pasien
                    };

                    const newOption = new Option(data.text, data.id, false, false);
                    $("#no_rekam_medik").append(newOption).trigger("change");
                    $("#kelaspelayanan_id").val(data_pemesanan.kelaspelayanan_id).trigger("change").trigger("depdrop:change");
                    $("#temp_ruangan_id").val(data_pemesanan.ruangan_id);
                    $("#ruangan_id").val(data_pemesanan.ruangan_id);
                    $("#kamarruangan_id").val(data_pemesanan.kamarruangan_id);
                    $("#kamartempattidur_id").val(data_pemesanan.kamartempattidur_id);
                    $("#nokamar").val(data_pemesanan.kamarruangan_nokamar);
                    $("#pendaftaran-id").val(data_pemesanan.pendaftaran_id);
                }, 1500)
            }
        }
    }
    
    $(".cariPeserta").on("click", function(){
        $("#poliTujuan").hide();
    });

    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return "";
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

', View::POS_END, 'js-index');
$this->registerJs($this->render('js/bayi.js'), View::POS_END);
?>
