<?php

/**
 * @Author  : M.ilhamsyah.P
 * @Date    : 2020-08-10 13:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan pasien konsul
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

$this->title = Yii::t('fe', 'Laporan Pasien Konsul');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rekam Medik'), 'url' => ['/rm/lap-pasien-konsul']];
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
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'pdf' => [
                        'title' => Yii::t('fe', 'Cetak Pdf'),
                        'attributes'=>[
                            'data-target'=>Url::home().'rm/lap-pasien-konsul/export-pdf?'
                        ],
                    ],
                    'excel' => [
                        'title' => Yii::t('fe', 'Excel'),
                        'attributes'=>[
                            'data-target'=>Url::home().'rm/lap-pasien-konsul/export-excel?'
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
    $(document).on('click', '#button-reset', function() {
        table.draw();
        setMonth()
    });

    // Event Ready
    $(document).ready(function() {

        // Generate Table
        table = $('#example').docoTabel({
            filter: true,
            
            sorting: [[5, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl + 'rm/lap-pasien-konsul/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t("fe", "Tanggal Pendaftaran"))."', data: 'tgl_pendaftaran'},
                {title: '".(\Yii::t("fe", "No Pendaftaran"))."', data: 'no_pendaftaran', searchable: false, orderable:false},
                {title: '".(\Yii::t("fe", "No Rekam Medik"))."', data: 'no_rekam_medik',searchable: false, orderable:false},
                {title: '".(\Yii::t("fe", "Nama Pasien"))."', data: 'nama_pasien', searchable: false, orderable:false},
                {title: '".(\Yii::t("fe", "Tanggal Konsul"))."', data: 'tgl_konsulpoli'},
                {title: '".(\Yii::t("fe", "Dokter Pengirim"))."', data: 'nama_dokter', searchable: false, orderable:false},
                {title: '".(\Yii::t("fe", "Ruangan Asal"))."', data: 'ruangan_asal', orderable:false},
                {title: '".(\Yii::t("fe", "Dokter Rujukan"))."', data: 'dok_mengkonsul', searchable: false, orderable:false},
                {title: '".(\Yii::t("fe", "Ruangan Tujuan"))."', data: 'ruangan_tujuan', searchable: false, orderable:false},
            ],
        });

        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                5,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' value='.date('1-M-Y')' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' value='date('d-M-Y')' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=4 readonly='true'></div>\"
            ],
            [
                1,
                \"<div class='input-group'><input type='text' id='start' class='form-control startDate2' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='finish' class='form-control endDate2' /><input type='text' style='display:none' class='targetDate2' col-index=2 readonly='true'></div>\"
            ],
            [
                7,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('Ruangan', '', 
                        ArrayHelper::map($data_ruangan, 'ruangan_nama', 'ruangan_nama'), 
                        [
                            'class' => 'form-control select2', 
                            'prompt' => \Yii::t('fe', '-- Pilih Ruangan--'),
                        ]
                    )
                ))."<div>\"
            ],
          
        ], 
        {
            5:0, 1:1, 7:2
        });
        setMonth()
        function setMonth(){
            var bb = $('#rangeDemoFinish').val()
            var monthsFull= [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sept',
                'Okt',
                'Nov',
                'Des'
            ]
            console.log(monthsFull)
           
            var arrAwal = bb.split('-');
            var month = getMonth(arrAwal[1])
            var kurang = month - 2
          
            var result = arrAwal[0]+ '-'+ monthsFull[kurang]+ '-' +arrAwal[2]
            
            $('#rangeDemoStart').val(result)
           
        }
        function getMonth(monthStr){
            return new Date(monthStr+'-1-01').getMonth()+1
        }
        $('#button-cari').click(function(){
            
        var awal = $('#rangeDemoStart').val()
        var akhir = $('#rangeDemoFinish').val()
        var arrAwal = awal.split('-');
        var arrAkhir = akhir.split('-');
        var awalM = getMonth(arrAwal[1])
        var akhirM = getMonth(arrAkhir[1])

        var start = $('#start').val()
        var finish = $('#finish').val()
        var arrStart = start.split('-');
        var arrFinish = finish.split('-');
        var startM = getMonth(arrStart[1])
        var finishM = getMonth(arrFinish[1])

        if ((akhirM - awalM > 3) || (akhirM - awalM == 3 && arrAkhir[0] - arrAwal[0] >= 0) || (arrAwal[2] != arrAkhir[2]) ) {
            docoNotification('error','Kapasitas Hanya 3 Bulan!', 'Range Data Melebihi Kapasitas');
            return false
        }else if((finishM - startM > 3) || (finishM - startM == 3 && arrFinish[0] - arrStart[0] >= 0) || (arrStart[2] != arrFinish[2]) ){
            docoNotification('error','Kapasitas Hanya 3 Bulan!', 'Range Data Melebihi Kapasitas');
            return false
        }else{
            $('.cari').addClass('data-filter');
        }
        });

        dateRangeHelper('.startDate','.endDate','.targetDate');
        dateRangeHelper('.startDate2','.endDate2','.targetDate2',true);
    });
    
    ", View::POS_END, 'b-index');
?>
