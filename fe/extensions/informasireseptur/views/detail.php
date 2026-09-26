<?php

/**
 * @author Randy Vianda Putra
 * @copyright 17 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Informasi Reseptur', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    table{
      margin: 0 auto;
      width: 100%;
      clear: both;
      border-collapse: collapse;
      table-layout: fixed;
      word-wrap:break-word;
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
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'print'=>[
                        'attributes'=>[
                            'data-target'=>'/apotek/informasi-reseptur/print-resep?id='.$id.'&noresep='.$no_resep.'&',
                        ]
                    ],
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batalkan Resep'),
                        'icon' => 'fa fa-times',
                        'method' => '#',
                        'attributes' => [
                            'id' => 'btn-batal-resep',
                            'data-options'=>'click'
                        ]
                    ]
                ]);?>
            </div>
            <div class="panel-body" style="padding:10px;">
                <!-- Informasi Resep -->
                <div class="col-md-3">
                    <div class="panel panel-default" id="informasi" style="margin-top:10px;">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Informasi Resep Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?php
                                    if (preg_match('/^RST/', $no_resep)) {
                                        echo "Nomor Reseptur";
                                    } else {
                                        echo "Nomor Resep";
                                    }
                                    ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_resep ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'No pendaftaran') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_pendaftaran ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'No Rekam Medik') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_rekam_medik ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Nama pasien') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $nama_pasien ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Tanggal Lahir') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $tgl_lahir ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Dokter resep') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $nama_pegawai ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Instalasi') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $instalasi_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Ruangan') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $ruangan_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Cara bayar') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $carabayar_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Penjamin') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $penjamin_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Diagnosa') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $diagnosa ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Alergi') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?php
                                        //Aris ToDo
                                        $string = '';
                                        if (isset($alergi) && !empty($alergi) && is_array($alergi)) {
                                            foreach ($alergi as $key => $value) {
                                                $valAlergi = $value['riwayat_alergi'];
                                                $replace = str_replace("-", "," , strip_tags($valAlergi));
                                                $replace = preg_replace('/["\[\]]/i', "", $replace);
                                                $list = explode(",", $replace);
                                                $list = array_filter($list);
                                                $cntList = count($list);
                                                $string = '';
                                                    if($cntList > 1) {
                                                        $string = '<ol>';
                                                        foreach ($list as $val) {
                                                            $string .= '<li>' . strip_tags($val) .'</li>';
                                                        }

                                                        $string .= '</ol>';
                                                    } else {
                                                        foreach ($list as $val) {
                                                            $string = $val;
                                                            if($string != strip_tags($string)){
                                                                $string = '';
                                                            }
                                                        }
                                                    }
                                            }
                                            echo $string;
                                        } else {
                                            echo '-';
                                        }
                                        //Aris ToDo
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Iter') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $iter ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Catatan') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= strip_tags($catatan) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default" id="informasi" style="margin-top:10px;">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Informasi Obat Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="example"
                                data-source="<?= Url::home(); ?>apotek/transaksi-resep/list-resep"
                                data-filter=".form-filter" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Racikan').' / '.Yii::t('fe', 'Non Racikan') ?></th>
                                        <th><?= Yii::t('fe', 'R ke') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                        <th><?= Yii::t('fe', 'Signa') ?></th>
                                        <th><?= Yii::t('fe', 'Harga Satuan (Rp.)') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Catatan') ?></th>
                                        <th><?= Yii::t('fe', 'Sub Total (Rp.)') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="list-obat">
                                    <tr>
                                        <td colspan="8" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan') ?></td>
                                    </tr>
                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs("
        var btn;
        $(document).ready(function(){
            btn = $('.data-copy').clone();
            var _test = function (data) {
                if($('.footer-total').length < 1){
                    $('.datatable-basic').find('tfoot').append('<tr class=\'footer-subtotal\'><td class=\'text-right\' style=\'width: 69%\'></td><td><strong>Sub Total (Rp.)</strong></td><td class=\'text-right\'>'+data.subtotalobat+'</td></tr>');
                    $('.datatable-basic').find('tfoot').append('<tr class=\'footer-biayaadmin\'><td class=\'text-right\' style=\'width: 69%\'></td><td><strong>Biaya Admin (Rp.)</strong></td><td class=\'text-right\'>'+'".$biayaadministrasi."'+'</td></tr>');
                    $('.datatable-basic').find('tfoot').append('<tr class=\'footer-total\'><td class=\'text-right\' style=\'width: 69%\'></td><td><strong>Total (Rp.)</strong></td><td class=\'text-right\'>'+'".$totaltagihan."'+'</td></tr>');
                }
            }
            table = $('#example').docoTabel({
                filter: true,
                sorting: [[2, 'asc']],
                bInfo: false,
                paging: false,
                bPaginate: false,
                // displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: function(data, callback, settings){
                    $.ajax({
                        url: baseUrl+'apotek/informasi-reseptur/get-data-obat?id=".$_GET['id']."&noresep=".$nomor."',
                        data: data,
                        success: function(data)
                        {
                            _test(data)
                            callback(data);
                        }
                    });

                },
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: '".(\Yii::t('fe', 'Racikan')).' <br>/ '.(\Yii::t('fe', 'Non Racikan'))."', data: 'jenis_racikan', name : 'rke',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'R ke'))."', data: 'rke',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'nama obat alkes'))."', data: 'obatalkes_nama',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Signa'))."', data: 'signa_nama',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Harga satuan').' (Rp.) ')."', data: 'hargajual_satuan',searchable: false,orderable: false, class:'text-right' },
                    {title: '".(\Yii::t('fe', 'Qty'))."',  data: 'qty_reseptur',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'satuan'))."',  data: 'satuan_input',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Catatan'))."',  data: 'etiket',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Sub Total').' (Rp.) ')."',  data: 'totaltagihan',searchable: false,orderable: false, class:'text-right'},
                ],

            });
            $(document).on('click', '#btn-batal-resep', function(e){
                $(this).docoForm('click', {
                    url: '/apotek/informasi-reseptur/batal-resep?no_resep=".$nomor."',
                    confirmMessage: 'Apakah anda yakin ingin membatalkan resep ini ?',
                    title: 'Sukses',
                    method: 'POST',
                    type: 'json',
                    success: function() {
                        window.location.replace('/apotek/informasi-reseptur');
                    }
                });

            });

        $('.dataTables_filter').hide();
        $(table.table().footer()).html('Your html content here ....');
        if($('.iter').val() == 0 || $('.iter').val() == ''){
            $('.data-copy').attr('disabled','disabled');
        }
    });
    var disableButton = function(){
        $('.data-copy').remove();
        btn.attr('disabled','disabled');
        $('.panel-toolbar').append(btn);

    }

        ",VIEW::POS_END, 'js-kuning');
?>