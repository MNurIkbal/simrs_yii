<?php

use yii\db\Migration;

/**
 * Class m210326_074625_migrate_20210326_lookuptransaksi
 */
class m210326_074625_migrate_20210326_lookuptransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('TRUNCATE TABLE lookuptransaksi_m;');

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES 
('ADMINISTRASI', 99992, 'daftartindakan untuk adm', NULL),
('apotek_rd', 13, 'Untuk Default Apotek RD', NULL),
('apotek_ri', 12, 'Untuk Default Apotek RI', NULL),
('apotek_rj', 6, 'Untuk Default Apotek RJ', NULL),
('asal_rujukan', 1, 'default asal rujukan', NULL),
('asuransi', 2, 'carabayar_m_carabayar_id', NULL),
('biaya_sendiri', 5, 'carabayar_m_carabayar_id', NULL),
('direktur', 1, 'jabatan_m, untuk direktur', NULL),
('DISKON', 99991, 'daftartindakan untuk diskon', NULL),
('FARMASI', 6, 'Untuk Instalasi Farmasi', NULL),
('Hari', 350, 'satuan tindakan', NULL),
('head_apoteker', 2, 'jabatan_m, untuk head of apoteker', NULL),
('head_purchasing', 3, 'jabatan_m, untuk head of purchasing', NULL),
('IBS', 12, 'Instalasi Bedah Sentral', NULL),
('Jam', 352, 'satuan tindakan', NULL),
('jaminan', 6, 'carabayar_m_carabayar_id', NULL),
('JasaAsuhanGizi', 1125, 'kode tindakan jasa asuhan gizi, digunakan untuk pengambilan tarif jasa asuhan gizi', NULL),
('JPK', 3732, 'daftartindakan_id default JPK', '[3732]'),
('kabupaten_id', 3174, 'default kabupaten pasien baru', NULL),
('Kali', 351, 'satuan tindakan', NULL),
('kasus_penyakit', 23, 'default kasus penyakit', NULL),
('kecamatan_id', 99999, 'default kecamatan pasien baru', NULL),
('kelompok_karcis', 17, 'digunakan untuk pengelompokan tindakan karcis (kelompoktindakan_m)', NULL),
('kelompok_lab', 26, 'digunakan untuk pengelompokan tindakan laboratorium (kelompoktindakan_m)', NULL),
('kelompok_rad', 10, 'digunakan untuk pengelompokan tindakan radiologi (kelompoktindakan_m)', NULL),
('kelompok_tindakan', 0, 'list kelompok tindakan ', '[32]'),
('kelurahan_id', 99999, 'default kelurahan pasien baru', NULL),
('komponen_jas_dok', 2, 'Komponen Jasa Dokter', NULL),
('komponen_rs', 1, 'Komponen Jasa Rumah Sakit', NULL),
('komponen_total', 6, 'digunakan untuk komponen total tarif tindakan (komponentarif_m)', NULL),
('LAB', 4, 'Instalasi LAB', NULL),
('MCU', 21, 'instalasi MCU', NULL),
('meninggal', 4, 'cara keluar meninggal (carakeluar_m)', NULL),
('not_in_kelompoktindakan_m', 33, 'tidak menampilkan kategori makanan di tindakan rajal', NULL),
('PEMBULATAN', 99990, 'daftartindakan untuk pembulatan', NULL),
('pemeriksaan_pcr', 35, 'jenispemeriksaanlab_id', NULL),
('perusahaan', 7, 'carabayar_m_carabayar_id', NULL),
('provinsi_id', 31, 'default provinsi pasien baru', NULL),
('RAD', 5, 'Instalasi Radiologi', NULL),
('RD', 2, 'kode instalasi rawat darurat (instalasi_m)', NULL),
('RI', 3, 'kode instalasi rawat inap (instalasi_m)', NULL),
('riwayat', 20, 'Lain - lain', NULL),
('RJ', 1, 'kode instalasi rawat jalan (instalasi_m)', NULL),
('ruangan_gigi', 119, 'kode ruangan poli gigi (ruangan_m)', NULL),
('ruangan_gizi', 147, 'kode ruangan poli gizi (ruangan_m)', NULL),
('ruangan_internis', 101, 'kode ruangan penyakit dalam (ruangan_m)', NULL),
('ruangan_kardiologi', 1, 'kode ruangan poli jantung (ruangan_m)', NULL),
('ruangan_mata', 4, 'kode ruangan Mata (ruangan_m)', NULL),
('ruangan_neurologi', 115, 'kode ruangan neurologi (ruangan_m)', NULL),
('ruangan_obsgyn', 116, 'kode ruangan obgyn (ruangan_m)', NULL),
('ruangan_tht', 21, 'kode ruangan THT (ruangan_m)', NULL),
('ruangan_urologi', 109, 'kode ruangan poli urologi (ruangan_m)', NULL),
('rujuk_ranap', 5, 'cara keluar rujuk rawat inap (carakeluar_m)', NULL),
('status_approve', 564, 'Default Status Approve', NULL),
('t_gizi', 12, 'nutrisionis dan dietisien (kelompokpegawai_m)', NULL),
('tindakan_keperawatan', 0, 'untuk menampilkan  tindakan kelas pelayanan keperawaatan dan dokter jaga', '[3732,1088,1079,1083]'),
('t_kebidanan', 3, 'bidan (kelompokpegawai_m)', NULL),
('t_kefarmasian', 4, 'apoteker (kelompokpegawai_m)', NULL),
('t_keperawatan', 2, 'perawat, suster (kelompokpegawai_m)', NULL),
('t_kesehatan', 5, 'epidemiolog kesehatan, tenaga promosi kesehatan dan ilmu perilaku, pembimbing kesehatan kerja, tenaga administrasi dan kebijakan kesehatan, tenaga biostatistik dan kependudukan, serta tenaga kesehatan reproduksi dan keluarga (kelompokpegawai_m)', NULL),
('t_kesling', 6, 'tenaga sanitasi lingkungan, entomolog kesehatan, dan mikrobiolog kesehatan (kelompokpegawai_m)', NULL),
('t_kestradisional', 11, 'tenaga kesehatan tradisional ramuan dan tenaga kesehatan tradisional keterampilan (kelompokpegawai_m)', NULL),
('t_keterapian', 8, 'fisioterapis, okupasi terapis, terapis wicara, dan akupunktur (kelompokpegawai_m)', NULL),
('t_medis', 1, 'tenaga medis - dokter, dokter gigi, dokter spesialis, dokter gigi spesialis (kelompokpegawai_m)', NULL),
('t_nonkesehatan', 7, 'non kesehatan (kelompokpegawai_m)', NULL),
('t_tekbiomedik', 10, 'radiografer, elektromedis, ahli teknologi laboratorium medik, fisikawan medik, radioterapis, dan ortotik prostetik (kelompokpegawai_m)', NULL),
('t_tekmedik', 9, 'perekam medis dan informasi kesehatan, teknik kardiovaskuler, teknisi pelayanan darah, refraksionis optisien / optometris, teknisi gigi, penata anestesi, terapis gigi dan mulut, dan audiologis (kelompokpegawai_m)', NULL),
('VisitDokterSpesialis', 99993, 'Tindakan Visit Dokter Spesialis', NULL),
('visite', 32, 'kelompok tindakan visite dokter', NULL);
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210326_074625_migrate_20210326_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210326_074625_migrate_20210326_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
