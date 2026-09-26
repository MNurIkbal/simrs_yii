<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-16 09:43:38
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-03 11:38:13
 * @Description: 
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
?>

<style type="text/css">
    .modal-body{
        max-height: calc(100vh - 1px);
        max-width: calc(200vh - 1px);
        /*overflow-y: auto;
        overflow-x: auto;*/
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><b><?=$title;?></b> - <?=$action?></h5>
</div>
<div class="modal-body" style="z-index:999">
    <div class="row">
        <h6><?=Yii::t('fe', 'No reseptur');?> : <?=@$no_reseptur;?></h6>
        <?=Html::hiddenInput('reseptur_id', $reseptur_id, ['id' => 'detail-riwayat-reseptur_id']);?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table width="100%" id="tabel-detail-riwayat-reseptur" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'No')?></th>
                        <th><?=Yii::t('fe', 'Racikan / non racikan')?></th>
                        <th><?=Yii::t('fe', 'R ke-')?></th>
                        <th><?=Yii::t('fe', 'Nama obat')?></th>
                        <th><?=Yii::t('fe', 'Satuan')?></th>
                        <th><?=Yii::t('fe', 'Signa')?></th>
                        <th><?=Yii::t('fe', 'Qty')?></th>
                        <th><?=Yii::t('fe', 'Harga satuan')?></th>
                        <th><?=Yii::t('fe', 'Jumlah harga')?></th>
                        <th><?=Yii::t('fe', 'Catatan')?></th>
                        <?php if ($type != 1) : ?>
                        <th><?=Yii::t('fe', 'Aksi')?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="list-history">
                    <tr class="default-value text-center">
                        <td colspan="9">Memuat data..</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- tabel list resep end -->
</div>
<div class="modal-footer">
    <?php if ($type == 2){ ?>
        <?= Html::button('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan'), [
            'class' => 'btn bg-teal', 
            'id' => 'saveRiwayatReseptur',
            'action' => "/rajal/pemeriksaan/save-detail-riwayat-reseptur"
        ]); ?>
    <?php } ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]); ?>
</div>

<?php 
$this->registerJs('
    var tabel_riwayat_detail_reseptur = $("#tabel-detail-riwayat-reseptur");
    $(document).ready(function(){
        getDetailListObat('.$reseptur_id.', '.$type.', true);
        $("#saveRiwayatReseptur").on("click", function (e) {
            let data = new FormData();
            let signa = $(".signa_edit").get();
            let qty = $(".qty_add").get();
            for (let i = 0; i < signa.length; i++) {
                data.append(signa[i].name, signa[i].value);
            }
            for (let i = 0; i < qty.length; i++) {
                data.append(qty[i].name, qty[i].value);
            }
            let data_resepturid = $("#detail-riwayat-reseptur_id");
            let general = data_resepturid.serializeArray();
            $.each(general, function (key, value) {
                data.append(value.name, value.value);
            });

            $(this).docoForm("click", {
                data: data,
                dataType: false, // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                method: "post",
                isUpload: true,
                success : function(data) {
                    getDetailListObat('.$reseptur_id.', '.$type.', true);
                }
            });
        });

        $(document).on("keyup", ".qty_add", function () {
            let id = $(this).attr("data-id");
            let harga = $(this).attr("data-harga");
            let qty = $(this).val();
            if (parseInt(qty) == 0) {
                qty = 1;
                $(this).val(qty);
            }
            $(this).val(qty);
            let total_resep = parseInt(qty) * parseInt(harga);
            if (isNaN(total_resep)) {
                total_resep = 1;
            }
            $("#total-resep" + id).html("Rp. " + docoHelper.convertToRupiah(total_resep));
        });
    })
', View::POS_END);
?>
