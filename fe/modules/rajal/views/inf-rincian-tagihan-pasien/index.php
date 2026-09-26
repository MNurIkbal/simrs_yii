<?php

/**
 * @Author: ayip
 * @Date:   2018-01-12 15:47:03
 * @Last Modified by: ayip
 * @Last Modified time: 2018-01-26 16:33:35
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
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

            <div class="panel-toolbar clearfix">                
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'detail',
                        // 'print',
                        // 'pdf',
                        // 'excel',
                        // 'add',
                        
                    ]);?>                
            </div>

         	<div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">                        
                    </div>
                </div>

                <table id="inf-rincian-tagihan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">                                        
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No. Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No. Rekan Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Dokter");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                            <th><?=\Yii::t("fe", "Total Tagihan");?></th>
                            <th><?=\Yii::t("fe", "Status Bayar");?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php /* list data */ ?>
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
        table = $('#inf-rincian-tagihan').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            sorting: [[2, 'asc']], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rajal/inf-rincian-tagihan-pasien/get-data',
            columns: [                
                {
                    title: '',
                    data: null,
                    defaultContent: '',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Pendaftaran'))."', data: 'tgl_daftar'},
                {title: '".(\Yii::t('fe', 'No. Pendaftaran'))."', data: 'no_pendaftaran'},
                {title: '".(\Yii::t('fe', 'No. Rekan Medik'))."', data: 'no_rekam_medik'},
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."',  data: 'nama_pasien'},
                {title: '".(\Yii::t('fe', 'Cara Bayar'))."', data: 'carabayar_nama'},
                {title: '".(\Yii::t('fe', 'Penjamin'))."', data: 'penjamin_nama'},
                {title: '".(\Yii::t('fe', 'Dokter'))."', data: 'nama_pegawai'},
                {title: '".(\Yii::t('fe', 'Kelas Pelayanan'))."', data: 'kelaspelayanan_nama'},
                {title: '".(\Yii::t('fe', 'Jenis Kasus Penyakit'))."', data: 'jeniskasuspenyakit_nama'},
                {title: '".(\Yii::t('fe', 'Total Tagihan'))."', data: 'total_tagihan',searchable: false},
                {title: '".(\Yii::t('fe', 'Status Bayar'))."', data: 'statusbayar_nama',searchable: false},
            ],            
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                6,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('carabayar_id', '', $listCaraBayar, 
                            [
                                'class' => 'form-control select2 ddl_carabayar', 
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>6,
                            ]
                        )
                    )))."\"
            ],
            [
                8,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('pegawai_id', '', $listDokterRajal, 
                            [
                                'class' => 'form-control select2 ddl_dokter', 
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>8,
                            ]
                        )
                    )))."\"
            ],
            [
                9,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('kelaspelayanan_id', '', array(), 
                            [
                                'class' => 'form-control select2 ddl_kelas_pelayanan', 
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>9,
                            ]
                        )
                    )))."\"
            ],
            [
                10,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('jeniskasuspenyakit_id', '', array(), 
                            [
                                'class' => 'form-control select2 ddl_jenis_kasus_penyakit', 
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>10,
                            ]
                        )
                    )))."\"
            ],

        ],{
            2:0,
            3:1,
            4:2,
            5:3,
            6:4,
            7:5,
            8:6,
            9:7,
            10:8
        });
        
        dateRangeHelper('.startDate','.endDate','.targetDate');

        $('.ddl_kelas_pelayanan').select2({
	        minimumInputLength: 3,  
	        ajax: {
	            url: '/master/kelas/get-kelas-pelayanan',
	            dataType: 'json',
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
	        dropdownCssClass: 'bigdrop',
	        escapeMarkup: function (m) { return m; },
	    });

		$('.ddl_jenis_kasus_penyakit').select2({
	        minimumInputLength: 3,  
	        ajax: {
	            url: '/master/jenis-kasus-penyakit/get-jenis-kasus-penyakit',
	            dataType: 'json',
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
	        dropdownCssClass: 'bigdrop',
	        escapeMarkup: function (m) { return m; },
	    });

        var primaryKey;

        $('#inf-rincian-tagihan tbody').on('click', 'tr', function(){
                // console.log(pri)
                primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;    
                if(primaryKey){
                    $('.data-edit').attr('action',$('.data-edit').data('target')+primaryKey);
                    $('.data-detail').attr('href',$('.data-detail').data('target')+primaryKey);                                        
                }else{
                    $('.data-edit').removeAttr('action');
                    $('.data-detail').removeAttr('href');                                        
                }
                
            });

        $(document).on('click', '.data-detail', function(){            
            window.location = $(this).attr('href');
        })
        
        // $(document).on('click', '.data-add', function(){            
        //     window.location = $(this).data('target');
        // })

    });", View::POS_END, 'js-kuning');
?>