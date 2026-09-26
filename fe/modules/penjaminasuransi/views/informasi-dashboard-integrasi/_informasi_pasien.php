<?php 
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use app\components\DocoHelpers;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading">
                <h6 class="panel-title"><?= Yii::t('fe', 'Identitas Pasien') ?></h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <table class="table-custom" style="border: none;width:100%;">
                        <tr>
                            <td>No Kartu </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataPeserta, 'no_kartu') ?></td>
                            <td>Nama Peserta </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataPeserta, 'additionalData.namapeserta') ?></td>
                            <td>Nama Perusahaan </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataPeserta, 'additionalData.namaperusahaan') ?></td>
                        </tr>
                        <tr>
                            <td>Member ID </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataPeserta, 'additionalData.namapeserta') ?></td>
                            <td>Tanggal Lahir </td>
                            <td> : </td>
                            <td><?= date('d-m-Y', strtotime(ArrayHelper::getValue($dataPeserta, 'additionalData.tanggallahir'))) ?></td>
                            <td>Member VIP </td>
                            <td> : </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>No BPJS </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataPeserta, 'no_sep') ?></td>
                            <td>Jenis Kelamin </td>
                            <td> : </td>
                            <td></td>
                            <td>No Polis </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataPeserta, 'no_polis') ?></td>
                        </tr>
                        <tr>
                            <td>Benefit Pasien </td>
                            <td> : </td>
                            <td style="font-weight:bold;"><?= ArrayHelper::getValue($dataPeserta, 'additionalData.namabenefit') ?></td>
                            <td>Sisa Limit </td>
                            <td> : </td>
                            <td style="font-weight:bold;"><?= DocoHelpers::formatNumber(ArrayHelper::getValue($sisaLimit, 'sisalimit')) ?></td>
                            <td>Diagnosa Utama </td>
                            <td> : </td>
                            <td style="font-weight:bold;">
                                <div style="max-width: 300px; margin-bottom: 10px">
                                    <?=
                                        Html::dropDownList('diagnosa', 'b81.2', [],
                                            [
                                                'id' => 'diagnosa',
                                                'class' => 'form-control select2',
                                                'prompt' => \Yii::t('fe', 'All'),
                                            ]
                                        )
                                    ?>
                                </div>
                                <div >
                                    <span class="text-danger"> <?= $diagnosaText ?></span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
