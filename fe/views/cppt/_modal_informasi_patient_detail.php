<?php


use app\components\DHtml;
use app\components\DocoConstants as ComponentsDocoConstants;
use yii\web\View;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;

?>

<style lang="">
    .form-title{
        margin-bottom: 12px;
    }

    .form-title > .heading-1{
        font-weight: 600;
        font-size: 21px;
        text-decoration: underline;
        display: block;
        text-align: center;
    }

    .form-title > .heading-2{
        font-weight: 300;
        font-size: 16px;
        font-style: italic;
        display: block;
        text-align: center;
    }


    .form-patient{
        padding: 5px 5px;
        display: flex;
        flex-direction: row;
    }

    .form-patient__input-null{
        width: 50%;
        border-bottom: 1px dotted;
    }

    .form-patient p{
        padding-left: 5px;
        padding-right: 5px;
    }

    .form-patient__label{
        font-size: 12px;
        width: 200px;
        position: relative;
    }

    .form-patient__label-sm{
        font-size: 12px;
        width: 100px;
        position: relative;
    }
    .form-patient__label label, .form-patient__label-sm label{
        font-weight: 600;
        display: block;
    }

    .form-patient__label label:after, .form-patient__label-sm label:after{
        content: ":";
        right: 0;
        position: absolute;
    }

    .form-patient__label span, .form-patient__label-sm span{
        font-style: italic;
        display: block;
        padding-left: 10%;
        font-weight: 300;
        font-size: 10px;
        line-height: 1.2;
    }

    .form-patient-signature{

    }

    .form-patient-signature > .form-patient-signature__label{
        font-size: 12px;
        font-weight: 600;
    }

    .form-patient-signature > .form-patient-signature__label span{
        font-size: 10px;
        font-style: italic;
        font-weight: 300;
    }

    .form-patient-signature > .form-patient-signature__name{
        margin-top:  70px;
        margin-bottom: 10px;
        padding: 0px 10px;
        border-bottom: 1px dotted;
        max-width: 200px;
    }

</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Informasi Data Detail Pasien</h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-12 form-title">
            <div class="heading-1">INFORMASI PASIEN</div>
            <div class="heading-2">PATIENT INFORMATION</div>
        </div>

        <div class="col-sm-12 form-patient">
            <div class="form-patient__label">
                <label for="">Nama Lengkap</label>
                <span>Full name</span>
            </div>
            <p><?=isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : ' '?></p>
        </div>

        <!-- One Row -->
        <div class="col-sm-12 col-md-7 form-patient">
            <div class="form-patient__label">
                <label for="">Tempat/Tanggal Lahir</label>
                <span>Place/Date of birth</span>
            </div>
            <p><?= isset($data_pasien['tempat_lahir']) ? $data_pasien['tempat_lahir'].' ,' : ' ' ?> <?= isset($data_pasien['tanggal_lahir']) ? date('d F Y', strtotime($data_pasien['tanggal_lahir'])) : '  '?></p>
        </div>
        <div class="col-sm-12 col-md-5 form-patient">
            <div class="form-patient__label-sm">
                <label for="">Usia</label>
                <span>Age</span>
            </div>
            <!-- 31556926 Total second in one year -->
            <p><?= isset($data_pasien['tanggal_lahir']) ? floor((time() - strtotime($data_pasien['tanggal_lahir'])) / 31556926).' Tahun' : ' - ' ?></p>
        </div>
        <!-- End of One Row -->

        <!-- Start of one row -->
        <div class="col-sm-12 col-md-7 form-patient">
            <div class="form-patient__label">
                <label for="">Alamat</label>
                <span>Address</span>
            </div>
            <p><?= isset($data_pasien['alamat_pasien']) ? $data_pasien['alamat_pasien'] : ' ' ?></p>
        </div>
        <div class="col-sm-12 col-md-5 form-patient">
            <div class="form-patient__label-sm">
                <label for="">Kota</label>
                <span>City</span>
            </div>
            <p><?= isset($data_pasien['kabupaten_nama']) ? $data_pasien['kabupaten_nama'] : ' ' ?></p>
        </div>
        <!-- End of one row -->

        <!-- Start of one row -->
        <div class="col-sm-12 col-md-5 form-patient">
            <div class="form-patient__label">
                <label for="">Jenis Kelamin</label>
                <span>Sex</span>
            </div>
            <p><?= isset($data_pasien['jenis_kelamin']) ? $data_pasien['jenis_kelamin'] : ' '?></p>
        </div>
        <div class="col-sm-12 col-md-7 form-patient">
            <div class="form-patient__label-sm">
                <label for="">Golongan Darah</label>
                <span>Blood Type</span>
            </div>
            <p>
                <?php foreach($config_golongan_darah as $key => $golongan_darah) { ?>
                    <input type='checkbox' name='golongan_darah' value='<?= $key ?>' <?= $key == $data_pasien['golongandarah'] || (empty($data_pasien['golongandarah']) && $key == ComponentsDocoConstants::GOL_DARAH_TIDAKTAHU) ? 'checked' : '' ?> disabled>
                    <label for=""><?= $golongan_darah ?></label>
                <?php } ?>
            </p>
        </div>
        <!-- End of one row -->

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Pekerjaan</label>
                <span>Job</span>
            </div>
            <p><?= isset($data_pasien['pekerjaan_nama']) ? $data_pasien['pekerjaan_nama'] : ' ' ?></p>
        </div>

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Status Perkawinan</label>
                <span>Marital Status</span>
            </div>
            <p><?= isset($data_pasien['status_perkawinan']) ? $data_pasien['status_perkawinan'] : ' ' ?></p>
        </div>

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Agama</label>
                <span>Religion</span>
            </div>
            <p><?= isset($data_pasien['agama_pasien']) ? $data_pasien['agama_pasien'] : ' ' ?></p>
        </div>

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Kewarganegaraan</label>
                <span>Nationality</span>
            </div>
            <p><?= isset($data_pasien['warganegara']) ? $data_pasien['warganegara'] : ' ' ?></p>
        </div>

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Pendidikan</label>
                <span>Education</span>
            </div>
            <p><?= isset($data_pasien['pendidikan_nama']) ? $data_pasien['pendidikan_nama'] : ' ' ?> </p>
        </div>

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Nama Perusahaan</label>
                <span>Company's Name</span>
            </div>
            <p><?= isset($data_pasien['nama_perusahaan']) ? $data_pasien['nama_perusahaan'] : ' ' ?></p>
        </div>

        <!-- Start of one row -->
        <div class="col-md-12 col-md-7 form-patient">
            <div class="form-patient__label">
                <label for="">No. Telp Rumah/Kantor</label>
                <span>Home/Office phone number</span>
            </div>
            <p><?= isset($data_pasien['no_telepon_pasien']) ? $data_pasien['no_telepon_pasien'] : '  ' ?></p>
        </div>

        <div class="col-md-12 col-md-5 form-patient form-patient">
            <div class="form-patient__label-sm">
                <label for="">Blackberry Pin</label>
            </div>
            <div class="form-patient__input-null"></div>
        </div>
        <!-- End of one row -->

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">No. Handphone</label>
                <span>Mobile number</span>
            </div>
            <p><?= isset($data_pasien['no_mobile_pasien']) ? $data_pasien['no_mobile_pasien'] : '  ' ?></p>
        </div>

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Email/Surat Elektronik</label>
                <span>Email</span>
            </div>
            <p><?= isset($data_pasien['alamatemail']) ? $data_pasien['alamatemail'] : ' ' ?></p>
        </div>

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Kartu Identitas</label>
                <span>ID Card</span>
            </div>
            <p class=""><?= isset($data_pasien['identitas']) ? $data_pasien['identitas'] : ' ' ?></p>
        </div>

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">No Identitas</label>
                <span>ID Card Number</span>
            </div>
            <p><?= isset($data_pasien['no_identitas_pasien']) ? $data_pasien['no_identitas_pasien'] : ' ' ?></p>
        </div>

        <!-- End of Informasi Pasien -->

        <!-- Start of Penanggung Jawab & Kerabat -->

        <div class="col-sm-12 form-title">
            <div class="heading-1">PENANGGUNG JAWAB & KERABAT</div>
            <div class="heading-2">GUARANTOR & FAMILY</div>
        </div>

        <div class="col-sm-12 form-patient">
            <div class="form-patient__label">
                <label for="">Nama Lengkap</label>
                <span>Full name</span>
            </div>
            <p><?= isset($data_keluarga['keluarga_nama']) ? $data_keluarga['keluarga_nama'] : ' ' ?></p>
        </div>

        <!-- One Row -->
        <div class="col-sm-12 col-md-7 form-patient">
            <div class="form-patient__label">
                <label for="">Tempat/Tanggal Lahir</label>
                <span>Place/Date of birth</span>
            </div>
            <p><?= isset($data_keluarga['keluarga_tempat_lahir']) ? $data_keluarga['keluarga_tempat_lahir'].' ,' : ' ' ?>, <?= isset($data_keluarga['keluarga_tanggal_lahir']) ? date('d F Y', strtotime($data_keluarga['keluarga_tanggal_lahir'])) : '  '?></p>
        </div>
        <div class="col-sm-12 col-md-5 form-patient">
            <div class="form-patient__label-sm">
                <label for="">Usia</label>
                <span>Age</span>
            </div>
            <!-- 31556926 Total second in one year -->
            <p><?= isset($data_keluarga['keluarga_tanggal_lahir']) ? floor((time() - strtotime($data_keluarga['keluarga_tanggal_lahir'])) / 31556926) : ' ' ?></p>
        </div>
        <!-- End of One Row -->

        <div class="col-sm-12 form-patient">
            <div class="form-patient__label">
                <label for="">Alamat</label>
                <span>Address</span>
            </div>
            <p><?= isset($data_keluarga['keluarga_alamat']) ? $data_keluarga['keluarga_alamat'] : ' ' ?></p>
        </div>

        <!-- Start of one row -->
        <div class="col-sm-12 col-md-5 form-patient">
            <div class="form-patient__label">
                <label for="">Jenis Kelamin</label>
                <span>Sex</span>
            </div>
            <p><?= isset($data_keluarga['jenis_kelamin']) ? $data_keluarga['jenis_kelamin'] : ' ' ?></p>
        </div>
        <div class="col-sm-12 col-md-7 form-patient">
            <div class="form-patient__label-sm">
                <label for="">Golongan Darah</label>
                <span>Blood Type</span>
            </div>
            <p>
                <?php foreach($config_golongan_darah as $key => $golongan_darah) { ?>
                    <input type='checkbox' name='golongan_darah' value='<?= $key ?>' <?= $key == isset($data_keluarga['keluarga_golongan_darah']) ? 'checked' : '' ?> disabled>
                    <label for=""><?= $golongan_darah ?></label>
                <?php } ?>
            </p>
        </div>
        <!-- End of one row -->

        <!-- Start of one row -->
        <div class="col-md-12 col-md-7 form-patient">
            <div class="form-patient__label">
                <label for="">No. Telp/Handphone</label>
                <span>Mobile number</span>
            </div>
            <p><?= isset($data_keluarga['keluarga_no_telepon']) ? $data_keluarga['keluarga_no_telepon'] : '   ' ?></p>
        </div>
        <div class="col-md-12 col-md-5 form-patient form-patient">
            <div class="form-patient__label-sm">
                <label for="">Blackberry Pin</label>
            </div>
            <div class="form-patient__input-null"></div>
        </div>
        <!-- End of one row -->

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Kartu Identitas</label>
                <span>ID Card</span>
            </div>
            <p><?= isset($data_keluarga['jenisidentitas']) ? $data_keluarga['jenisidentitas'] : ' ' ?></p>
        </div>


        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">No Identitas</label>
                <span>ID Card Number</span>
            </div>
            <p><?= isset($data_keluarga['no_identitas_keluarga']) ? $data_keluarga['no_identitas_keluarga'] : ' ' ?></p>
        </div>

        <div class="col-md-12 form-patient">
            <div class="form-patient__label">
                <label for="">Hubungan dengan pasien</label>
                <span>Relationship with Patient</span>
            </div>
            <p><?= isset($data_keluarga['hubungan_keluarga']) ? $data_keluarga['hubungan_keluarga'] : ' ' ?></p>
        </div>

        <!-- End of Penanggung Jawab & Kerabat -->

        <!-- Start of Tandatangan -->
        <div class="col-sm-12 col-md-4 col-md-offset-8 form-patient-signature">
            <div class="form-patient-signature__label">
                Pasien / Penanggung Jawab  <span>Patient / Family</span>
            </div>
            <div class="form-patient-signature__name">
                <?= isset($data_keluarga['keluarga_nama']) ? $data_keluarga['keluarga_nama'] : (isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : ' ') ?>
            </div>
            <div class="form-patient-signature__label">
                Nama dan tanda tangan  <span>Name and Signature</span>
            </div>
        </div>
        <!-- End Of Tandatangan -->
    </div>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
