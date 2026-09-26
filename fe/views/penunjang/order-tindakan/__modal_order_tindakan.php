<?php

/**
* Render form order pemeriksaan
* 
* @return Html
* @author : Budi (budi@sirs.co.id)
* Powered by Sirs
*/

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="list_checkbox">
            <div class="row">                
                <div class="col-sm-12">
                    <div class="col-sm-3">
                        <?= Html::dropDownList('jenispemeriksaanlab_id', null, $dataJenis, [
                            'class' => 'form-control search-jenispemeriksaan',
                            'data-ruangan_id' => ArrayHelper::getValue($params, 'ruangan_id'),
                            'data-penjamin_id' => ArrayHelper::getValue($params, 'penjamin_id'),
                            'data-kelaspelayanan_id' => ArrayHelper::getValue($params, 'kelaspelayanan_id'),
                            'data-instalasi_id' => ArrayHelper::getValue($params, 'instalasi_id'),
                            'prompt' => "Pilih Jenis Pemeriksaan"
                        ]) ?>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" name="daftartindakan_nama" 
                        id="daftartindakan_nama"
                        class="form-control input-xs search-radlab"
                        data-ruangan_id="<?= ArrayHelper::getValue($params, 'ruangan_id') ?>"
                        data-penjamin_id="<?= ArrayHelper::getValue($params, 'penjamin_id') ?>"
                        data-kelaspelayanan_id="<?= ArrayHelper::getValue($params, 'kelaspelayanan_id') ?>"
                        data-instalasi_id="<?= ArrayHelper::getValue($params, 'instalasi_id') ?>"
                        placeholder="Pencarian Nama Pemeriksaan"
                        autocomplete="new-password"
                        >
                    </div>
                    <div class="col-sm-3">
                        <button type="button" class="btn btn-info btn-labeled btn-xs" id="btn-search_radlab"><b><i class="fa fa-search"></i></b>Cari</button>
                        <button type="button" class="btn btn-info btn-labeled btn-xs" id="btn-reset"><b><i class="fa fa-refresh"></i></b>Muat Ulang</button>
                    </div>
                </div>
            </div>
            <br>
            <div id="loading-content"></div>
            <div class="row content-radlab">
                <div class="col-sm-12">
                    <?php if($result) : ?>
                        <?php foreach($result as $header => $detail_header) : $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $header))); ?>
                            <div class="col-sm-4">
                                <div class="panel panel-default">
                                    <a id="heading-<?= $slug ?>" data-toggle="collapse" href="#tab-<?= $slug ?>" role="button" aria-expanded="true" aria-controls="tab-<?= $slug ?>" class="">
                                        <div class="panel-heading flex-container" style="background-color:#37474f;color:white;">
                                            <h6 class="panel-title text-bold" style="font-size:12px;"><?= strtoupper($header) ?></h6>
                                            <ul class="icons-list">
                                                <li><i id="chevron" class="fa fa-chevron-up"></i></li>
                                            </ul>
                                        </div>
                                    </a>
                                    <div class="panel-body multi-collpase label-information collapse out" id="tab-<?= $slug ?>" aria-expanded="true">
                                        <div class="row">
                                            <?php if(!empty($detail_header)) : ?>
                                            <?php foreach ($detail_header as $key => $detail2) : 
                                                $selected = false;
                                                $daftarTindakanId = ArrayHelper::getValue($detail2, 'daftartindakan_id');
                                                $daftarTindakanNama = ArrayHelper::getValue($detail2, 'daftartindakan_nama');
                                            ?>
                                                <p style="margin-left:10px;margin-top:10px;">
                                                <?= Html::checkbox('checkPemeriksaan', $selected, [
                                                    'id' => $daftarTindakanId,
                                                    'class' => 'cb_penunjang',
                                                    'data-tariftindakan_id' => ArrayHelper::getValue($detail2, 'tariftindakan_id'),
                                                    'data-daftartindakan_id' => $daftarTindakanId,
                                                    'data-daftartindakan_nama' => $daftarTindakanNama,
                                                    'data-harga_tariftindakan' => ArrayHelper::getValue($detail2, 'harga_tariftindakan'),
                                                    'data-persencyto_tindakan' => ArrayHelper::getValue($detail2, 'persencyto_tindakan'),
                                                    'data-jenispemeriksaanlab_id' => ArrayHelper::getValue($detail2, 'jenispemeriksaanlab_id'),
                                                    'data-jenispemeriksaanlab_nama' => ArrayHelper::getValue($detail2, 'jenispemeriksaanlab_nama'),
                                                    'data-pemeriksaanlab_id' => ArrayHelper::getValue($detail2, 'pemeriksaanlab_id'),
                                                    'data-pemeriksaanlab_nama' => ArrayHelper::getValue($detail2, 'pemeriksaanlab_nama'),
                                                    'data-persen_penyulit' => ArrayHelper::getValue($detail2, 'persen_penyulit'),
                                                    'label' => ArrayHelper::getValue($detail2, 'kode').' - '.$daftarTindakanNama,
                                                    'value' => $daftarTindakanId,
                                                ]) ?></p>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs('
var _module = "'.$module.'";
var _endPoint = "'.$endPoint.'";
var _listTindakan = '.json_encode($dataTindakan).';
var pasienkirimkeunitlain_id = "'.$pasienkirimkeunitlain_id.'";

', View::POS_END, 'index');
$this->registerJs($this->render('__modal_order_tindakan.js'), View::POS_END, 'jsModal')

?>
