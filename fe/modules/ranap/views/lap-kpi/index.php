<?php

/**
 * @Author  : M.ilhamsyah.P
 * @Date    : 2020-08-10 13:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan KPI
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Laporan KPI');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'KPI'), 'url' => ['/ranap/lap-kpi']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row body">
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
            </div>

            <div class="panel-toolbar clearfix">
                
                <button type="button" id="button-cari" class="btn btn-info btn-labeled btn-xs cari" data-parent="" data-title="Pencarian (Enter)">
                <b><i class="fa fa-search"></i></b>Cari</button>
         
            <?=DocoHelpers::generateToolbar([
                    // 'search' => [
                    //     'attributes' => [
                    //         'id' => 'button-cari',
                    //     ]
                    // ],
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'excel' => [
                        'title' => Yii::t('fe', 'Excel'),
                        'attributes'=>[
                            'data-target'=>Url::home().'ranap/lap-kpi/export-excel?jenis=ranap&'
                        ]
                    ],
                ], '#example');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                    </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed"
                    id="example"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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

$this->registerJs("
    var table;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
       
        // Generate Table
        table = $('#example').docoTabel({
            displayLength: 10,
            sorting: [[2, 'desc']],
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl + 'ranap/lap-kpi/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false

                },
                {title: '".(\Yii::t("fe", "Tipe Pasien"))."', data: 'patient_type', searchable: false },
                 {title: '".(\Yii::t("fe", "Tanggal Masuk"))."', data: 'tgl_masukkamar'},
                {title: '".(\Yii::t("fe", "No Pendaftaran"))."', data: 'no_pendaftaran', searchable: false},
                {title: '".(\Yii::t("fe", "Tanggal Admisi"))."', data: 'tgl_admisi', searchable: false},
                {title: '".(\Yii::t("fe", "No. Rekam Medik"))."', data: 'no_rekam_medik'},
                {title: '".(\Yii::t("fe", "Nama Pasien"))."', data: 'nama_pasien'},
                {title: '".(\Yii::t("fe", "Tanggal Lahir"))."', data: 'tanggal_lahir', searchable: false},
                {title: '".(\Yii::t("fe", "Kamar Terakhir"))."', data: 'kamar_terakhir'},
                {title: '".(\Yii::t("fe", "Jenis Kamar"))."', data: 'jenis_kamar', searchable: false},
                {title: '".(\Yii::t("fe", "Ruangan Terakhir"))."', data: 'ruangan_terakhir', visible:false},
                {title: '".(\Yii::t("fe", "Nama DPJP"))."', data: 'nama_dpjp', visible:false},
                {title: '".(\Yii::t("fe", "Tanggal Pasien Pulang"))."', data: 'tglpasienpulang', searchable: false},
                {title: '".(\Yii::t("fe", "Kondisi Pulang"))."', data: 'kondisi_pulang'},
                {title: '".(\Yii::t("fe", "Cara Keluar"))."', data: 'cara_keluar'},
                {title: '".(\Yii::t("fe", "Nama Penjamin"))."', data: 'penjamin_nama', searchable: false},
                {title: '".(\Yii::t("fe", "Tanggal Rencana Pulang"))."', data: 'tgl_rencanapulang', searchable: false},
                {title: '".(\Yii::t("fe", "Nomor Tagihan"))."', data: 'nomor_tagihan'},
                {title: '".(\Yii::t("fe", "Tanggal Tagihan"))."', data: 'tgl_tagihan', searchable: false},
                {title: '".(\Yii::t("fe", "Tanggal Bayar"))."', data: 'tgl_bayar', searchable: false},
                {title: '".(\Yii::t("fe", "Tanggal Dibersihkan"))."', data: 'tgl_dibersihkan', searchable: false},
                {title: '".(\Yii::t("fe", "Jam Tunggu"))."', data: 'jam_ranap', searchable: false},
                {title: '".(\Yii::t("fe", "Jam Ranap"))."', data: 'jam_tunggu', searchable: false},
            ],
        });

        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
               2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],

        ], 
        {
         2:0
        });

        function getMonth(monthStr){
            return new Date(monthStr+'-1-01').getMonth()+1
        }

        dateRangeHelper('.startDate','.endDate','.targetDate',true);
        
          $('#button-cari').click(function(){
             
            var awal = $('#rangeDemoStart').val()
            var akhir = $('#rangeDemoFinish').val()
            var arrAwal = awal.split('-');
            var arrAkhir = akhir.split('-');
            var awalM = getMonth(arrAwal[1])
            var akhirM = getMonth(arrAkhir[1])
    
            if ((akhirM - awalM > 3) || (akhirM - awalM == 3 && arrAkhir[0] - arrAwal[0] >= 0) || (arrAwal[2] != arrAkhir[2]) ) {
                docoNotification('error','Kapasitas Hanya 3 Bulan!', 'Range Data Melebihi Kapasitas');
                return false
            }else{
                $('.cari').addClass('data-filter');
            }
          });


    });
    
    ", View::POS_END, 'b-index');
?>
