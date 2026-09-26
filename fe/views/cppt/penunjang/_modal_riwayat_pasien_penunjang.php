<?php


use yii\web\View;
use app\components\DocoHelpers;
use app\widgets\DHInfiniteTableWidget;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;

$this->title = \Yii::t('fe', 'Riwayat Pasien');
$this->params['breadcrumbs'][] = ['label' => $instalasi, 'url' => [$url]];
$this->params['breadcrumbs'][] = $title;
?>

<style lang="">
    .info-pasien {
        width: 70px;
        height: 30px;
        border-radius: 5px;
        border: 1px solid black;
        float: left;
        margin: 3px;
    }

    .btn-riwayat {
        margin-bottom: 5px;
        margin-right: 5px;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <?php if($aksesRiwayatPasien) : ?>
    <div class="row">
        <div class="col-md-12">
          <div class="panel-toolbar clearfix">
              <div class="col-md-6">
                  <?=
                  DocoHelpers::generateToolbar([
                      'riwayat-pasien' => [
                          'type'  => 'button',
                          'title' => Yii::t('fe', 'Berkas Pasien'),
                          'icon'  => 'fa fa-file',
                          'attributes' => [
                              'id'          => 'btn-berkas-pasien',
                              'data-width'  => '50%',
                              'data-toggle' => 'modal',
                              'data-target' => '#modal_berkas_pasien',
                              'action'      => '/rajal/pemeriksaan/modal-berkas-pasien?no_rekam_medik='.$norm,
                          ]
                      ],
                  ]) ?>
              </div>
          </div>
        </div>
        
        <div class="col-md-12">
            <?php
                if ($is_penunjang == null) {
                    $columns = [
                        [
                            'title' => Yii::t('fe', 'Tanggal kunjungan / No pendaftaran'),
                            'data' => 'tgl_pendaftaran',
                            'name' => 'tgl_pendaftaran',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Ruangan / Kamar'),
                            'data' => 'ruangan_pend',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Dokter pemeriksa'),
                            'data' => 'dok_rjrd',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Pelayanan Pasien'),
                            'data' => 'aksi_pelayanan',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Penunjang'),
                            'data' => 'aksi_penunjang',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Cara Keluar'),
                            'data' => 'cara_keluar',
                            'searchable' => false,
                            'orderable' => false
                        ]
                    ];
                    $formFilters = [
                        [
                            'fieldName' => 'ruangan_pend_id',
                            'label' => 'Ruangan',
                            'type' => [
                                'name' => 'select',
                                'payload' => $listRuangan
                            ]
                        ],
                        [
                            'fieldName' => 'dok_rjrd_id',
                            'label' => 'Dokter Pemeriksa',
                            'type' => [
                                'name' => 'select',
                                'payload' => $listDokter
                            ]
                        ],
                    ];
                } else {
                    $columns = [
                        [
                            'title' => Yii::t('fe', 'Tanggal kunjungan / No pendaftaran'),
                            'data' => 'tgl_pendaftaran',
                            'name' => 'tgl_pendaftaran',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Ruangan / Kamar'),
                            'data' => 'ruangan_pend',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Dokter pemeriksa'),
                            'data' => 'dok_rjrd',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Penunjang'),
                            'data' => 'aksi_penunjang',
                            'searchable' => false,
                            'orderable' => false
                        ]
                    ];
                    $formFilters = [];
                }
            ?>
            <?=
                DHInfiniteTableWidget::widget([
                    'id' => 'table-riwayat',
                    'ajax' => [
                        'url' => 'igd/riwayat-pasien/get-data-history-patient',
                        'data' => [
                                'norm' => $norm,
                                'is_modal' => true,
                                'is_jenis' => $is_jenis == false ? '' : $is_jenis,
                                'instalasi_id' => $instalasi_id,
                            ]
                    ],
                    'limit' => 5,
                    'columns' => $columns,
                    'formFilters' => $formFilters,
                    'functions' => [
                        'data' => "function (data) {
                            data.advancedFilter = data.advancedFilter || {};
                            data.advancedFilter['ruangan_pend_id'] = $('#table-riwayat-ruangan_pend_id--form').val();
                            data.advancedFilter['dok_rjrd_id'] = $('#table-riwayat-dok_rjrd_id--form').val();
                        }",
                        'filterRendered' => "function (wrapper) {
                            $('#advanced-filter-table-riwayat').find('.flex-1').css('display', 'none')
                            setTimeout(function () {
                                $('#ruangan_pend_id--filter').find('.select2').remove()
                                $('#table-riwayat-ruangan_pend_id--form').select2()

                                $('#dok_rjrd_id--filter').find('.select2').remove()
                                $('#table-riwayat-dok_rjrd_id--form').select2()

                                $('#table-riwayat-ruangan_pend_id--form').bind('change', () => {
                                    $('#btn-search__table-riwayat').trigger('click')
                                })
                                $('#table-riwayat-dok_rjrd_id--form').bind('change', () => {
                                    $('#btn-search__table-riwayat').trigger('click')
                                })
                            }, 300);
                        }"
                    ]
                ]);
            ?>
        </div>
    </div>
    <hr>
    <h5 class="panel-title"><b>Informasi Dokumen</b></h5>
    <div class="row">
        <div class="col-md-12">
            <table id="table-upload-dokumen" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead class="text-center">
                    <tr class="bg-inverse">
                        <th style="width: 99px;"><?= Yii::t('fe', 'Tanggal') ?></th>
                        <th style="width: 59px;"><?= Yii::t('fe', 'Ruangan') ?></th>
                        <th style="width: 78px;"><?= Yii::t('fe', 'Dokter') ?></th>
                        <th style="width: 484px;"><?= Yii::t('fe', 'Nama Dokumen') ?></th>
                        <th style="width: 484px;"><?= Yii::t('fe', 'Upload Dokumen') ?></th>
                    </tr>
                </thead>
                <tbody> 
                </tbody>
            </table>
        </div>
    </div>
    <?php else : ?>
    <br>
        <div class="alert alert-danger" role="alert">
        Mohon maaf, akses anda dibatasi.
        </div>
    <?php endif; ?>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>

<?php
$this->registerJs("
    var url = '$url'
    var instalasi_id = '$instalasi_id'
    var is_jenis = '" . ($is_jenis ? $is_jenis : "false") . "'
    var is_modal = true
    var norm = '$norm'
    var listRuangan = " . json_encode($listRuangan) . "
    var listDokter = " . json_encode($listDokter) . "
", View::POS_END);

$this->registerJs("
var table_column_upload = [
    {title: '".(\Yii::t('fe', 'Tanggal kunjungan / No pendaftaran'))."',  data: 'doc_date', name: 'doc_date'},
    {title: '".(\Yii::t('fe', 'Ruangan / Kamar'))."',  data: 'ruangan_nama'},
    {title: '".(\Yii::t('fe', 'Dokter pemeriksa'))."',  data: 'nama_pegawai'},
    {title: '".(\Yii::t('fe', 'Nama Dokumen'))."',  data: 'nama_dokumen'},
    {title: '".(\Yii::t('fe', 'Dokumen'))."',  data: 'aksi_non_pendaftaran'},
];
// var formfilt = []
", View::POS_END); 
$this->registerJs($this->render('_modal_riwayat_pasien_penunjang.js'), View::POS_END);
?>