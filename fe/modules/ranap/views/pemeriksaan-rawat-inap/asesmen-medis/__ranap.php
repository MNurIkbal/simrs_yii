<?php
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\helpers\Url;
$this->title = \Yii::t('fe', $title);
?>
<div class="row">
    <div class="col-md-6">
        <label class="control-label col-sm-3">Form Asesmen</label>
        <?= Html::dropDownList('formasesmen_id', null, $lookup, [
            'class' => 'form-control formasesmen_id',
            'prompt' => 'Pilih Form Asesmen'
        ]) ?>
    </div>
</div>
<div class="row">
    <br>
    <div class="section-table-asesmen">
        <?php if(!empty($dataAsmed)) : ?>
        <table id="table-asmed-spesialis" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="1">No</th>
                    <th>Nama Dokumen</th>
                    <th>Tanggal</th>
                    <th>User Input</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($dataAsmed as $value) :
                    $tglPeriksa = ArrayHelper::getValue($value, 'tanggal');
                    $asesmenMedisId = ArrayHelper::getValue($value, 'asesmenmedis_id');
                    $reportCode = ArrayHelper::getValue($value, 'lookup_kode');
                    $formAsesmenId = ArrayHelper::getValue($value, 'formasesmen_id');
                    $isDokumenEklaim = ArrayHelper::getValue($value, 'is_dokumen_eklaim', false);
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= ArrayHelper::getValue($value, 'nama_dokumen') ?></td>
                    <td><?= $tglPeriksa ? date('d/M/Y H:i:s', strtotime($tglPeriksa)) : '-' ?></td>
                    <td><?= ArrayHelper::getValue($value, 'user_input') ?></td>
                    <td><button type="button" class="btn btn-info btn-labeled btn-xs btn-edit"
                        data-id="<?= $asesmenMedisId ?>" data-form-id="<?= $formAsesmenId ?>"
                        data-form-kode="<?= $reportCode ?>"
                        >
                        <b><i class="fa fa-pencil"></i></b>Edit</button>
                        <?php if(!empty($reportCode)) : ?>
                        <button type="button" class="btn btn-info btn-labeled btn-xs btn-lihat-dokumen"
                        data-target="/reports/viewer/<?= $reportCode ?>?asesmenmedis_id=<?= $asesmenMedisId ?>"
                        >
                        <b><i class="fa fa-print"></i></b>Lihat Dokumen</button>
                        <?php endif; ?>

                        <?php if($isDokumenEklaim) : ?>
                            <span class="badge badge-success">Dokumen Eklaim</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<button type="button" id="btn-show-asmed"
class="btn btn-xs hidden btn-only btn-info btn-labeled btn-xs data-print btn-toolbar"
data-width="90%" data-toggle="modal" data-target='#modal_backdrop'>
</button>

<?php
$this->registerJs("
    var isDokumenEklaim = '".$isDokumenEklaim."';
    var pendaftaranId = ".$pendaftaranId.";
    var pasienAdmisiId = ".$pasienAdmisiId."
    var pasienId = ".$pasienId."
", View::POS_END, 'js2');

$this->registerJs('
    $(document).ready(function(){
        table = $("#table-asmed-spesialis").DataTable({
            responsive: true,
            filter: false,
            order: [[1, "asc"]],
            scrollY: "300px",
            scrollCollapse: true,
            paging: false,
            columnDefs: [{ targets: 0, orderable: false }],
        });
        $(".formasesmen_id").select2();
        $(".formasesmen_id").on("change", function(){
            let _formAsesmenId = $(this).val()
            if(_formAsesmenId) {
                var _url = `/ranap/pemeriksaan-rawat-inap/modal-asmed?id=${pendaftaranId}&pasienadmisi_id=${pasienAdmisiId}&pasien_id=${pasienId}&formasesmen_id=${_formAsesmenId}`
                $("#btn-show-asmed").attr("action", _url)
                $("#btn-show-asmed").unbind("click");
                $("#btn-show-asmed").trigger("click")
                $("#is_dokumen_eklaim").prop("checked", false).trigger("change")
            }
        })
        
        $(".btn-lihat-dokumen").on("click", function(e){
            e.preventDefault()
            let url = $(this).attr("data-target");
            window.open(url, "_blank");
        })
        
        $(".btn-edit").on("click", function(e){
            e.preventDefault()
            let asesmenId = $(this).attr("data-id");
            let asesmenMedisId = $(this).attr("data-id");
            let _formAsesmenId = $(this).attr("data-form-kode");
            var _urlEdit = `/ranap/pemeriksaan-rawat-inap/modal-asmed?id=${pendaftaranId}&pasienadmisi_id=${pasienAdmisiId}&pasien_id=${pasienId}&formasesmen_id=${_formAsesmenId}&asesmenmedis_id=${asesmenMedisId}`
            $("#btn-show-asmed").attr("action", _urlEdit)
            $("#btn-show-asmed").unbind("click");
            $("#btn-show-asmed").trigger("click")
        })
    })
', View::POS_END)
?>
