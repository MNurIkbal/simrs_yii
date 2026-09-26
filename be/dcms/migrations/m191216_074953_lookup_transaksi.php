<?php

use yii\db\Migration;

/**
 * Class m191216_074953_lookup_transaksi
 */
class m191216_074953_lookup_transaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('CREATE TABLE public.lookuptransaksi_m
                    (
                    kode_transaksi character varying(255) NOT NULL,
                    kode_id integer NOT NULL,
                    kode_fungsi text,
                    CONSTRAINT lookuptransaksi_m_pkey PRIMARY KEY (kode_transaksi, kode_id)
                    )
                    WITH (
                    OIDS=FALSE
                    );');

         $this->execute('ALTER TABLE public.lookuptransaksi_m
  OWNER TO postgres;');

         $this->execute('TRUNCATE TABLE lookuptransaksi_m RESTART IDENTITY;');


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
                        ('t_gizi', 12, 'nutrisionis dan dietisien (kelompokpegawai_m)');
                        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191216_074953_lookup_transaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191216_074953_lookup_transaksi cannot be reverted.\n";

        return false;
    }
    */
}
