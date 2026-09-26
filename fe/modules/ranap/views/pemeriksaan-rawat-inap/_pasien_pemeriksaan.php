<?php



use yii\web\View;
use yii\helpers\Html;
use app\components\DocoConstants;
use app\components\DocoHelpers;

$cpptTab = "";
$asesmenAwalClass = "active";
$asesmenMedisClass = null;
$classRekons = "hidden";
$classResumeMedis = "hidden";
$partografClass = "";
if ($jeniskasuspenyakit == DocoConstants::VAR_PENYAKIT_PERSALINAN) {
    $partografClass = "active";
    $hiddenOther = $classRekons = $classResumeMedis = "hidden";
    $asesmenAwalClass = $asesmenMedisClass = "hidden";
} else {
    $partografClass = "";
    $cpptTab = "active";
}
if ($obat_darirumah) {
    $classRekons = " ";
}
if ($resumeTab == true) {
    $partografClass = "hidden";
    $resumeTab = "active";
    $cpptTab = "";
}
//Matikan Komentar Untuk Show Resume Medis Lama
// if ($resume_medis == true){
//     $classResumeMedis = " ";
//}
?>

<style type="text/css">
    .nav-sidebar {
        border-right: 1px solid #ddd;
        height: 100%;
        position: sticky;
        top: 0;
        padding-right: 10px;
        max-height: calc(100vh - 20px);
        overflow-y: auto;
    }

    .nav-sidebar li a {
        padding: 10px 15px;
        display: block;
    }

    .nav-sidebar li.active>a {
        background: #428bca;
        color: white;
    }
</style>

<div class='tabbable' style="position:relative;">
    <!--Top Bar-->
    <div class="row">
        <div class="col-sm-2">
            <ul id="tab-ranap" class="nav nav-pills nav-stacked nav-sidebar nav-tabs nav-tab-periksa">
                <?php /* <li class="<?=$asesmenAwalClass?>" id="tab-asesmenawal"> */ ?>
                <li id="tab-asesmenawal">
                    <a href="#view-asesmenawal" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Asesmen Perawat') ?>
                    </a>
                </li>
                <?php /* <li class="<?=$asesmenMedisClass?> hidden" id="tab-asesmenmedis"> */ ?>
                <li id="tab-asesmenmedis">
                    <a href="#view-asesmenmedis" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Asesmen Dokter') ?>
                    </a>
                </li>
                <?php if($skor >= 2) : ?>
                    <li id="tab-pagt">
                        <a href="#view-pagt" data-toggle="tab" aria-expanded="true">
                            <?=Yii::t('fe', 'PAGT')?>
                        </a>
                    </li>
                <?php endif; ?>
                <li id="tab-nursingnote">
                    <a href="#view-nursingnote" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Catatan Dokter')?>
                    </a>
                </li>
                <li class="<?= $cpptTab ?>" id="tab-cppt">
                    <a href="#view-cppt" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'CPPT') ?>
                    </a>
                </li>
                <li class="" id="tab-implementasi">
                    <a href="#view-implementasi" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Instruksi & Implementasi') ?>
                    </a>
                </li>
                <li class="" id="tab-pemberian-infus">
                    <a href="#view-pemberian-infus" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Pemberian Infus') ?>
                    </a>
                </li>
                <li class="hidden" id="tab-permintaan-konsul">
                    <a href="#view-permintaan-konsul" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Permintaan konsul') ?>
                    </a>
                </li>
                <li class="" id="tab-pemberian-obat">
                    <a href="#view-pemberian-obat" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Catatan Pemberian Obat') ?>
                    </a>
                </li>
                <!-- <li id="tab-permintaan-makan" <?php /* echo isset($tabs) && isset($tabs['permintaanmakan']) ? 'class="hidden"' : '' */ ?>>
                        <a href="#view-permintaan-makan" data-toggle="tab" aria-expanded="true">
                            <?php /* echo Yii::t('fe', 'Permintaan Makan') */ ?>
                        </a>
                    </li> -->
                <li id="tab-rekonsobat">
                    <a href="#view-rekonsobat" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Rekonsiliasi Obat') ?>
                    </a>
                </li>
                <li class="<?= $partografClass ?>" id="tab-partograf">
                    <a href="#view-partograf" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Partograf') ?>
                    </a>
                </li>
                <?php /* <li class="<?= $discharge_plan || $rencana_pulang ? 'active' : '' ?>" id="tab-dischargeplan"> */ ?>
                <li id="tab-dischargeplan">
                    <a href="#view-dischargeplan" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Discharge Planning') ?>
                    </a>
                </li>
                <li class="<?= $classResumeMedis ?>" id="tab-resumemedis">
                    <a href="#view-resumemedis" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Resume') ?>
                    </a>
                </li>
                <li class="<?= $resumeTab ?>" id="tab-resumemedisri">
                    <a href="#view-resumemedisri" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Resume Medis') ?>
                    </a>
                </li>
                <li id="tab-cathlab-koroangiografi" <?= isset($tabs) && isset($tabs['cathlab']) ? 'class="hidden"' : '' ?>>
                    <a href="#view-cathlab" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Cathlab Koroangiografi') ?>
                    </a>
                </li>
                <li id="tab-cathlab-pci" <?= isset($tabs) && isset($tabs['cathlab']) ? 'class="hidden"' : '' ?>>
                    <a href="#view-cathlab" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Cathlab PCI') ?>
                    </a>
                </li>
                <li id="tab-cathlab-dsa" <?= isset($tabs) && isset($tabs['cathlab'])  ? 'class="hidden"' : '' ?>>
                    <a href="#view-cathlab" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Cathlab DSA') ?>
                    </a>
                </li>
                <li id="tab-upload-dokumen">
                    <a href="#view-upload-dokumen" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe','Upload Dokumen')?>
                    </a>
                </li>
                <li id="tab-surat-keterangan">
                    <a href="#view-surat-keterangan" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe','Surat Keterangan')?>
                    </a>
                </li>
                <li id="tab-asmed-ranap">
                    <a href="#view-asmed-ranap" data-toggle="tab" aria-expanded="true">
                        <?=Yii::t('fe','Assesmen Tambahan')?>
                    </a>
                </li>
                <?php if(empty($showTtvTab)) : ?>
                    <li id="tab-monitoring-ttv" <?= $showTtvTab ? 'class="hidden"' : '' ?>>
                        <a href="#view-monitoring-ttv" data-toggle="tab" aria-expanded="true">
                            <?=Yii::t('fe','Monitoring TTV')?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(empty($showEwsTab)) : ?>
                    <li id="tab-monitoring-ews" <?= $showEwsTab ? 'class="hidden"' : '' ?>>
                        <a href="#view-monitoring-ews" data-toggle="tab" aria-expanded="true">
                            <?=Yii::t('fe','Observasi EWS')?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(empty($showSbarTab)) : ?>
                    <li id="tab-sbar" <?= $showSbarTab ? 'class="hidden"' : '' ?>>
                        <a href="#view-sbar" data-toggle="tab" aria-expanded="true">
                            <?=Yii::t('fe','SBAR')?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <!--end of topbar-->
        <div class="col-sm-10">
            <div class="tab-content">
                <?php /* <div class="tab-pane has-padding <?=$partografClass?>" id="view-partograf"> */ ?>
                <div class="tab-pane has-padding active" id="view-partograf">
                    <div id="content-partograf"></div>
                </div>
                <?php /* <div class="tab-pane has-padding <?=$asesmenAwalClass?>" id="view-asesmenawal">*/ ?>
                <div class="tab-pane has-padding" id="view-asesmenawal">
                    <div id="content-asesmenawal"> </div>
                </div>
                <div class="tab-pane has-padding" id="view-asesmenmedis">
                    <div id="content-asesmenmedis"> </div>
                </div>
                <?php if($skor >= 2) : ?>
                    <div class="tab-pane has-padding" id="view-pagt">
                        <div id="content-pagt">  </div>
                    </div>
                <?php endif; ?>
                <div class="tab-pane has-padding" id="view-rekonsobat">
                    <div id="content-rekonsobat"></div>
                </div>
                <div class="tab-pane has-padding" id="view-dischargeplan">
                    <div id="content-dischargeplan"></div>
                </div>
                <div class="tab-pane has-padding" id="view-nursingnote">
                    <div id="content-nursingnote"></div>
                </div>
                <div class="tab-pane has-padding <?= $cpptTab ?>" id="view-cppt">
                    <div id="content-cppt"></div>
                </div>
                <div class="tab-pane has-padding" id="view-pemberian-infus">
                    <div id="content-pemberian-infus"></div>
                </div>
                <div class="tab-pane has-padding" id="view-permintaan-konsul">
                    <div id="content-permintaan-konsul"></div>
                </div>
                <div class="tab-pane has-padding" id="view-pemberian-obat">
                    <div id="content-pemberian-obat"></div>
                </div>
                <!-- <div class="tab-pane has-padding" id="view-permintaan-makan">
                <div id="content-permintaan-makan"></div>
                </div> -->
                <div class="tab-pane has-padding" id="view-implementasi">
                    <div id="content-implementasi"></div>
                </div>
                <div class="tab-pane has-padding" id="view-resumemedis">
                    <div id="content-resumemedis"></div>
                </div>
                <div class="tab-pane has-padding <?= $resumeTab ?>" id="view-resumemedisri">
                    <div id="content-resumemedisri"></div>
                </div>
                <div class="tab-pane" id="view-cathlab">
                    <div id="content-cathlab"></div>
                </div>
                <div class="tab-pane has-padding" id="view-upload-dokumen">
                    <div id="content-upload-dokumen"></div>
                </div>
                <div class="tab-pane" id="view-surat-keterangan">
                    <div id="content-surat-keterangan"></div>
                </div>
                <div class="tab-pane" id="view-asmed-ranap">
                    <div id="content-asmed-ranap"></div>
                </div>
                <div class="tab-pane" id="view-monitoring-ttv">
                    <div id="content-monitoring-ttv"></div>
                </div>
                <div class="tab-pane" id="view-monitoring-ews">
                    <div id="content-monitoring-ews"></div>
                </div>
                <div class="tab-pane" id="view-sbar">
                    <div id="content-sbar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var count = 0;

    $(document).on("click",".btn-append", function(e){
        e.preventDefault();
        count++;

        var _clone = $(".clone-div").clone()
            .addClass("row-data row-"+count)
            .removeClass("clone-div hidden")

        _clone.find("input, select").each(function(k,v){
            if($(this).hasClass("select-ppa") ){
                $(this).addClass("select-ppa"+count)
                setTimeout(function(){ $(".select-ppa"+count).select2(); }, 1)
            }
            // $(this).attr("name", $(this).attr("data-name"))
            $(this).attr("name", "RencanaPulangDetailForm["+$(this).attr("data-name")+"]["+count+"]")
        });
        _clone.find(".btn-remove").attr("data-key", "row-"+count)
        $(".isi-loop").append(_clone);
        $(".date").pickadate({
            format: "dd mmmm yyyy",
            disable: [{
                from: [0, 0, 0],
                to: yesterday
            }],
        });
        return false;
    });
', View::POS_END, 'b-index');
?>
