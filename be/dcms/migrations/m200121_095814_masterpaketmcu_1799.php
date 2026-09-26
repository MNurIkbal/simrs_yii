<?php

use yii\db\Migration;

/**
 * Class m200121_095814_masterpaketmcu_1799
 */
class m200121_095814_masterpaketmcu_1799 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('TRUNCATE TABLE lookuptransaksi_m RESTART IDENTITY;');

        $this->execute("INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi) VALUES 
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
                        ('MCU', 21, 'instalasi MCU');
                        ");

        $this->execute('ALTER TABLE "public"."tipepaket_m" 
        ADD COLUMN "is_mcu" bool DEFAULT false;');

        $this->execute('ALTER TABLE "public"."paketpelayanan_mp" 
        ADD COLUMN "paketdetail_id" int4,
        ADD COLUMN "ruangan_id" int4,
        ALTER COLUMN "daftartindakan_id" DROP NOT NULL,
        ALTER COLUMN "tipepaket_id" DROP NOT NULL;');

        $this->execute('DROP VIEW IF exists public.masterpaketmcu_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.masterpaketmcu_v AS 
 SELECT paket_mcu.tipepaket_id,
    paket_mcu.tipepaket_kode,
    paket_mcu.tipepaket_nama,
    paket_mcu.tipepaket_namalainnya,
    paket_mcu.keterangan_tipepaket,
    paket_mcu.is_active,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT paketpelayanan_mp.paketdetail_id,
                    paket_detail.tipepaket_nama AS nama,
                    paketpelayanan_mp.ruangan_id,
                    ruangan_m.ruangan_nama,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    instalasi_m.is_penunjang
                   FROM tipepaket_m
                     JOIN paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN ( SELECT tipepaket_m_1.tipepaket_id,
                            tipepaket_m_1.tipepaket_nama
                           FROM tipepaket_m tipepaket_m_1) paket_detail ON paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id
                     JOIN ruangan_m ON paketpelayanan_mp.ruangan_id = ruangan_m.ruangan_id
                     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                  WHERE tipepaket_m.is_mcu = true AND paket_mcu.tipepaket_id = tipepaket_m.tipepaket_id AND tipepaket_m.is_deleted = false
                UNION ALL
                 SELECT paketpelayanan_mp.daftartindakan_id,
                    tindakan_detail.daftartindakan_nama AS nama,
                    paketpelayanan_mp.ruangan_id,
                    ruangan_m.ruangan_nama,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    instalasi_m.is_penunjang
                   FROM tipepaket_m
                     JOIN paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN ( SELECT daftartindakan_m.daftartindakan_id,
                            daftartindakan_m.daftartindakan_nama
                           FROM daftartindakan_m) tindakan_detail ON paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id
                     JOIN ruangan_m ON paketpelayanan_mp.ruangan_id = ruangan_m.ruangan_id
                     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                  WHERE tipepaket_m.is_mcu = true AND paket_mcu.tipepaket_id = tipepaket_m.tipepaket_id AND tipepaket_m.is_deleted = false) d2) AS detail
   FROM tipepaket_m paket_mcu
  WHERE paket_mcu.is_mcu = true AND paket_mcu.is_deleted = false;");

        $this->execute('ALTER TABLE public.masterpaketmcu_v
  OWNER TO postgres;');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200121_095814_masterpaketmcu_1799 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200121_095814_masterpaketmcu_1799 cannot be reverted.\n";

        return false;
    }
    */
}
