<?php
// author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
?>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe', 'Permintaan Makan')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <br>
                <table id="menu-diet-data" class="table table-bordered table-hover table-responsive">
                    <thead>
                        <tr class="bg-inverse">
                            <th width=5>No</th>
                            <th width="230"><?=Yii::t('fe', 'Jenis Diet')?></th>
                            <th width=400><?=Yii::t('fe', 'Menu')?></th>
                            <th width=180><?=Yii::t('fe', 'Waktu Diet')?></th>
                            <th width=100><?=Yii::t('fe', 'Jumlah')?></th>
                            <th width="250"><?=Yii::t('fe', 'Keterangan')?></th>
                            <th width=10><?=Yii::t('fe', 'Aksi')?></th>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="select2-md" style="padding-bottom: 10px !important">
                                <?=Html::dropDownList('jenis_diet', null, $jenisDiet, ['class' => 'select2', 'prompt' => 'Pilih', 'id' => 'jenis-diet'])?>
                            </td>
                            <td class="select2-md" style="padding-bottom: 10px !important">
                                <div style="overflow: hidden;width:400px">
                                    <?=Html::dropDownList('menu', null, [], ['class' => 'select2', 'disabled' => true, 'id' => 'menu-diet'])?>
                                </div>
                            </td>
                            <td class="select2-md" style="padding-bottom: 10px !important">
                                <?=Html::dropDownList('waktu_diet', null, $waktuDiet, ['class' => 'select2', 'prompt' => 'Pilih', 'id' => 'waktu-diet'])?>
                            </td>
                            <td style="padding-bottom: 10px !important">
                                <?=Html::textInput('qty', null, ['class' => 'form-control', 'id' => 'qty-diet'])?>
                            </td>
                            <td style="padding-bottom: 10px !important">
                                <?=Html::textArea('keterangan', null, ['class' => 'form-control', 'rows' => 3, 'id' => 'keterangan-diet'])?>
                            </td>
                            <td>
                                <button class="btn btn-success btn-sm" id="btn-add-diet">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" style="padding: 10px !important" class="text-center">Belum Ada Permintaan Makan yang Ditambahkan</td>
                        </tr>
                    </tbody>
                </table>
                <br>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1 col-md-offset-11">
                <button class="btn btn-labeled btn-xs btn-success" id="btn-save-permintaanmakan">
                    <b><i class="fa fa-save"></i></b>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs($this->render('js/_form.js'), View::POS_END);
?>