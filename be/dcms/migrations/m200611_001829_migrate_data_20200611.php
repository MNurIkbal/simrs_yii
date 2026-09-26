<?php

use yii\db\Migration;

/**
 * Class m200611_001829_migrate_data_20200611
 */
class m200611_001829_migrate_data_20200611 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type ='keadaan_masuk';");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (158, 'keadaan_masuk', 'Gawat - Tidak Darurat', 'Gawat - Tidak Darurat', 1, '', NULL, '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (159, 'keadaan_masuk', 'Tidak Gawat - Tidak Darurat', 'Tidak Gawat - Tidak Darurat', 2, '', NULL, '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (160, 'keadaan_masuk', 'Gawat - Darurat', 'Gawat - Darurat', 3, '', NULL, '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (161, 'keadaan_masuk', 'Tidak Gawat - Darurat', 'Tidak Gawat - Tidak Darurat', 4, '', NULL, '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");

        $this->execute("DELETE FROM lookup_m WHERE lookup_type ='jenis_kamar';");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (340, 'jenis_kamar', 'Pria', 'Pria', 1, NULL, NULL, '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (341, 'jenis_kamar', 'Wanita', 'Wanita', 2, NULL, NULL, '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (342, 'jenis_kamar', 'Fleksibel', 'Fleksibel', 3, NULL, NULL, '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (431, 'jenis_kamar', 'Campur', 'Campur', 4, NULL, NULL, '2020-01-30 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");

        $this->execute("DELETE FROM lookup_m WHERE lookup_type ='jenis_transaksi';");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (669, 'jenis_transaksi', 'Pengeluaran', 'Pengeluaran', 1, NULL, NULL, '2020-03-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (668, 'jenis_transaksi', 'Penerimaan', 'Penerimaan', 2, NULL, NULL, '2020-03-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");

        $this->execute("DELETE FROM lookup_m WHERE lookup_type ='transaksi_konsul';");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (672, 'transaksi_konsul', 'Konsul Poli', 'Konsul Poli', 1, NULL, NULL, '2020-06-03 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (673, 'transaksi_konsul', 'Rencana Kontrol', 'Rencana Kontrol', 2, NULL, NULL, '2020-06-03 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");

        $this->execute("DELETE FROM lookup_m WHERE lookup_type ='status_worklistresep';");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (674, 'status_worklistresep', 'Belum Disiapkan', 'Belum Disiapkan', 1, NULL, NULL, '2020-06-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (675, 'status_worklistresep', 'Disiapkan', 'Disiapkan', 2, NULL, NULL, '2020-06-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (676, 'status_worklistresep', 'Ditelaah', 'Ditelaah', 3, NULL, NULL, '2020-06-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (677, 'status_worklistresep', 'QC', 'QC', 4, NULL, NULL, '2020-06-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (678, 'status_worklistresep', 'Siap diserahkan', 'Siap diserahkan', 5, NULL, NULL, '2020-06-08 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
        
        $this->execute("DELETE FROM lookup_m WHERE lookup_type ='tipe_transaksi';");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (700, 'tipe_transaksi', 'Vendor', 'Vendor', 1, NULL, NULL, '2020-03-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (701, 'tipe_transaksi', 'Karyawan', 'Karyawan', 2, NULL, NULL, '2020-03-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (702, 'tipe_transaksi', 'Pasien', 'Pasien', 3, NULL, NULL, '2020-03-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");

        $this->execute("DELETE from lookuptransaksi_m;");
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
            ('visite', 32, 'kelompok tindakan visite dokter'),
            ('riwayat', 20, 'Lain - lain'),
            ('status_approve', 564, 'Default Status Approve');
        ");
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200611_001829_migrate_data_20200611 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200611_001829_migrate_data_20200611 cannot be reverted.\n";

        return false;
    }
    */
}
