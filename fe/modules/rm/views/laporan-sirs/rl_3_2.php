<?php
    use yii\web\View;
    use app\components\DocoHelpers;
?>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Formulir RL 3.2'); ?></h3>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Kunjungan Rawat Darurat'); ?></h3>
<hr>
    <div class="form-group">
        <div class="col-lg-12">
            <table id="rl-kunjrd" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <td rowspan="2">No.</td>
                        <td rowspan="2"><?= strtoupper(Yii::t('fe', 'Jenis Pelayanan')); ?></td>
                        <td colspan='2'><?= strtoupper(Yii::t('fe', 'Total Pasien')); ?></td>
                        <td colspan='3'><?= strtoupper(Yii::t('fe', 'Tindak Lanjut Pelayanan')); ?></td>
                        <td rowspan='2'><?= strtoupper(Yii::t('fe', 'Mati Di IGD')); ?></td>
                        <td rowspan='2'><?= strtoupper(Yii::t('fe', 'DOA')); ?></td>
                    </tr>
                    <tr class="bg-inverse">
                        <td><?= strtoupper(Yii::t('fe', 'Rujukan')); ?></td>
                        <td><?= strtoupper(Yii::t('fe', 'Non Rujukan')); ?></td>
                        <td><?= strtoupper(Yii::t('fe', 'Dirawat')); ?></td>
                        <td><?= strtoupper(Yii::t('fe', 'Dirujuk')); ?></td>
                        <td><?= strtoupper(Yii::t('fe', 'Pulang')); ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; $total = []; ?>
                    <?php foreach($contents as $key=>$content): ?>
                        <tr>
                            <td width='1'><?= $no++; ?></td>
                            <td><?= $content['jeniskasuspenyakit_nama']; ?></td>
                            <td><?= $content['rujukan']; ?></td>
                            <td><?= $content['non_rujukan']; ?></td>
                            <td><?= $content['di_rujuk_rawat_inap']; ?></td>
                            <td><?= $content['dirujuk_ke_rs_lain']; ?></td>
                            <?php $pulang = $content['pulang_paksa'] + $content['dipulangkan'] + $content['melarikan_diri'] + $content['lain-lain']; ?>
                            <td>
                                <?= $pulang ? : 0; ?>
                            </td>
                            <?php $matiIgd = $content['meninggal_<_48_jam'] + $content['meninggal_>_48_jam']; ?>
                            <td><?= $matiIgd; ?></td>
                            <td><?= $content['death_on_arrival']; ?></td>

                            <?php
                            $total['rujukan'] = (isset($total) && isset($total['rujukan']) ? $total['rujukan'] : 0) + $content['rujukan'];
                            $total['non_rujukan'] = (isset($total) && isset($total['non_rujukan']) ? $total['non_rujukan'] : 0) + $content['non_rujukan'];
                            $total['dirawat'] = (isset($total) && isset($total['dirawat']) ? $total['dirawat'] : 0) + $content['di_rujuk_rawat_inap'];
                            $total['dirujuk'] = (isset($total) && isset($total['dirujuk']) ? $total['dirujuk'] : 0) + $content['dirujuk_ke_rs_lain'];
                            $total['pulang'] = (isset($total) && isset($total['pulang']) ? $total['pulang'] : 0) + $pulang;
                            $total['mati_igd'] = (isset($total) && isset($total['mati_igd']) ? $total['mati_igd'] : 0) + $matiIgd;
                            $total['death_on_arrival'] = (isset($total) && isset($total['death_on_arrival']) ? $total['death_on_arrival'] : 0) + $content['death_on_arrival'];
                            ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" align="center"><strong><?= Yii::t('fe', 'Total'); ?></strong></td>
                        <td><strong><?= isset($total['rujukan']) ?: 0; ?></strong></td>
                        <td><strong><?= isset($total['non_rujukan']) ?: 0; ?></strong></td>
                        <td><strong><?= isset($total['dirawat']) ?: 0; ?></strong></td>
                        <td><strong><?= isset($total['dirujuk']) ?: 0; ?></strong></td>
                        <td><strong><?= isset($total['pulang']) ?: 0; ?></strong></td>
                        <td><strong><?= isset($total['mati_igd']) ?: 0; ?></strong></td>
                        <td><strong><?= isset($total['death_on_arrival']) ?: 0; ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<script type="text/javascript">
var table;
$(document).ready(function() {
    table = $("#rl-kunjrd").DataTable({
        "language": {
            "search": "Pencarian&nbsp;:&nbsp;"
        },
        "columnDefs": [
            { "width": "40px", "targets": 0 },
        ],
        ordering : false,
        searching : false,
        paging: false,
    });
});
</script>
