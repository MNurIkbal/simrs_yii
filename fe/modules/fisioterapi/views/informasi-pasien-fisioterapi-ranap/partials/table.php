
<?php
    /**
     * @author Chacha Nurholis (chacha@sirs.co.id)
     * A Product of PT Citraraya Nusatama
     * Powered by Sirs
     */

    use app\components\DocoHelpers;
?>

<div class="panel panel-default">
    <div class="panel-toolbar clearfix">
        <?= 
            DocoHelpers::generateToolbar([
                'search',
                'reset' => [
                    'attributes' => [
                        'data-parent' => '.filter-form',
                    ]
                ],
                'periksa' => [
                    'title' => \Yii::t('fe', 'Periksa'),
                    'icon' => 'fa fa-stethoscope',
                    'attributes' => [
                        'data-target' => '/fisioterapi/pemeriksaan?pendaftaran_id=',
                        'id' => 'periksa',
                        'data-options' => 'click',
                        'disabled' => true
                    ]
                ],
                'detail' => [
                    'title' => Yii::t('fe', 'Lihat'),
                    'icon' => 'fa fa-eye',
                    'attributes' => [
                        'id' => 'lihat',
                        'data-options' => 'click',
                        'disabled' => true
                    ]
                ],
                // 'history' => [
                //     'title' => \Yii::t('fe', 'History'),
                //     'icon' => 'fa fa-eye',
                //     'attributes' => [
                //         'class' => $verifyButton == false ? 'hidden' : '',
                //         'data-target' => '/fisioterapi/histrory-fisioterapi?pendaftaran_id=',
                //         'id' => 'history',
                //         'data-options' => 'click',
                //         'disabled' => true
                //     ]
                // ],
            ], '#example')
        ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="advanced-filter"></div>
        </div>
        <table id="example" class="table table-striped table-condensed table-hover" style="width: 100%;">
            <thead>
                <tr class="bg-inverse">
                    <th></th>
                    <th></th>
                    <th ><?= Yii::t("fe", "Tanggal Rujukan") ?></th> 
                    <th id="data-pasien" width="20%" ><?= Yii::t("fe", "Data Pasien") ?></th> 
                    <th><?= Yii::t("fe", "Status Periksa") ?></th> 
                    <th><?= Yii::t("fe", "Jenis Terapi") ?></th> 
                    <th><?= Yii::t("fe", "Terapi") ?></th> 
                    <th width="20%"><?= Yii::t("fe", "Nama Ruangan No.Kamar-No.Bed") ?></th> 
                    <th><?= Yii::t("fe", "Dokter Penanggung Jawab") ?></th> 
                    <th><?= Yii::t("fe", "Cara Bayar / Penjamin") ?></th>
                    <th id="status-bayar"><?= Yii::t("fe", "Status Bayar") ?></th> 
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="12"><?= Yii::t("fe", "Data tidak ditemukan.") ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>