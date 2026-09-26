<?php

/**
 * @Author: afil
 * @Date:   2018-01-12 15:47:03
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-26 16:33:35
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Informasi');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['/rajal/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" 
                    id="data-informasi" 
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'No antrian')?></th>
                            <th><?=Yii::t('fe', 'Tanggal pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'Asal ruangan')?></th>
                            <th><?=Yii::t('fe', 'No pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'No RM')?></th>
                            <th><?=Yii::t('fe', 'Nama pasien')?></th>
                            <th><?=Yii::t('fe', 'L/P')?></th>
                            <th><?=Yii::t('fe', 'Penjamin')?></th>
                            <th><?=Yii::t('fe', 'Dokter')?></th>
                            <th><?=Yii::t('fe', 'Status pasien')?></th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
                        </tr>
                    </thead>
                    <tbody> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
    // Event Ready
    // Generate Table
    var tabel = $("#data-informasi").docoTabel({
        filter: true,
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"rajal/informasi/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "no_antrian")).'", data: "no_antrian"},
            {title: "'.(\Yii::t("fe", "tgl_pendaftaran")).'", data: "tgl_pendaftaran"},
            {title: "'.(\Yii::t("fe", "ruanganasal_nama")).'", data: "ruanganasal_nama"},
            {title: "'.(\Yii::t("fe", "no_pendaftaran")).'", data: "no_pendaftaran"},
            {title: "'.(\Yii::t("fe", "no_rekam_medik")).'", data: "no_rekam_medik"},
            {title: "'.(\Yii::t("fe", "nama_pasien")).'", data: "nama_pasien"},
            {title: "'.(\Yii::t("fe", "jenis_kelamin")).'", data: "jenis_kelamin"},
            {title: "'.(\Yii::t("fe", "penjamin_nama")).'", data: "penjamin_nama"},
            {title: "'.(\Yii::t("fe", "nama_pegawai")).'", data: "nama_pegawai"},
            {title: "'.(\Yii::t("fe", "status_periksa1")).'", data: "status_periksa1"},
            {
                title: "'.(\Yii::t("fe", "Aksi")). '",
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
        ],
        fixedColumns:   {
            rightColumns: 2
        }
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(tabel, [
        [2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('tgl_pendaftaran', '', ['class' => 'form-control daterange', 'placeholder' => \Yii::t('fe', 'Tanggal rencana kontrol')]))).'\'],
        [8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('penjamin_nama', '', ArrayHelper::map($data_penjamin, 'penjamin_nama', 'penjamin_nama'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--pilih penjamin--')]))).'\'],
        [9, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_pegawai', '', ArrayHelper::map($data_pegawai, 'nama_pegawai', 'nama_pegawai'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--pilih pegawai--')]))).'\'],
        [10, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_periksa1', '', ArrayHelper::map($data_statusperiksa, 'status_periksa1', 'lookup_name'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--pilih status periksa--')]))).'\'],
    ]);

    var _afterSave = function (bool) {
        tabel.reload();
    }

    $(document).on("click",".data-delete", function(event) {
        event.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                _afterSave()
            }
        });
    });

    $(document).on("click",".data-aktifasi", function(event) {
        $(this).docoForm("delete",{
            success : function (data) {
                _afterSave()
            }
        });
    });

    $(".reset-filter").on("click", function (e) {
        e.preventDefault();
        tabel.reset();
    });

    $(".select2", $("form.form-filter")).change(function (event) {
        event.preventDefault();
        tabel.reload();
    });

    $("form.form-filter").on("submit", function (e) {
        e.preventDefault();
        tabel.reload();
    });
', View::POS_END, 'b-index');

$this->registerJs($this->render('js/_informasi.js'), View::POS_END);
?>