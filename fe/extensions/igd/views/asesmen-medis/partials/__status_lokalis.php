<?php
use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
?>


<!-- Status Lokalis -->
<div class="row">
      <div class="panel panel-default">
          <div class="panel-heading">
		                            <h5 class="panel-title"><?= Yii::t('fe', 'Status Lokalis') ?></h5>
		                        </div>
          <div class="panel-body">
              <div class="col-md-5 col-sm-12">
                  <div class="image-frame">
                  <?php
											echo Html::img('@web/media/img/img-pemeriksaan/bagian_tubuh_medis.jpg', [
												'width'=> 500,
												'height'=> 520,
                        'usemap' => '#image-map',
											]);
                  ?>

                      <map name="image-map">
                          <area target="_self" data-value="mata" data-id="7" alt="Mata Depan Kiri" title="Mata Depan Kiri" href="" coords="117,32,129,29,129,39,117,43" shape="poly">
                          <area target="_self" data-value="mata" data-id="7" alt="Mata Depan Kanan" title="Mata Depan Kanan" href="" coords="96,28,108,31,106,43,98,40" shape="poly">
                          <area target="_self" data-value="mulut & gigi" data-id="8" alt="Mulut" title="Mulut" href="" coords="101,51,124,51,119,58,108,58" shape="poly">
                          <area target="_self" data-value="hidung" data-id="2" alt="Hidung Depan" title="Hidung Depan" href="" coords="113,32,106,49,119,49" shape="poly">
                          <area target="_self" data-value="kepala" data-id="5" alt="Kepala Depan" title="Kepala Depan" href="" coords="113,3,134,10,137,23,136,30,139,36,139,48,132,58,116,68,100,61,92,50,91,37,91,26,94,11" shape="poly">
                          <area target="_self" data-value="leher" data-id="6" alt="Leher Depan" title="Leher Depan" href="" coords="98,58,98,72,88,77,110,80,119,80,136,77,131,74,129,60,115,68" shape="poly">
                          <area target="_self" data-value="dada" data-id="1" alt="Dada Depan" title="Dada Depan" href="" coords="69,84,70,146,113,147,156,147,160,86,136,77,120,80,103,80,89,78" shape="poly">
                          <area target="_self" data-value="kelamin" data-id="4" alt="Kelamin" title="Kelamin" href="" coords="126,275,125,242,103,242,104,275" shape="poly">
                          <area target="_self" data-value="perut" data-id="9" alt="Perut" title="Perut" href="" coords="156,149,151,164,151,178,151,186,153,197,158,216,144,219,131,249,96,249,81,220,70,218,74,201,77,187,79,173,76,161,72,149" shape="poly">
                          <area target="_self" data-value="tangan" data-id="10" alt="Tangan Depan Kanan" title="Tangan Depan Kanan" href="" coords="67,84,50,95,45,108,43,121,45,134,45,145,41,160,40,170,34,182,33,200,33,224,27,243,2,262,10,264,17,257,10,285,15,293,26,291,34,283,40,259,43,240,52,216,62,187,60,171,69,145" shape="poly">
                          <area target="_self" data-value="tangan" data-id="10"alt="Tangan Depan Kiri" title="Tangan Depan Kiri" href="" coords="160,84,170,89,182,101,186,119,184,136,186,160,187,172,194,184,196,205,198,219,198,229,199,240,211,246,223,258,222,267,211,258,218,284,211,295,194,284,187,265,184,241,177,217,167,191,167,169,158,147" shape="poly">
                          <area target="_self" data-value="kaki" data-id="3" alt="Kaki Depan Kanan" title="Kaki Depan Kanan" href="" coords="70,217,81,218,102,245,112,274,110,298,106,326,101,350,98,386,93,418,88,448,88,483,82,492,81,502,77,514,65,516,52,507,60,492,69,478,69,440,67,420,65,402,65,382,67,364,70,345,67,309,65,278,67,247" shape="poly">
                          <area target="_self" data-value="kaki" data-id="3" alt="Kaki Depan Kiri" title="Kaki Depan Kiri" href="" coords="158,216,146,220,127,246,115,275,119,302,120,323,124,340,127,352,131,370,131,387,131,397,134,411,136,424,137,438,141,450,141,460,141,485,146,494,146,504,151,515,158,518,168,515,177,504,160,479,158,449,161,422,163,384,161,368,156,350,158,323,163,289,163,268" shape="poly">
                          <area target="_self" data-value="kepala" data-id="5" alt="Kepala Belakang" title="Kepala Belakang" href="" coords="360,33,32" shape="circle">
                          <area target="_self" data-value="leher" data-id="6" alt="Leher Belakang" title="Leher Belakang" href="" coords="343,59,340,74,358,77,377,73,375,61,364,65,352,65" shape="poly">
                          <area target="_self" data-value="dada" data-id="1" alt="Dada Belakang" title="Dada Belakang" href="" coords="311,86,315,125,316,146,323,164,344,167,359,167,382,165,397,161,402,145,403,124,405,86,377,73,358,78,340,74" shape="poly">
                          <area target="_self" data-value="perut" data-id="9" alt="Perut Belakang " title="Perut Belakang" href="" coords="322,163,323,179,317,210,342,215,360,216,376,214,401,208,395,178,396,160,378,166,359,168,343,168" shape="poly">
                          <area target="_self" data-value="tangan" data-id="10" alt="Tangan Belakang Kanan" title="Tangan Belakang Kanan" href="" coords="404,85,421,93,429,108,430,126,428,141,431,162,435,174,439,188,442,207,442,224,445,241,458,248,463,255,470,261,466,265,456,258,463,279,462,288,456,293,447,292,438,284,433,254,426,229,412,186,412,173,402,145" shape="poly">
                          <area target="_self" data-value="tangan" data-id="10" alt="Tangan Belakang Kiri" title="Tangan Belakang Kiri" href="" coords="310,86,295,94,287,110,291,138,287,169,279,184,276,206,277,226,271,241,248,259,250,264,263,258,256,281,259,288,263,293,271,292,279,284,286,259,286,247,291,234,306,189,306,173,316,145" shape="poly">
                          <area target="_self" data-value="kelamin" data-id="4" alt="Kelamin Belakang" title="Kelamin Belakang" href="" coords="316,209,312,231,311,255,358,273,407,259,407,232,401,208,379,213,356,216,342,215" shape="poly">
                          <area target="_self" data-value="kaki" data-id="3" alt="Kaki Belakang Kiri" title="Kaki Belakang Kiri" href="" coords="310,254,310,269,308,297,311,321,317,349,315,365,316,373,314,396,313,411,320,440,323,484,310,499,295,506,279,513,277,518,282,520,300,519,315,518,335,520,343,511,341,485,345,435,353,410,348,378,351,370,351,350,354,325,358,273" shape="poly">
                          <area target="_self" data-value="kaki" data-id="3" alt="Kaki Belakang Kanan" title="Kaki Belakang Kanan" href="" coords="406,259,409,289,409,307,407,323,401,350,403,363,401,375,405,399,404,411,400,433,397,442,396,487,411,501,436,510,440,515,436,519,417,521,403,518,383,518,376,509,377,481,373,437,365,413,364,403,370,382,367,370,367,345,357,274" shape="poly">

                          <area target="" alt="Kelamin" title="Kelamin" href="" coords="" shape="poly">
<area target="_self" alt="Kaki Depan Kanan" title="Kaki Depan Kanan" href="" coords="" shape="poly">
<area target="_self" alt="Kaki Depan Kiri" title="Kaki Depan Kiri" href="" coords="" shape="poly">
<area target="_self" alt="Perut Depan" title="Perut Depan" href="" coords="" shape="poly">
                      </map>
                  </div>
                  <!-- modal anatomi start -->
                  <div class="tag" style="display: none" data-show="1">
                      <span style="box-sizing: border-box;position: absolute;border: 6px solid #b93d3d;border-color: transparent transparent #ff0000 #ff0000;transform-origin: 0 0;transform: rotate(135deg);box-shadow: -3px 3px 3px -3px rgba(0,0,0,0.3);margin-left: 18px;"></span>
                      <div class="well well-sm" style="min-height:130px;">
                          <div class="form-group">
                              <label class="col-lg-3">Bagian<sup style="color: red">*</sup></label>
                              <div class="col-lg-9">
                                  <?php
                                  echo Html::dropDownList('bagain_tubuh', null, ArrayHelper::map($data_bagiantubuh, 'bagiantubuh_id', 'namabagtubuh'),
                                                 array(
                                                  'class' => 'form-control bagian-tubuh',
                                                  'style' => 'padding : 9px 12px !important;',
                                                  'empty' => '-- Pilih --',
                                                 ));
                                                 ?>
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-lg-3">Detail Bagian<sup style="color: red">*</sup></label>
                              <div class="col-lg-9">
                                  <?php echo Html::dropDownList('bagain_tubuh', null, [],
                                                 array(
                                                  'class' => 'form-control bagian-tubuh-detail',
                                                  'style' => 'padding : 9px 12px !important;',
                                                  'empty' => '-- Pilih --',
                                                 )); ?>
                              </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-3">Catatan<sup style="color: red">*</sup></label>
                              <div class="col-lg-9">
                                  <input type="text"
                                         placeholder="catatan.."
                                         class="form-control add-caption">
                              </div>
                          </div>
                          <div class="form-group">
                              <div class="col-lg-12">
                                  <p class="helper-text ">(tekan <b>enter</b> untuk selesai)</p>
                              </div>
                          </div>
                      </div>
                  </div>
                    <!-- modal anatomi end -->
              </div>
              <div class="col-md-7 col-sm-12">
                    <div class="col-lg-6">
                        <h6 class="panel-title"><?=Yii::t('fe', 'Status Lokalis')?></h6>
                    </div>
                    <table class="table table-bordered table-hover no-footer table-framed tabel-anggotatubuh">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="10%">No</th>
                                <th width="10%"><?=Yii::t('fe', 'Tanggal periksa')?></th>
                                <th width="15%"><?=Yii::t('fe', 'Bagian tubuh')?></th>
                                <th width="15%"><?=Yii::t('fe', 'Bagian tubuh detail')?></th>
                                <th width="45%"><?=Yii::t('fe', 'Catatan')?></th>
                                <th width="5%"><?=Yii::t('fe', 'Aksi')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- table data -->
                        </tbody>
                    </table>
                </div>
          </div>
      </div>
  </div>
