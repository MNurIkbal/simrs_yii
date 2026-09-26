<?php

/**
 * @author Randy Vianda Putra
 * @copyright 22 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $this->title), 'url' => ['index']];

?>
<style>
.bold {
    font-weight: bold;
}

.tabel {
    font-size: 16px;
}
.tabel td {
    padding: 5px;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title; ?></b></h3>
                <?php echo Breadcrumbs::widget([
                      'homeLink' => [ 
                                      'label' => Yii::t('fe', 'Home'),
                                      'url' => Yii::$app->homeUrl,
                                 ],
                      'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                   ]); 
                ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body" style="padding:10px;">
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?=Yii::t('fe', 'Data pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table width="80%" cellpadding="10" class="tabel">
                            <tbody>
                                <tr>
                                    <td class="bold"><?=Yii::t('fe', 'Instalasi akhir') ?></td>
                                    <td class="">Rawat Jalan</td>
                                    <td class="bold"><?=Yii::t('fe', 'No pendaftaran') ?></td>
                                    <td class="">RJ201801200001</td>
                                    <td class="bold"><?=Yii::t('fe', 'Cara bayar') ?></td>
                                    <td class="">BPJS</td>
                                    
                                </tr>
                                <tr>
                                    <td class="bold"><?=Yii::t('fe', 'Ruangan akhir') ?></td>
                                    <td class="">Bedah Tulang</td>
                                    <td class="bold"><?=Yii::t('fe', 'No rekam medis') ?></td>
                                    <td class="">0014123</td>
                                    <td class="bold"><?=Yii::t('fe', 'Penjamin') ?></td>
                                    <td class="">BPJS</td>
                                </tr>
                                <tr>
                                    <td class="bold"><?=Yii::t('fe', 'Tangal pendaftaran') ?></td>
                                    <td class="">01 Januari 2018</td>
                                    <td class="bold"><?=Yii::t('fe', 'Nama pasien') ?></td>
                                    <td class="">Randy Vianda Putra</td>
                                    <td class="bold"><?=Yii::t('fe', ' Kelas pelayanan') ?></td>
                                    <td class="">Kelas 1</td>
                                </tr>
                                <tr>
                                    <td class="bold"><?=Yii::t('fe', 'Sampai dengan') ?></td>
                                    <td class="">05 Januari 2018</td>
                                    <td class="bold"><?=Yii::t('fe', 'No telepon') ?></td>
                                    <td class="">085793991997</td>
                                    <td class="bold"><?=Yii::t('fe', 'Tanggal keluar') ?></td>
                                    <td class="">05 Januari 2018</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?=Yii::t('fe', 'Detail tagihan pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?=Yii::t('fe', 'Tanggal tindakan') ?></th>
                                    <th><?=Yii::t('fe', 'Instalasi') ?></th>
                                    <th><?=Yii::t('fe', 'Ruangan') ?></th>
                                    <th><?=Yii::t('fe', 'Nama tindakan / obat') ?></th>
                                    <th><?=Yii::t('fe', 'Harga satuan') ?></th>
                                    <th><?=Yii::t('fe', 'Qty') ?></th>
                                    <th><?=Yii::t('fe', 'Sub total') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="default-value">
                                    <td>1</td>
                                    <td>12-08-2017</td>
                                    <td>Rawat Jalan</td>
                                    <td>Apotek</td>
                                    <td>Acarbose</td>
                                    <td>Rp. 5.500</td>
                                    <td>10</td>
                                    <td>Rp. 55.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?=Yii::t('fe', 'Pembayaran') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'ajax-form', 
                                'options' => ['class' => 'form-horizontal'],
                                // 'action' =>['obat-alkes-kasus/create']
                            ]); 
                        ?>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Tanggal pembayaran') ?></label>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <?=Html::textInput('tanggal_pembayaran',null,[
                                                'class' => 'form-control pickadate',
                                                'placeholder' => Yii::t('fe', 'Tanggal pembayaran')
                                            ]);
                                            ?>
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Penggunaan uang muka') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('penggunaan_uang',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'Penggunaan uang muka'),
                                            ]);
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Subsidi asuransi') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('subsidi_asuransi',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'Subsidi asuransi'),
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Total tagihan') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('total_tagihan',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'Total tagihan'),
                                            ]);
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Biaya administrasi') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('biaya_admin',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'Biaya administrasi'),
                                            ]);
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Uang diterima') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('uang_diterima',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'Uang diterima'),
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Uang diterima') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('uang_diterima',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'Uang diterima'),
                                            ]);
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Pembulatan') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('pembulatan',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'Pembulatan')
                                            ]);
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Uang kembalian') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('uang_kembaliam',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'Uang kembalian'),
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?=Yii::t('fe', 'E-Collection') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'ajax-form', 
                                'options' => ['class' => 'form-horizontal'],
                                // 'action' =>['obat-alkes-kasus/create']
                            ]); 
                        ?>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <div class="checkbox" class="styled">
                                    <input type="checkbox">
                                    <label>
                                        <?=Yii::t('fe', 'E-Collection') ?>
                                    </label>
                                </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'Nama pemilik rekening') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('nama_pemilik_rek',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'Nama pemilik rekening')
                                            ]);
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-lg-4 control-label"><?=Yii::t('fe', 'No rekening') ?></label>
                                    <div class="col-lg-8">
                                        <?=Html::textInput('no_rekening',null,[
                                                'class' => 'form-control',
                                                'placeholder' => Yii::t('fe', 'No rekening'),
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?=Html::a('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan'), 
                    ['#'], 
                    [
                        'class' => 'btn bg-teal btn-custom-table save',
                        'title' => Yii::t('fe', 'Simpan'),
                        'data-tooltip' => 'tooltip'
                        ]
                    );
                ?>
                <?=Html::a('<i class="fa fa-refresh"></i> '. Yii::t('fe', "Ulang"),
                    ['#'],
                    [
                        'class' => 'btn btn-lime-green ulang',
                        'title' => Yii::t('fe', 'Ulang'),
                        'data-tooltip' => 'tooltip'
                        ]
                    );
                ?>
                <?=Html::a('<i class="fa fa-file-pdf-o"></i> '. Yii::t('fe', "Print rincian"),
                    ['#'],
                    [
                        'class' => 'btn btn-crimson',
                        'title' => Yii::t('fe', 'Print rincian'),
                        'data-tooltip' => 'tooltip'
                        ]
                    );
                ?>
                <?=Html::a('<i class="fa fa-file-pdf-o"></i> '. Yii::t('fe', "Print kwitansi"),
                    ['#'],
                    [
                        'class' => 'btn btn-crimson',
                        'title' => Yii::t('fe', 'Print kwitansi'),
                        'data-tooltip' => 'tooltip'
                        ]
                    );
                ?>
                <?=Html::a('<i class="fa fa-file-pdf-o"></i> '. Yii::t('fe', "Print BKM"),
                    ['#'],
                    [
                        'class' => 'btn btn-crimson',
                        'title' => Yii::t('fe', 'Print BKM'),
                        'data-tooltip' => 'tooltip'
                        ]
                    );
                ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop-lg" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->
<?php 
    $this->registerJs('
        $(function(){
            var pickdate = $(".pickadate").pickadate({
                formatSubmit: "yyyy-mm-dd",            
            });        
        });
    ');
?>