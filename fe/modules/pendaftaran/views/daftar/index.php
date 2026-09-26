<?php

/**
 * @author Arief Saputra
 * @description UI Pendaftaran Rajal
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
                        <?php if ($param == 'rajal' ||$param == 'penunjang') : ?>
                            <li><?= Yii::t('fe', 'Tekan enter untuk mencari') ?></li>
                            <li>
                                <div class='form-group'>
                                    <input type="text" class="form-control no-antrian" name="no_antrian" autocomplete="off" placeholder="Ketik antrian manual" readonly onfocus="this.removeAttribute('readonly');" >
                                </div>
                            </li>
                            <li>
                                <button
                                    type="button" class="btn btn-primary"
                                    id="btn-pilih-antrian"
                                    action="/pendaftaran/daftar/pilih-antrian"
                                    data-width='900px'
                                    data-toggle="modal" data-target="#modal_backdrop"
                                >
                                    Panggil Antrian
                                </button>
                            </li>
                            <li>
                                <?php
                                    echo Html::button(Yii::t('fe', 'Ubah Jenis Antrian'),[
                                        'class' => 'btn btn-info btn-md',
                                        'id'=>'btn-ubah-jenis-antrian',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal_backdrop',
                                        'action' => '/pendaftaran/daftar/ubah-jenis-antrian?loket_id='.$loket_id.'&jenisantrian_id='.$jenisantrian_id,
                                    ]);
                                ?>
                            </li>
                            <li>
                                <!-- <a
                                    class="btn btn-link"
                                    id="btn-pindah-loket"
                                    href="/pendaftaran/daftar/pilih-loket?jenisantrian_id=<?= $jenisantrian_id; ?>"
                                >
                                    <?= Yii::t('fe', 'Pindah loket'); ?>
                                </a> -->

                                <?= Html::hiddenInput('loket', $loket_nama, ['id'=>'loket']); ?>
                            </li>
                        <?php endif; ?>
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
                <!-- DATA PASIEN -->
                <legend class="text-bold">Data Pasien</legend>
                <?php echo Yii::$app->controller->renderPartial('partial/_pasien', $packFormPasien);?>

                <!-- DATA KUNJUNGAN -->
                <legend><?=Yii::t('fe','Data kunjungan')?></legend>
                    <div class="row">
                        <?php
                        if ($param != 'ranap') {
                            echo Yii::$app->controller->renderPartial(
                                'partial/_formkunjungan',
                                array_merge($packFormKunjungan, ['param'=>$param, 'pemilihanDokter' => $pemilihanDokter])
                            );
                        } else {
                            echo Yii::$app->controller->renderPartial(
                                'partial/_formkunjunganranap',
                                array_merge($packFormKunjunganRanap, ['param'=>$param])
                            );
                        }
                        ?>
                    </div>
                    <!-- <div class="row">
                        <div class="form-bpjs hidden">
                            <div class="col-md-6">
                                <?//= Yii::$app->controller->renderPartial('partial/_bpjs',$packFormBpjs);?>
                            </div>
                        </div>
                        <div class="form-asuransi hidden">
                            <div class="col-md-6">
                                <?//= Yii::$app->controller->renderPartial('partial/_asuransi',$packFormAsuransi);?>
                            </div>
                        </div>
                        <div class="form-rujukan hidden">
                            <div class="col-md-6">
                                <?//= Yii::$app->controller->renderPartial('partial/_rujukan',$packFormRujukan);?>
                            </div>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="col-md-12">
                            <?= Yii::$app->controller->renderPartial('partial/_tarifkarcis',[]);?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= Yii::$app->controller->renderPartial('partial/_riwayatkunjungan');?>
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

<!-- <div class="row">
    <div class="col-md-12">
        <?= Yii::$app->controller->renderPartial('partial/_statussync', ['data' => $data]);?>
    </div>
</div> -->

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

<button class="btn btn-primary grid-button btn-sm btn-form-bpjs hidden" data-toggle="modal" data-target="#modal_backdrop" data-width="90%" action="/pendaftaran/daftar/get-form-bpjs?pendaftaran_id=364&no_rekam_medik=00005&param=igd">Tampilkan</button>

<button class="btn btn-primary grid-button btn-sm btn-form-asuransi hidden" data-toggle="modal" data-target="#modal_backdrop" data-width="75%" action="/pendaftaran/daftar/get-form-asuransi?pendaftaran_id=364&asalrujukan_id=3">Tampilkan</button>

<script type="text/javascript">
    const isPenunjang = "<?= $isPenunjang ?>";
</script>
<?php
$data_pemesanan_kamar = isset($data_pemesanan_kamar) ? $data_pemesanan_kamar : '{}';
$this->registerJs('
    const pendaftaranol_id = "'.$pendaftaranol_id. '";
    const data_pemesanan = '.$data_pemesanan_kamar. ';
    const paramLayanan = "'.$param.'";
    const url = document.URL;
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
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
