
<?php
    /**
     * @author Andri Amirul Sonjaya (andri.amirul@sirs.co.id)
     * A Product of PT Citraraya Nusatama
     * Powered by Sirs
     */

    use app\components\DocoHelpers;
    use yii\helpers\Url;
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
                'export-pdf-bgprocess' => [
                    'type' => 'button',
                    'title' => 'Cetak PDF',
                    'icon' => 'fa fa-print',
                    'attributes' => [
                        'id' => 'btn-export-pdf-bgprocess',
                        'data-options' => 'excel-serconn',
                        'data-target' => '#modal_backdrop',
                        'data-url' => Url::home() . 'fisioterapi/laporan-pemeriksaan-fisioterapi-ranap/show-popup-pdf?',
                        'data-width' => '75%'
                    ]
                ],
                'export-excel-serconn' => [
                    'type' => 'button',
                    'title' => \Yii::t('fe', 'Excel'),
                    'icon' => 'fa fa-file-excel-o',
                    'attributes' => [
                        'id' => 'data-export-excel-serconn',
                        'data-options' => 'excel-serconn',
                        'data-target' => '#modal_backdrop',
                        'data-url' => Url::home() . 'fisioterapi/laporan-pemeriksaan-fisioterapi-ranap/show-popup-excel?',
                        'data-width' => '75%'
                    ]
                ],
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
                    <th ><?= Yii::t("fe", "Tanggal Pendaftaran") ?></th> 
                    <th id="data-pasien" width="15%" ><?= Yii::t("fe", "Data Pasien") ?></th> 
                    <th><?= Yii::t("fe", "Cara Bayar / Penjamin") ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><?= Yii::t("fe", "Instalasi / Ruangan") ?></th> 
                    <th></th>
                    <th></th>
                    <th><?= Yii::t("fe", "Dokter Terapis") ?></th> 
                    <th></th>
                    <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th> 
                    <th></th>
                    <th><?= Yii::t("fe", "Nama Pemeriksaan") ?></th> 
                    <th></th>
                    <th><?= Yii::t("fe", "Jumlah") ?></th> 
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="12"><?= Yii::t("fe", "Data tidak ditemukan.") ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>