<?php
// Author : Budi
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rm'), 'url' => ['index']];
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
            <div class="panel-toolbar clearfix">                
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],                    
                ]);?>                
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Nomor Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                            <th><?=\Yii::t("fe", "Instalasi");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Dokter Penanggung Jawab");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php 
$this->registerCss('
.daterangepicker{
    // top:187px !important;
}
');
$this->registerJs('
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/inf-kunjungan/get-data",
            columns: [
                {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Tanggal kunjungan")).'", data: "tgl_pendaftaran"},
            {title: "'.(\Yii::t("fe", "No pendaftaran")).'", data: "no_pendaftaran", searchable: false},
            {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik", searchable: false},
            {title: "'.(\Yii::t("fe", "Nama pasien")).'", data: "nama_pasien", searchable: false},
            {title: "'.(\Yii::t("fe", "Jenis Kelamin")).'", data: "jenis_kelamin", searchable: false},
            {title: "'.(\Yii::t("fe", "Cara bayar")).'", data: "carabayar_nama", name: "carabayar_id"},
            {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama", name: "penjamin_id"},
            {title: "'.(\Yii::t("fe", "Jenis kasus penyakit")).'", data: "jeniskasuspenyakit_nama"},
            {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_nama", name: "instalasi_id"},
            {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", name: "ruangan_id"},
            {title: "'.(\Yii::t("fe", "Dokter penanggung jawab")).'", data: "nama_pegawai"},
            ],
            
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=1></div>\'
                ],
                [
                    6, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carabayar_nama', '', 
                        ArrayHelper::map($api['response']['cara_bayar'], 'carabayar_id', 'carabayar_nama'), [
                            'class' => 'form-control select2','id'=>'filter_carabayar', 'prompt' => '-' ]))).'\'
                ],
                [
                    7, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                        DepDrop::widget([
                            'name' => 'carabayar_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_carabayar'],
                               'placeholder' => '-',
                               'url' =>'inf-kunjungan/get-penjamin'
                            ]
                        ])                        
                    )).'</div>\'
                ],
                [
                    8, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jeniskasuspenyakit_nama', '', 
                        ArrayHelper::map($api['response']['kasus_penyakit'], 'jeniskasuspenyakit_nama', 'jeniskasuspenyakit_nama'), [
                            'class' => 'form-control select2', 'prompt' => '-']))).'\'
                ],
                [
                    9, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi_nama', '', 
                        ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'), [
                            'class' => 'form-control select2','id'=>'filter_instalasi', 'prompt' => '-']))).'\'
                ],
                [
                    10, 
                    \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                        DepDrop::widget([
                            'name' => 'ruangan_nama',
                            'options' => [
                                'disabled' => false,
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions' => [
                               'depends'  => ['filter_instalasi'],
                               'placeholder' => \Yii::t('fe', 'Ruangan tujuan'),
                               'url' =>'inf-kunjungan/get-ruangan'
                            ]
                        ])                        
                    )).'</div>\'
                ],
                [
                    11, 
                    \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_pegawai', NULL, [], ['class' => 'form-control select2 selectDokter', 'prompt' => \Yii::t('fe', 'Dokter penanggung jawab'), "col-index" => "11"]))).'<span class="input-group-addon"><span class="cursor-pointer" action="'.Url::home().'rm/inf-kunjungan/search" data-toggle="modal" data-target="#modal_backdrop_search"><i class="fa fa-list"></i> <i class="fa fa-search"></i></span></span></div>\'
                ],
            ], {
                1:0,
                6:1,
                7:2,
                8:3,
                9:4,
                10:5,
            }, true
        );
        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".daterange-basic").daterangepicker({
            // autoUpdateInput: false,
            startDate: "'.(date("01-m-Y")).'",
            endDate: "'.(date("d-m-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });
        $(".selectDokter").select2({
            placeholder: "",
            minimumInputLength: 3,  
            allowClear: true,
            language: {
                errorLoading: function () { return "Searching..." } 
            },
            ajax: {
                url: "/rm/inf-kunjungan/get-dokter",
                dataType: "json",
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {                
                  return {
                    results: data.result
                  };
                }                   
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
    });
', View::POS_END, 'b-index');
?>