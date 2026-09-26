<?php

use yii\helpers\Html;
?>

<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?= Yii::t('fe', 'Pilih antrian'); ?></h4>
</div>
<div class="modal-body">
    <div class='row'>
        <div class="col-lg-6">
            <div class="col-lg-6">
                <?= Html::hiddenInput('antrian_id', '', ['id'=>'hide_antrian_id']); ?>
                <?= Html::hiddenInput('limit_antrian', '', ['id'=>'hide_limit_antrian']); ?>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h2>No Antrian</h2>
                        <span id="no_antrian" style="font-size: 60px;">
                            <?php echo "-"; ?>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        Panggilan Ke : <span id="jumlah_panggil"> x </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        Sisa Antrian : <span id="queue"> x </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="btn-group-vertical">
                    <button id="btnNext" type="button" class="btn btn-lg btn-info">
                        <i class="fa fa-arrow-right"></i> No. Berikutnya (1)
                    </button>
                    <button id="btnPilih" type="button" class="btn btn-lg btn-success btnPilih">
                        <i class="fa fa-check"></i> Pilih (2)
                    </button>
                    <button id="btnPanggilUlang" type="button" class="btn btn-lg btn-primary btnPanggilUlang">
                        <i class="fa fa-volume-up"></i> Panggil Ulang (3)
                    </button>
                    <button id="btnLewati" type="button" class="btn btn-lg btn-warning"
                        data-confirm-message="<?= Yii::t('fe', 'Apakah anda yakin untuk lewati antrian?'); ?>">
                        <i class="fa fa-share"></i> Lewati (4)
                    </button>
                    <button id="btnBatal" type="button" class="btn btn-lg btn-danger btnBatal"
                    data-confirm-message="<?= Yii::t('fe', 'Apakah anda yakin untuk membatalkan data ini?'); ?>">
                        <i class="fa fa-times"></i> Batal (5)
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <fieldset>
                <legend>No Antrian yang di lewati</legend>
                    <table
                        class="table datatable-basic table-striped table-hover dataTable no-footer"
                        id="table-antrian-lewati"
                        style="width:100%;"
                    >
                        <thead>
                            <tr class="bg-inverse">
                                <th><?= Yii::t('fe', 'No antrian'); ?></th>
                                <th><?= Yii::t('fe', 'Aksi'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan') ?></td>
                            </tr>
                        </tbody>
                    </table>
            </fieldset>
        </div>
    </div>
</div>


<?php
$this->registerJs('
');
$this->registerJs($this->render('js/pilih-antrian.js'));
?>