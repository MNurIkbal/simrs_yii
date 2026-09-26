<?php

use yii\db\Migration;

/**
 * Class m201019_031128_migrate_20201019_lookuptransaksi
 */
class m201019_031128_migrate_20201019_lookuptransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('truncate table lookuptransaksi_m RESTART IDENTITY;');

        $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi) VALUES 
('apotek_rd', 13, 'Untuk Default Apotek RD'),
('asal_rujukan', 1, 'default asal rujukan'),
('FARMASI', 6, 'Untuk Instalasi Farmasi'),
('Hari', 350, 'satuan tindakan'),
('IBS', 12, 'Instalasi Bedah Sentral'),
('Jam', 352, 'satuan tindakan'),
('Kali', 351, 'satuan tindakan'),
('kasus_penyakit', 23, 'default kasus penyakit'),
('kelompok_karcis', 17, 'digunakan untuk pengelompokan tindakan karcis (kelompoktindakan_m)'),
('kelompok_lab', 26, 'digunakan untuk pengelompokan tindakan laboratorium (kelompoktindakan_m)'),
('kelompok_rad', 10, 'digunakan untuk pengelompokan tindakan radiologi (kelompoktindakan_m)'),
('komponen_jas_dok', 2, 'Komponen Jasa Dokter'),
('komponen_rs', 1, 'Komponen Jasa Rumah Sakit'),
('komponen_total', 6, 'digunakan untuk komponen total tarif tindakan (komponentarif_m)'),
('LAB', 4, 'Instalasi LAB'),
('MCU', 21, 'instalasi MCU'),
('meninggal', 4, 'cara keluar meninggal (carakeluar_m)'),
('RAD', 5, 'Instalasi Radiologi'),
('RD', 2, 'kode instalasi rawat darurat (instalasi_m)'),
('RI', 3, 'kode instalasi rawat inap (instalasi_m)'),
('riwayat', 20, 'Lain - lain'),
('RJ', 1, 'kode instalasi rawat jalan (instalasi_m)'),
('rujuk_ranap', 5, 'cara keluar rujuk rawat inap (carakeluar_m)'),
('status_approve', 564, 'Default Status Approve'),
('t_gizi', 12, 'nutrisionis dan dietisien (kelompokpegawai_m)'),
('t_kebidanan', 3, 'bidan (kelompokpegawai_m)'),
('t_kefarmasian', 4, 'apoteker (kelompokpegawai_m)'),
('t_keperawatan', 2, 'perawat, suster (kelompokpegawai_m)'),
('t_kesehatan', 5, 'epidemiolog kesehatan, tenaga promosi kesehatan dan ilmu perilaku, pembimbing kesehatan kerja, tenaga administrasi dan kebijakan kesehatan, tenaga biostatistik dan kependudukan, serta tenaga kesehatan reproduksi dan keluarga (kelompokpegawai_m)'),
('t_kesling', 6, 'tenaga sanitasi lingkungan, entomolog kesehatan, dan mikrobiolog kesehatan (kelompokpegawai_m)'),
('t_kestradisional', 11, 'tenaga kesehatan tradisional ramuan dan tenaga kesehatan tradisional keterampilan (kelompokpegawai_m)'),
('t_keterapian', 8, 'fisioterapis, okupasi terapis, terapis wicara, dan akupunktur (kelompokpegawai_m)'),
('t_medis', 1, 'tenaga medis - dokter, dokter gigi, dokter spesialis, dokter gigi spesialis (kelompokpegawai_m)'),
('t_nonkesehatan', 7, 'non kesehatan (kelompokpegawai_m)'),
('t_tekbiomedik', 10, 'radiografer, elektromedis, ahli teknologi laboratorium medik, fisikawan medik, radioterapis, dan ortotik prostetik (kelompokpegawai_m)'),
('t_tekmedik', 9, 'perekam medis dan informasi kesehatan, teknik kardiovaskuler, teknisi pelayanan darah, refraksionis optisien / optometris, teknisi gigi, penata anestesi, terapis gigi dan mulut, dan audiologis (kelompokpegawai_m)'),
('visite', 32, 'kelompok tindakan visite dokter'),
('direktur', 1, 'jabatan_m, untuk direktur'),
('head_apoteker', 2, 'jabatan_m, untuk head of apoteker'),
('head_purchasing', 3, 'jabatan_m, untuk head of purchasing'),
('PEMBULATAN', 99990, 'daftartindakan untuk pembulatan'),
('DISKON', 99991, 'daftartindakan untuk diskon'),
('ruangan_tht', 21, 'kode ruangan THT (ruangan_m)'),
('ruangan_mata', 4, 'kode ruangan Mata (ruangan_m)'),
('ruangan_kardiologi', 1, 'kode ruangan poli jantung (ruangan_m)');
            ");
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_031128_migrate_20201019_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_031128_migrate_20201019_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
