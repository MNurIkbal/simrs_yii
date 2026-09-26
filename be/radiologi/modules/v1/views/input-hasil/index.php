<?php
    use Doco\components\DocoHelpers;
?>
<style media="print">
    .table-header {
        width: 100%;
        font-size: 10px;
    }
    .key-part {
        width: 15%;
        font-weight: bold;
    }
    .separator-part {
        width: 1%;
        text-align: center;
    }
    .value-part {
        width: 34%;
    }
    .body-title {
        text-align: center;
        font-weight: bold;
        text-decoration: underline;
    }
    .treatment-header {
        font-weight: bold;
    }
    .body-item__title {
        font-weight: bold;
    }
    .body-item {
        margin-bottom: 12px;
    }
    .body-signature {
        margin-top: 20px;
        width: 35%;
        float: right;
        text-align: center;
        font-weight: bold;
    }
    .body-signature p {
        margin: 0px;
    }
</style>

<div class="body-paper">
    <hr>
    <p class="body-title">RADIOLOGY REPORT</p>
    <p class="treatment-header"><?= !empty($expertiseData['daftartindakan_nama']) ? $expertiseData['daftartindakan_nama'] : '-' ?> 
    on <?= !empty($expertiseData['tgl_hasilrad']) ? (new DocoHelpers)->convertDate($expertiseData['tgl_hasilrad']) : '-' ?>:</p>
    <?php
        $kesan = !empty($expertiseData['kesan']) ? strip_tags($expertiseData['kesan']) : '-';
        $kesimpulan = !empty($expertiseData['kesimpulan']) ? strip_tags($expertiseData['kesimpulan']) : '-';
        if (!empty($patientData['dokter_perujuk_nama'])) :
    ?>
        <p class="treatment-header">Yth. <?= !empty($patientData['dokter_perujuk_nama']) ? $patientData['dokter_perujuk_nama'] : '-' ?></p>
    <?php
        endif;
    ?>
    <?php
        if (!empty($kesan)) :
    ?>
        <div class="body-item">
            <div class="body-item__title">Deskripsi</div>
            <div class="body-item__content">
                <?= !empty($expertiseData['kesan']) ? $expertiseData['kesan'] : '-' ?>
            </div>
        </div>
    <?php
        endif;
    ?>

    <?php
        if (!empty($kesimpulan)) :
    ?>
            <div class="body-item">
                <div class="body-item__title">Kesan</div>
                <div class="body-item__content">
                    <?= !empty($expertiseData['kesimpulan']) ? $expertiseData['kesimpulan'] : '-' ?>
                </div>
            </div>
    <?php
        endif;
    ?>
    <div class="body-signature">
        <p class="doctor-name"><?= $patientData['dokter_penunjang'] ?></p>
        <p class="doctor-title">(Radiologist)</p>
        <p class="body-signature__date"><?= (new DocoHelpers)->convertDate($patientData['tglmasukpenunjang'], 'd-m-Y H:i') ?></p>
    </div>
</div>