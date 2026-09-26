<?php
use app\components\DocoConstants;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>
<style type="text/css">
.modal-body {
    height: calc(100vh - 100px);
    overflow-y: auto;
}
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body body-history-terra">
    <div class="panel-toolbar clearfix">
        <?=DocoHelpers::generateToolbar([
            'search' => ['attributes' => ['data-parent' => '.filter-forms']],
            'reset' => ['attributes' => ['data-parent' => '.filter-forms']]
        ], '.tb-terra-medik');?>
    </div>
    <form class="row filter-forms advancedFilter">
          <div class='col-md-3'>
              <label for="dokter_id">Dokter :</label>
              <?= Html::dropDownList('dokter_id', '',[],
                  [
                      'id' => 'filter_dokter',
                      'class' => 'form-control select2',
                      'style'=>'width:100%;',
                      'prompt' => \Yii::t('fe', '--Pilih Dokter--'),
                  ]
              )?>
          </div>
          <div class='col-md-3'>
              <label for="dokter_id">Tanggal :</label>
              <div class="form-group">
                  <div class='input-group'>
                    <input type='text' id='terraTanggalStart' class='form-control pickadate' value='' />
                    <span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span>
                    <input type='text' id='terraTanggalEnd' class='form-control pickadate' value=''/>
                    <input type='text' name="tanggal" id="targetDate" style='display:none' class='targetDate' readonly='true'>
                  </div>
              </div>
          </div>
    </form>

    <div class="tabbable" style="position: relative">
        <div class="nav-sticky-wrapper nav-sticky-cppt" id="nav-sticky">
            <div class="nav nav-tabs nav-tab-cppt" id="nav-tab" role="tablist">
                <a id="tab-soap" class="nav-item nav-tab-type nav-link active" data-toggle="tab" href="#view-soap" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat SOAP</a>
                <a id="tab-obat" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-obat" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat Obat</a>
                <a id="tab-lab" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-lab" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat Lab</a>
                <a id="tab-radiologi" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-radiologi" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat Radiologi</a>
                <a id="tab-bedah" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-bedah" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat Bedah</a>
                <a id="tab-mcu" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-mcu" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat MCU</a>
                <a id="tab-fisioterapi" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-fisioterapi" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat Fisioterapi</a>
                <a id="tab-bbl" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-bbl" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat BBL</a>
                <a id="tab-resumemedis" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-resumemedis" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat Resume Medis</a>
                <a id="tab-riwayatkunjungan" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-riwayatkunjungan" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Riwayat Kunjungan</a>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane active" id="view-soap">
                <div id="content-soap">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__soap', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-obat">
                <div id="content-obat">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__obat', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-lab">
                <div id="content-lab">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__lab', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-radiologi">
                <div id="content-radiologi">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__radiologi', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-bedah">
                <div id="content-bedah">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__bedah', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-mcu">
                <div id="content-mcu">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__mcu', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-fisioterapi">
                <div id="content-fisioterapi">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__fisioterapi', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-bbl">
                <div id="content-bbl">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__bbl', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-resumemedis">
                <div id="content-resumemedis">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__resumemedis', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
            <div class="tab-pane" id="view-riwayatkunjungan">
                <div id="content-riwayatkunjungan">
                    <?=Yii::$app->controller->renderPartial('//pelayanan/terra-medik/__riwayat_kunjungan', [
                        'pasien_terra' => $pasien_terra,
                        'url' => $url
                    ]);?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-left">

</div>
<?php
$this->registerJs($this->render('js/_terra_medik.js'), View::POS_END);
?>
