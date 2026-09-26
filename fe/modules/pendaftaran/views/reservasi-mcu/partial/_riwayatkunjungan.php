<div class="panel panel-white">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe','Riwayat Kunjungan Pasien')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <table id="tbl-kunjungan" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th><?=\Yii::t("fe", "Tanggal pendaftaran");?></th>
                    <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                    <th><?=\Yii::t("fe", "Instalasi");?></th>
                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                    <th><?=\Yii::t("fe", "Dokter");?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>