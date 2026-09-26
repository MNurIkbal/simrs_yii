<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;

use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

$this->title = \Yii::t('fe', 'Info Slider Rumah Sakit');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            // 'class' => 'spa',
                            // 'data-options' => 'link',
                            'id' => 'btn-tambah',
                            // 'data-content' => 'content-perda',
                            'data-target' => '/master/info-slider-rumah-sakit/tambah',
                            // 'data-pesan-error' => '',
                            // 'disabled' => 'disabled'
                        ]
                    ],
                    'update' => [
                        'title' => \Yii::t('fe', 'Ubah'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'link',
                            'id' => 'btn-update',
                            // 'data-content' => 'content-perda',
                            'data-target' => '/master/info-slider-rumah-sakit/update?id=',
                            'data-pesan-error' => '',
                            'disabled' => 'disabled'
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'id' => 'btn-delete',
                            'disabled' => 'disabled',
                            'data-additional' => 'data-rm'
                        ]
                    ],
                    // 'pdf',
                ], '#tb-info-slider') ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-info-slider" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Judul");?></th>
                            <th><?=\Yii::t("fe", "Gambar");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Mulai");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Selesai");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php
$this->registerJs('
    // Global vars

    const judul = "' . (\Yii::t("fe", "Judul")) . '";
    const gambar = "' . (\Yii::t("fe", "Gambar")) . '";
    const tgl_mulai = "' . (\Yii::t("fe", "Tanggal Mulai")) . '";
    const tgl_selesai = "' . (\Yii::t("fe", "Tanggal Selesai")) . '";
	// var kelompokPemeriksaan = "' . (\Yii::t("fe", "Kelompok Pemeriksaan")) . '";


    const updateUrl = "/master/info-slider-rumah-sakit/update?id=";
    const deleteUrl = "/master/info-slider-rumah-sakit/delete?id=";

    // Datatable language
    const emptyTable = "' . (\Yii::t("fe", "Tidak ada data yang tersedia")) . '";
    const info = "' . (\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")) . '";
    const infoEmpty = "' . (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")) . '";
    const infoFiltered = "' . (\Yii::t("fe", "(disaring dari _MAX_ total data)")) . '";
    const lengthMenu = "' . (\Yii::t("fe", "Menampilkan _MENU_ data")) . '";
    const loadingRecords = "' . (\Yii::t("fe", "Memuat...")) . '";
    const processing = "' . (\Yii::t("fe", "Memproses...")) . '";
    const search = "' . (\Yii::t("fe", "Cari:")) . '";
    const zeroRecords = "' . (\Yii::t("fe", "Tidak ada data yang ditemukan")) . '";
    const first = "' . (\Yii::t("fe", "Pertama")) . '";
    const last = "' . (\Yii::t("fe", "Terakhir")) . '";
    const next = "' . (\Yii::t("fe", "Selanjutnya")) . '";
    const previous = "' . (\Yii::t("fe", "Sebelumnya")) . '";
    const sortAscending = "' . (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")) . '";
    const sortDescending = "' . (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")) . '";

    var filterTanggalSlider = \'<div class="input-group"><input type="text" id="rangeSliderStart" class="form-control rangeLahirStart"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeSliderFinish" class="form-control rangeLahirFinish"/><input type="text" style="display:none" class="targetDateLahir"></div>\';
    
    var filter_tanggal_mulai = \'' . (preg_replace("/[\n\t\r]/i",'',Html::textInput('tglmasukrak', '', ['class' => 'form-control pickadate', 'placeholder' => \Yii::t('fe', 'Tanggal Mulai')]))) . '\';

    var filter_tanggal_selesai = \'' . (preg_replace("/[\n\t\r]/i", '', Html::textInput('tglmasukrak', '', ['class' => 'form-control pickadate', 'placeholder' => \Yii::t('fe', 'Tanggal Selesai')]))) . '\';
    
', View::POS_END, 'b-index');

// Register js file
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
