<?php

use yii\db\Migration;

/**
 * Class m211027_062649_migrate_hotfix_lookup_update
 */
class m211027_062649_migrate_hotfix_lookup_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id IN (1095,1094,1093,1092,1091,1090,1089,1088,1087,1086,1085,1084,1083,1082,1081,1080,1079,1078,1077,1076,1075,1074,1073,1072,1071,1070,1069,1068,1067,1066,1065,1064,1063,1062,1061,1060,1059,1058,1057,1056,1055,1054,1053,1052,1051,1050,1049,1048,1047,1046,1045,1044,1043,1042,1041,1040,1039,1038,1037,1036,1035,1034,1033,1032,1031,1030,1029,1028,1027,1026,1025);
        ");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1095, 'nama_depan', 'NY.', 'NY.', 18, '', NULL, '2020-03-02 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1094, 'nama_depan', 'BR.', 'BR.', 17, '', NULL, '2020-03-02 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1093, 'status_periksa', 'DIRAWAT', 'DIRAWAT', NULL, 'W', NULL, '2021-06-17 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1092, 'status_periksa', 'MENINGGAL', 'MENINGGAL', NULL, 'M', NULL, '2021-06-17 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1091, 'alamat_depan', 'KOMP.', 'KOMP.', 5, NULL, NULL, '2021-05-07 17:24:15', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1090, 'alamat_depan', 'KAMP.', 'KAMP.', 4, NULL, NULL, '2021-05-07 17:24:15', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1089, 'alamat_depan', 'JL.', 'JL.', 3, NULL, NULL, '2021-05-07 17:24:15', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1088, 'alamat_depan', 'GANG', 'GANG.', 2, NULL, NULL, '2021-05-07 17:24:15', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1087, 'alamat_depan', 'DESA.', 'DESA.', 1, NULL, NULL, '2021-05-07 17:24:15', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1086, 'nama_depan', 'IBU.', 'IBU.', 16, '', NULL, '2020-03-02 00:00:00', NULL, NULL, NULL, NULL, 't', 'f', NULL, NULL),
            (1085, 'tipe_cara_bayar', 'NON BPJS', 'NON BPJS', 2, NULL, NULL, '2021-10-26 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1084, 'tipe_cara_bayar', 'BPJS', 'BPJS', 1, NULL, NULL, '2021-10-26 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1083, 'tipe_obat', 'ORIGINAL', 'ORIGINAL', NULL, '3', NULL, '2021-09-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1082, 'tipe_obat', 'ME TOO', 'ME TOO', NULL, '2', NULL, '2021-09-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1081, 'tipe_obat', 'GENERIK', 'GENERIK', NULL, '1', NULL, '2021-09-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1080, 'bpjs', 'version', '1.1', NULL, NULL, NULL, '2021-09-27 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1079, 'jenis_dokumen', 'LAIN-LAIN', 'LAIN-LAIN', NULL, NULL, NULL, '2021-09-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1078, 'jenis_dokumen', 'PENUNJANG MEDIS', 'PENUNJANG MEDIS', NULL, NULL, NULL, '2021-09-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1077, 'jenis_dokumen', 'LEMBAR CATATAN DOKTER DAN KEPERAWATAN', 'LEMBAR CATATAN DOKTER DAN KEPERAWATAN', NULL, NULL, NULL, '2021-09-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1076, 'jenis_dokumen', 'ADMINISTRASI', 'ADMINISTRASI', NULL, NULL, NULL, '2021-09-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1075, 'rute_obat', 'Inhalasi', 'Inhalasi', NULL, NULL, NULL, '2021-09-15 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1074, 'rute_obat', 'Nasal', 'Nasal', NULL, NULL, NULL, '2021-09-15 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1073, 'rute_obat', 'Okular', 'Okular', NULL, NULL, NULL, '2021-09-15 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1072, 'rute_obat', 'Vaginal', 'Vaginal', NULL, NULL, NULL, '2021-09-15 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1071, 'rute_obat', 'Supositoral', 'Supositoral', NULL, NULL, NULL, '2021-09-15 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1070, 'rute_obat', 'Topikal', 'Topikal', NULL, NULL, NULL, '2021-09-15 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1069, 'rute_obat', 'Sublingual', 'Sublingual', NULL, NULL, NULL, '2021-09-15 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1068, 'rute_obat', 'Suntikan ( parenteral )', 'Suntikan ( parenteral )', NULL, NULL, NULL, '2021-09-15 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1067, 'rute_obat', 'Orat', 'Orat', NULL, NULL, NULL, '2021-09-15 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1066, 'klasifikasi_ruangan', 'LDR', 'LDR', NULL, NULL, NULL, '2021-09-13 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1065, 'fitur_akses', 'Resume Medis', 'resume_medis', NULL, NULL, NULL, '2021-09-10 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1064, 'fitur_akses', 'Asesmen Medis', 'asesmen_medis', NULL, NULL, NULL, '2021-09-10 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1063, 'fitur_akses', 'Asesmen Keperawatan', 'asesmen_keperawatan', NULL, NULL, NULL, '2021-09-10 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1062, 'klasifikasi_ruangan', 'NICU/PICU', 'NICU/PICU', NULL, NULL, NULL, '2021-09-09 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1061, 'klasifikasi_ruangan', 'ICCU', 'ICCU', NULL, NULL, NULL, '2021-09-09 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1060, 'klasifikasi_ruangan', 'ICU', 'ICU', NULL, NULL, NULL, '2021-09-09 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1059, 'klasifikasi_ruangan', 'Isolasi', 'Isolasi', NULL, NULL, NULL, '2021-09-09 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1058, 'status_periksa', 'Batal Rujuk Rawat Inap', 'Batal Rujuk Rawat Inap', NULL, NULL, NULL, '2021-09-07 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1057, 'status_purchaserequest', 'Belum Approved', 'Belum Approved', 6, NULL, NULL, '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1056, 'status_purchaserequest', 'Approved', 'Approve', 5, NULL, NULL, '2020-08-24 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1055, 'waktu_pelayanan', '60', '60', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1054, 'waktu_pelayanan', '55', '55', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1053, 'waktu_pelayanan', '50', '50', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1052, 'waktu_pelayanan', '45', '45', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1051, 'waktu_pelayanan', '40', '40', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1050, 'waktu_pelayanan', '35', '35', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1049, 'waktu_pelayanan', '30', '30', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1048, 'waktu_pelayanan', '25', '25', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1047, 'waktu_pelayanan', '20', '20', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1046, 'waktu_pelayanan', '15', '15', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1045, 'waktu_pelayanan', '10', '10', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1044, 'waktu_pelayanan', '5', '5', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1043, 'waktu_pelayanan', '1', '1', NULL, NULL, 'Satuan Menit', '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1042, 'status_mcu', 'Close', 'Close', NULL, NULL, NULL, '2021-08-19 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1041, 'status_mcu', 'Open', 'Open', NULL, NULL, NULL, '2021-08-19 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1040, 'jenis_instruksi', 'UDD', 'UDD', 4, NULL, NULL, '2021-07-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1039, 'kegiatan_keperawatan', 'Bidan', 'Bidan', NULL, NULL, NULL, '2021-07-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1038, 'kegiatan_keperawatan', 'Perawat', 'Perawat', NULL, NULL, NULL, '2021-07-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1037, 'LOB', 'MCU', 'MCU', NULL, NULL, NULL, '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1036, 'LOB', 'IPD', 'IPD', NULL, NULL, NULL, '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1035, 'LOB', 'IGD', 'IGD', NULL, NULL, NULL, '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1034, 'LOB', 'OPD', 'OPD', NULL, NULL, NULL, '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1033, 'jenis_layanan', 'Kelas', 'Kelas', 1, NULL, NULL, '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1032, 'status_udd', 'Diterima', 'Diterima', NULL, NULL, NULL, '2021-06-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1031, 'status_udd', 'Dikirim', 'Dikirim', NULL, NULL, NULL, '2021-06-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1030, 'status_udd', 'Proses', 'Proses', NULL, NULL, NULL, '2021-06-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1029, 'status_udd', 'Belum Proses', 'Belum Proses', NULL, NULL, NULL, '2021-06-21 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1028, 'jenis_layanan', 'Obat', 'Obat', 5, NULL, NULL, '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1027, 'jenis_layanan', 'Paket', 'Paket', 4, NULL, NULL, '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1026, 'jenis_layanan', 'Tindakan', 'Tindakan', 3, NULL, NULL, '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1025, 'jenis_layanan', 'Kelompok Tindakan', 'Kelompok Tindakan', 2, NULL, NULL, '2021-08-04 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211027_062649_migrate_hotfix_lookup_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211027_062649_migrate_hotfix_lookup_update cannot be reverted.\n";

        return false;
    }
    */
}
