<?php

use yii\db\Migration;

/**
 * Class m200506_033000_migrateseeder_20200506
 */
class m200506_033000_migrateseeder_20200506 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DELETE FROM lookup_m WHERE lookup_type=\'status_konsulpoli\';');

         $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (671, 'status_konsulpoli', 'Sudah Dijawab', 'Sudah Dijawab', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (670, 'status_konsulpoli', 'Belum Dijawab', 'Belum Dijawab', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");
         
         $this->execute('DELETE FROM lookuptransaksi_m;');

         $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi) VALUES 
            ('kelompok_karcis', 17, 'digunakan untuk pengelompokan tindakan karcis (kelompoktindakan_m)'),
            ('RJ', 1, 'kode instalasi rawat jalan (instalasi_m)'),
            ('RD', 2, 'kode instalasi rawat darurat (instalasi_m)'),
            ('RI', 3, 'kode instalasi rawat inap (instalasi_m)'),
            ('komponen_total', 6, 'digunakan untuk komponen total tarif tindakan (komponentarif_m)'),
            ('kelompok_rad', 10, 'digunakan untuk pengelompokan tindakan radiologi (kelompoktindakan_m)'),
            ('kelompok_lab', 26, 'digunakan untuk pengelompokan tindakan laboratorium (kelompoktindakan_m)'),
            ('rujuk_ranap', 5, 'cara keluar rujuk rawat inap (carakeluar_m)'),
            ('meninggal', 4, 'cara keluar meninggal (carakeluar_m)'),
            ('t_medis', 1, 'tenaga medis - dokter, dokter gigi, dokter spesialis, dokter gigi spesialis (kelompokpegawai_m)'),
            ('t_keperawatan', 2, 'perawat, suster (kelompokpegawai_m)'),
            ('t_kebidanan', 3, 'bidan (kelompokpegawai_m)'),
            ('t_kefarmasian', 4, 'apoteker (kelompokpegawai_m)'),
            ('t_kesehatan', 5, 'epidemiolog kesehatan, tenaga promosi kesehatan dan ilmu perilaku, pembimbing kesehatan kerja, tenaga administrasi dan kebijakan kesehatan, tenaga biostatistik dan kependudukan, serta tenaga kesehatan reproduksi dan keluarga (kelompokpegawai_m)'),
            ('t_kesling', 6, 'tenaga sanitasi lingkungan, entomolog kesehatan, dan mikrobiolog kesehatan (kelompokpegawai_m)'),
            ('t_nonkesehatan', 7, 'non kesehatan (kelompokpegawai_m)'),
            ('t_keterapian', 8, 'fisioterapis, okupasi terapis, terapis wicara, dan akupunktur (kelompokpegawai_m)'),
            ('t_tekmedik', 9, 'perekam medis dan informasi kesehatan, teknik kardiovaskuler, teknisi pelayanan darah, refraksionis optisien / optometris, teknisi gigi, penata anestesi, terapis gigi dan mulut, dan audiologis (kelompokpegawai_m)'),
            ('t_tekbiomedik', 10, 'radiografer, elektromedis, ahli teknologi laboratorium medik, fisikawan medik, radioterapis, dan ortotik prostetik (kelompokpegawai_m)'),
            ('t_kestradisional', 11, 'tenaga kesehatan tradisional ramuan dan tenaga kesehatan tradisional keterampilan (kelompokpegawai_m)'),
            ('t_gizi', 12, 'nutrisionis dan dietisien (kelompokpegawai_m)'),
            ('asal_rujukan', 1, 'default asal rujukan'),
            ('kasus_penyakit', 23, 'default kasus penyakit'),
            ('MCU', 21, 'instalasi MCU'),
            ('visite', 32, 'kelompok tindakan visite dokter');
            ");

         $this->execute('TRUNCATE TABLE riwayat_m RESTART IDENTITY;');

         $this->execute("
            INSERT INTO public.riwayat_m(riwayat_id, riwayat_nama, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1, 'Tekanan Darah Tinggi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (2, 'Tekanan Darah Rendah', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (3, 'Penyakit Jantung', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (4, 'Sakit Asma', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (5, 'TBC Paru', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (6, 'Sakit Lambung / Maag', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (7, 'Sakit Kuning / Empedu', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (8, 'Batu Ginjal', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (9, 'Kencing Batu', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (10, 'Sakit Ginjal', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (11, 'Kencing Manis', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (12, 'Rematik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (13, 'Penyakit Gondok', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (14, 'Geger Otak', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (15, 'Radang Otak', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (16, 'Sakit Ayan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (17, 'Kusta', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (18, 'Kanker Tumor', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (19, 'Alergi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200506_033000_migrateseeder_20200506 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200506_033000_migrateseeder_20200506 cannot be reverted.\n";

        return false;
    }
    */
}
