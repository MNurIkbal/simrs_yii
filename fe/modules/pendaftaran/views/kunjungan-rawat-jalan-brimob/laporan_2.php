<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;

$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>

<style>
    .kotak{
        display:block;
        margin: 5px 5px 5px 5px;
    }
    .sembunyikan{
        display:none;
    }

    .multiselect-container>li>a>label>input{
        margin-left:15px !important;
    }

</style>

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
                      <h3 class="panel-title"><b><?=$title; ?></b></h3>
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
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'id'=>'reset-lap-rajal',
                                // 'data-parent'=>'.filter-form'
                            ]
                        ],
                        // 'pdf',
                        'excel',
                        
                    ],'#laporan');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="row">
                
                </div>
                <table id="laporan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tgl pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No rekam medik");?></th>
                            <th><?=\Yii::t("fe", "Nama pasien");?></th>
                            <th><?=\Yii::t("fe", "Jenis kelamin");?></th>
                            <th><?=\Yii::t("fe", "Umur");?></th>
                            <th><?=\Yii::t("fe", "Golongan umur");?></th>
                            <th><?=\Yii::t("fe", "Agama");?></th>
                            <th><?=\Yii::t("fe", "Status perkawinan");?></th>
                            <th><?=\Yii::t("fe", "Pekerjaan");?></th>
                            <th><?=\Yii::t("fe", "Alamat");?></th>
                            <th><?=\Yii::t("fe", "Kota/kab.");?></th>
                            <th><?=\Yii::t("fe", "Kunjungan");?></th>
                            <th><?=\Yii::t("fe", "Cara masuk");?></th>
                            <th><?=\Yii::t("fe", "Jenis kasus penyakit");?></th>
                            <th><?=\Yii::t("fe", "Cara bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Rujukan");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Dokter");?></th>
                            <th><?=\Yii::t("fe", "Kelas pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Status pulang");?></th>
                            <th></th>
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


<?php
$this->registerJs('

    // Global Var
    var table;

    

    // Event Reload
    $(document).on("click", ".data-reload", function    () {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {

        $("#propinsi_id").hide();
        $("#kabupaten_id").hide();
        $("#golongan_umur").show();

        

        $(function(){
            $(".daterange").daterangepicker({
                applyClass: "bg-slate-600",
                cancelClass: "btn-default",
                locale: {
                    format: "DD MMM YYYY"
                }
            });
        });

        // Generate Table
        table = $("#laporan").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/kunjungan-rawat-jalan-brimob/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                }, // 0
                {title: "'.(\Yii::t("fe", "Tanggal pendaftaran")).'", data: "tgl_pendaftaran"}, // 1
                {title: "'.(\Yii::t("fe", "No pendaftaran")).'", data: "no_pendaftaran", searchable:false}, // 2
                {title: "'.(\Yii::t("fe", "No rekam medik")).'", data: "no_rekam_medik", searchable:false}, // 3
                {title: "'.(\Yii::t("fe", "Nama pasien")).'", data: "nama_pasien",searchable: false}, // 4
                {title: "'.(\Yii::t("fe", "Jenis kelamin")). '", data: "jeniskelamin",searchable: false}, // 5
                {title: "'.(\Yii::t("fe", "Tanggal Lahir")).'", data: "tanggal_lahir",searchable: false}, // 6
                {title: "'.(\Yii::t("fe", "Umur")).'", data: "umur",searchable: false}, // 7
                {title: "'.(\Yii::t("fe", "Golongan umur")).'", data: "golonganumur_nama",searchable: false}, // 8
                {title: "'.(\Yii::t("fe", "Agama")).'", data: "agama",searchable: false}, // 9
                {title: "'.(\Yii::t("fe", "Status perkawinan")).'", data: "statusperkawinan",searchable: false}, // 10
                {title: "'.(\Yii::t("fe", "Pekerjaan")).'", data: "pekerjaan_nama",searchable: false}, // 11
  
                {title: "' . (\Yii::t("fe", "Provinsi")) . '", data: "propinsi_nama",searchable: false}, // 12
                {title: "'.(\Yii::t("fe", "Kota/kab")).'", data: "kabupaten_nama",searchable: false}, // 13
                {title: "'.(\Yii::t("fe", "Kunjungan")). '", data: "kunjungan",searchable: false}, // 14
                {title: "' . (\Yii::t("fe", "Jenis kasus penyakit")) . '", data: "jeniskasuspenyakit_nama",searchable: false}, // 15
                {title: "'.(\Yii::t("fe", "Cara masuk")).'", data: "caramasuk_nama",searchable: false}, // 16
                
                {title: "'.(\Yii::t("fe", "Cara bayar")).'", data: "carabayar_nama",searchable: false}, // 17
                {title: "'.(\Yii::t("fe", "Penjamin")).'", data: "penjamin_nama",searchable: false}, // 18
                {title: "'.(\Yii::t("fe", "Rujukan")).'", data: "nama_perujuk",searchable: false}, // 19
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama",searchable: false}, // 20
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "nama_pegawai",searchable: false}, // 21
                {title: "'.(\Yii::t("fe", "Kelas pelayanan")).'", data: "kelaspelayanan_nama",searchable: false}, // 22
                {title: "'.(\Yii::t("fe", "Status pulang")).'", data: "status_periksa",searchable: false}, // 23
                {title: "'.(\Yii::t("fe", "Cara bayar")).'", data: "carabayar_id", visible:false}, // 24
                {title: "'.(\Yii::t("fe", "Nama Penjamin")).'", data: "penjamin_id", visible:false}, // 25
                {title: "'.(\Yii::t("fe", "Kolom")). '", data: "toggle", visible:false}, // 26
                {title: "'.(\Yii::t("fe", "Instalasi")). '", data: "instalasi_id", visible:false}, // 27
                {title: "' .(\Yii::t("fe", "Ruangan")). '", data: "ruangan_id", visible:false}, // 28
                {title: "' .(\Yii::t("fe", "Jenis pencarian")). '", data: "toggle", visible:false,searchable: true}, // 29
                {title: "' . (\Yii::t("fe", "Provinsi")) . '", data: "propinsi_id", visible:false,searchable: true}, // 30
                {title: "' . (\Yii::t("fe", "Kabupaten / Kota")) . '", data: "kabupaten_id", visible:false,searchable: true}, // 31
                {title: "' . (\Yii::t("fe", "Golongan umur")) . '", data: "golonganumur_id",searchable: true,visible: false}, // 32
              
            ],
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 4,
            // }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [
                    1,
                    \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="' . date('d-M-Y') . '"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="' . date('d-M-Y') . '" /><input type="text" style="display:none" class="targetDate"></div>\'
                ],
                [
                    24,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'carabayar_id',
                                '',
                                $carabayarList,
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'carabayar_id',
                                    'prompt' => \Yii::t('fe', '--pilih cara bayar--')
                                ]
                            )
                        )
                    ). '\'
                ],
                [
                    32,
                    \'' . (preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'golongan_umur',
                            '',
                            $golonganUmurList,
                            [
                                'class' => 'form-control select2',
                                'id' => 'golongan_umur',
                                'prompt' => \Yii::t('fe', '--Golongan Umur--')
                            ]
                        )
                    )) . '\'
                ],
                [
                    30,
                    \'' . (preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'propinsi_id',
                            '',
                            $propinsiList,
                            [
                                'class' => 'form-control select2',
                                'id' => 'propinsi_id',
                                'prompt' => \Yii::t('fe', '--Propinsi--')
                            ]
                        )
                    )) . '\'
                ],
                [
                    31,
                    \'' . (preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        DepDrop::widget(
                            [
                                'name' => 'kabupaten_id',
                                'options' => [
                                    'id' => 'kabupaten_id',
                                    'class' => 'select2',
                                ],
                                'pluginOptions' => [
                                    'depends' => ['propinsi_id'],
                                    'placeholder' => \Yii::t('fe', '--pilih Kabupaten / Kota--'),
                                    'url' => Url::to(['/pendaftaran/kunjungan-rawat-jalan-brimob/list-kabupaten'])
                                ]
                            ]
                        )
                    )) . '\'
                ],
                [
                    25,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DepDrop::widget(
                                [
                                    'name'=>'penjamin_id',
                                    'options'=>[
                                        'id'=>'penjamin_id',
                                        'class'=>'select2',
                                    ],
                                    'pluginOptions'=>[
                                        'depends'=>['carabayar_id'],
                                        'placeholder'=>\Yii::t('fe', '--pilih penjamin--'),
                                        'url'=>Url::to(['/master/penjamin/list-penjamin'])
                                    ]
                                ]
                            )
                        )
                    ). '\'
                ],
                [
                    27,
                    \'' . (preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'instalasi_id',
                            '',
                            $instalasiList,
                            [
                                'class' => 'form-control select2',
                                'id' => 'instalasi_id',
                                'prompt' => \Yii::t('fe', '--pilih Instalasi--')
                            ]
                        )
                    )) . '\'
                ],
                [
                    28,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DepDrop::widget(
                                [
                                    'name' => 'ruangan_id',
                                    'options' => [
                                        'id' => 'ruangan_id',
                                        'class' => 'select2',
                                    ],
                                    'pluginOptions' => [
                                        'depends' => ['instalasi_id'],
                                        'placeholder' => \Yii::t('fe', '--pilih Ruangan--'),
                                        'url' => Url::to(['/pendaftaran/kunjungan-rawat-jalan-brimob/list-ruangan'])
                                    ]
                                ]
                            )
                        )
                    ).'\'
                ],
                [
                    26,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                '',
                                '',
                                $listKolom,
                                [
                                    'class' => 'multiselect',
                                    'id' => 'toggle_tablex',
                                    'prompt' => \Yii::t('fe', '--pilih Kolom yang ditampilkan--'),
                                    'multiple' => 'multiple',
                                ]
                            )
                        )
                    ). '\'
                ],
                [
                    29,
                    \'' . (preg_replace(
                        "/[\n\t\r]/i",
                        '',
                        Html::dropDownList(
                            'jenis_pencarian',
                            '',
                            $jenisPencarian,
                            [
                                'class' => 'form-control select2',
                                'id' => 'jenis_pencarian',
                                'prompt' => \Yii::t('fe', '--jenis pencarian--')
                            ]
                        )
                    )) . '\'
                ],

            ],{
          1:0,
          23:1,
          24:1,
          25:2,
          29:4,
          32:5,
          30:6,
          31:7,
          27:8,
          28:9,
          26:10,
        }
        );

        dateRangeHelper(".startDate", ".endDate", ".targetDate",true);

        $("#toggle_tablex").multiselect({
            buttonWidth: "100%"
        });
        // cek filter kebutuhan
            $("#toggle_tablex").multiselect("select",'.$dataKolomList. ')

            var kolom_show = $("#toggle_tablex").val();

            if(kolom_show.length > 0){                
                for (i = 0; i <= 26; i++) {
                    table.column(i).visible(false);
                } 

                $.each(kolom_show, function(i, item){
                    // console.log(item);
                    table.column(item).visible(true);
                });

                if(kolom_show.length == 1 && kolom_show[0] == ""){
                    location.reload();
                }

            }

        // cek filter kebutuhan
        
        $("#propinsi_id").parent().attr("id","prop_id");
        $("#kabupaten_id").parent().attr("id","kab_id");

        $("#prop_id").hide();
        $("#kab_id").hide();


        //show / hide pencarian
        $(document).on("change","#toggle_tablex",function(){
            
            var kolom_show = $(this).val();

            if(kolom_show.length > 0){
                console.log(kolom_show);
                 $.post("simpan-kolom", {filter_kebutuhan: kolom_show}, function(result){
                    
                });
                
                for (i = 0; i <= 26; i++) {
                    table.column(i).visible(false);
                } 

                $.each(kolom_show, function(i, item){
                    // console.log(item);
                    table.column(item).visible(true);
                });

                if(kolom_show.length == 1 && kolom_show[0] == ""){
                    location.reload();
                }

            }else{
                location.reload();
            }
        });

        $(document).on("change","#jenis_pencarian",function(){
            
            $("#propinsi_id").parent().attr("id","prop_id");
            $("#kabupaten_id").parent().attr("id","kab_id");
            $("#golongan_umur").parent().attr("id","golongan");

            $("#prop_id").hide();
            $("#kab_id").hide();
            $("#golongan").hide();


            var jenis_pencarian = $(this).val();
            //console.log(jenis_pencarian);
            switch(jenis_pencarian) {
                case "1":
                    $("#toggle_tablex").multiselect("deselect",[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]);
                    $("#toggle_tablex").multiselect("select",[0,1,2,3,4,7,8,20]);
                     var kolom_show = $("#toggle_tablex").val();
                   
                    if(kolom_show.length > 0){
                        // console.log(kolom_show);
                        
                        for (i = 0; i <= 26; i++) {
                            table.column(i).visible(false);
                        } 

                        $.each(kolom_show, function(i, item){
                            // console.log(item);
                            table.column(item).visible(true);
                        });

                        if(kolom_show.length == 1 && kolom_show[0] == ""){
                            location.reload();
                        }

                    }

                    $("#golongan").show();
                    $("#prop_id").hide();
                    $("#kab_id").hide();
                    break;
                case "2":
                    $("#golongan_umur").hide();
                    $("#prop_id").show();
                    $("#kab_id").show();

                    $("#toggle_tablex").multiselect("deselect",[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]);
                    $("#toggle_tablex").multiselect("select",[0,1,2,3,4,12,13,20]);
                     var kolom_show = $("#toggle_tablex").val();
            
                    if(kolom_show.length > 0){
                        //  console.log(kolom_show);
                        
                        for (i = 0; i <= 26; i++) {
                            table.column(i).visible(false);
                        } 

                        $.each(kolom_show, function(i, item){
                            // console.log(item);
                            table.column(item).visible(true);
                        });

                        if(kolom_show.length == 1 && kolom_show[0] == ""){
                            location.reload();
                        }

                    }

                    break;
                default:
                    $("#golongan_umur").show();
                    $("#prop_id").hide();
                    $("#kab_id").hide();

                    $("#toggle_tablex").multiselect("deselect",[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23]);
                    $("#toggle_tablex").multiselect("select",[0,1,2,3,4,7,8]);
                     var kolom_show = $("#toggle_tablex").val();
            
                    if(kolom_show.length > 0){
                        // console.log(kolom_show);
                        
                        for (i = 0; i <= 26; i++) {
                            table.column(i).visible(false);
                        } 

                        $.each(kolom_show, function(i, item){
                            // console.log(item);
                            table.column(item).visible(true);
                        });

                        if(kolom_show.length == 1 && kolom_show[0] == ""){
                            location.reload();
                        }

                    }
            }

        });

        $(document).on("change", "#toggle_table", function (e) {
            e.preventDefault();

            for (i = 0; i <= 26; i++) {
                table.column(i).visible(false);
            }

            var show = new Array();
            switch ($(this).val()) {
                case "":
                    show = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
                    break;
                case "1": // umur
                    show = [0, 1, 2, 3, 4, 6, 7];
                    break;
                case "2": // jenis kelamin
                    show = [0, 1, 2, 3, 4, 5];
                    break;
                case "3": // status kunjungan
                    show = [0, 1, 2, 3, 4, 13];
                    break;
                case "4": // agama
                    show = [0, 1, 2, 3, 4, 8];
                    break;
                case "5": // pekerjaan
                    show = [0, 1, 2, 3, 4, 10];
                    break;
                case "6": // status pekerjaan
                    show = [0, 1, 2, 3, 4, 10];
                    break;
                case "7": // status perkawinan
                    show = [0, 1, 2, 3, 4, 9];
                    break;
                case "8": // alamat
                    show = [0, 1, 2, 3, 4, 11];
                    break;
                case "9": // kabupaten kota
                    show = [0, 1, 2, 3, 4, 12];
                    break;
                case "10": // cara masuk
                    show = [0, 1, 2, 3, 4, 14];
                    break;
                case "11": // rujukan
                    show = [0, 1, 2, 3, 4, 18];
                    break;
                case "12": // jenis kasus penyakit
                    show = [0, 1, 2, 3, 4, 15];
                    break;
                case "13": // keterangan pulang
                    show = [0, 1, 2, 3, 4, 22];
                    break;
                case "14": // dokter pemeriksa
                    show = [0, 1, 2, 3, 4, 20];
                    break;
                case "15": // kelas pelayanan
                    show = [0, 1, 2, 3, 4, 21];
            }

            $.each(show, function(i, item){
                table.column(item).visible(true);
            });
        });

        $("#reset-lap-rajal").on("click", function () {
            location.reload();
        })
    });

    $(".box-pilih").on("click", function (e) {
        //e.preventDefault();
 
        // Get the column API object
        console.log($(this).val());
        var column = table.column($(this).val());
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

', View::POS_END, 'b-index');
?>
