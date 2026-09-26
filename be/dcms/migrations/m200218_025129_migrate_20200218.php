<?php

use yii\db\Migration;

/**
 * Class m200218_025129_migrate_20200218
 */
class m200218_025129_migrate_20200218 extends Migration
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
('MCU', 21, 'instalasi MCU'),
('visite', 32, 'kelompok tindakan visite dokter');
");

        $this->execute('DROP VIEW if exists public.closing_kasir_view;');

        $this->execute("
            CREATE OR REPLACE VIEW public.closing_kasir_view AS 
 SELECT agr_bukti_bayar.tandabuktibayar_id,
    agr_bukti_bayar.ruangan_id,
    agr_bukti_bayar.bayaruangmuka_id,
    agr_bukti_bayar.closingkasir_id,
    agr_bukti_bayar.pembayaranpelayanan_id,
    agr_bukti_bayar.shift_id,
    agr_bukti_bayar.nourutkasir,
    agr_bukti_bayar.nobuktibayar,
    agr_bukti_bayar.tglbuktibayar,
    agr_bukti_bayar.uangditerima,
    agr_bukti_bayar.pendaftaran_id,
        CASE
            WHEN pendaftaran_t.no_pendaftaran IS NULL THEN penjualanresep_t.noresep
            ELSE pendaftaran_t.no_pendaftaran
        END AS no_pendaftaran,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    agr_bukti_bayar.pegawai1_id,
    agr_bukti_bayar.no_pembayaran,
    pasien_m.no_rekam_medik,
    agr_bukti_bayar.carabayar_id,
    agr_bukti_bayar.carabayar_nama,
    agr_bukti_bayar.penjamin_id,
    agr_bukti_bayar.penjamin_nama,
        CASE
            WHEN agr_bukti_bayar.total_tagihan IS NULL THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tagihan
        END AS jmlpembayaran,
        CASE
            WHEN agr_bukti_bayar.total_tunai IS NULL THEN agr_bukti_bayar.jmlpembayaran
            ELSE agr_bukti_bayar.total_tunai
        END AS pembayaran_tunai,
    COALESCE(agr_bukti_bayar.total_nontunai, 0::double precision) AS pembayaran_nontunai,
    COALESCE(agr_bukti_bayar.total_penjamin, 0::double precision) AS pembayaran_penjamin,
    agr_bukti_bayar.pembayaran_id
   FROM ( SELECT tandabuktibayar_t.tandabuktibayar_id,
            tandabuktibayar_t.ruangan_id,
            tandabuktibayar_t.bayaruangmuka_id,
            tandabuktibayar_t.closingkasir_id,
            tandabuktibayar_t.pembayaranpelayanan_id,
            tandabuktibayar_t.shift_id,
            tandabuktibayar_t.nourutkasir,
            tandabuktibayar_t.nobuktibayar,
            tandabuktibayar_t.tglbuktibayar,
            tandabuktibayar_t.uangditerima,
            tandabuktibayar_t.pegawai1_id,
            pembayaranpelayanan_t.no_pembayaran,
                CASE
                    WHEN tandabuktibayar_t.bayaruangmuka_id IS NOT NULL THEN bayaruangmuka_t.pendaftaran_id
                    WHEN tandabuktibayar_t.pembayaranpelayanan_id IS NOT NULL THEN pembayaranpelayanan_t.pendaftaran_id
                    ELSE NULL::integer
                END AS pendaftaran_id,
            pembayaranpelayanan_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pembayaranpelayanan_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            tandabuktibayar_t.jmlpembayaran,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaran_penjamin.total_penjamin,
            pembayaran_penjamin.total_nontunai,
                CASE
                    WHEN pembayaran_penjamin.total_tunai < 0::double precision THEN 0::double precision
                    ELSE pembayaran_penjamin.total_tunai
                END AS total_tunai,
            pembayaran_penjamin.total_tagihan,
            pembayaran_penjamin.pembayaran_id
           FROM tandabuktibayar_t
             LEFT JOIN bayaruangmuka_t ON bayaruangmuka_t.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id
             LEFT JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN ( SELECT COALESCE(pembayaran_t.total_dijamin, 0::double precision) + COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_penjamin,
                    pembayaran_t.pembayaran_id,
                    total_pembayaran.total_nontunai,
                    COALESCE(pembayaran_t.total_dibayar, 0::double precision) - COALESCE(total_pembayaran.total_nontunai, 0::double precision) AS total_tunai,
                    pembayaran_t.total_tagihan + pembayaran_t.total_administrasi AS total_tagihan
                   FROM pembayaran_t
                     LEFT JOIN pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                     LEFT JOIN ( SELECT sum(pembayaranmetode_t.total_dibayar) AS total_nontunai,
                            pembayaranmetode_t.pembayaran_id
                           FROM pembayaranmetode_t
                          WHERE pembayaranmetode_t.is_deleted = false
                          GROUP BY pembayaranmetode_t.pembayaran_id) total_pembayaran ON total_pembayaran.pembayaran_id = pembayaran_t.pembayaran_id
                  WHERE pembayaran_t.is_deleted = false) pembayaran_penjamin ON pembayaran_penjamin.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
             LEFT JOIN carabayar_m carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
             LEFT JOIN penjamin_m penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
          WHERE tandabuktibayar_t.is_deleted = false) agr_bukti_bayar
     LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = agr_bukti_bayar.pendaftaran_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN penjualanresep_t ON penjualanresep_t.penjualanresep_id = agr_bukti_bayar.penjualanresep_id;");

        $this->execute('ALTER TABLE public.closing_kasir_view
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.tariftindakanruangan_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.tariftindakanruangan_v AS 
 SELECT kategoritindakan_m.kategoritindakan_id,
    kategoritindakan_m.kategoritindakan_nama,
    kelompoktindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    daftartindakan_m.daftartindakan_kode,
    daftartindakan_m.daftartindakan_nama,
    daftartindakan_m.daftartindakan_namalainnya,
    daftartindakan_m.daftartindakan_katakunci,
    perdatarif_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    perdatarif_m.perda_no,
    perdatarif_m.perda_tgl,
    perdatarif_m.perda_tentang,
    perdatarif_m.ditetapkan_oleh,
    perdatarif_m.tempat_ditetapkan,
    jenistarif_m.jenistarif_id,
    jenistarif_m.jenistarif_nama,
    tariftindakan_m.tariftindakan_id,
    komponentarif_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.hargadiskon_tindakan,
    tariftindakan_m.persencyto_tindakan,
    jeniskelas_m.jeniskelas_id,
    jeniskelas_m.jeniskelas_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kelaspelayanan_m.kelaspelayanan_namalainnya,
    daftartindakan_m.daftartindakan_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama
   FROM daftartindakan_m
     JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     JOIN tindakanruangan_mp ON daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
     JOIN tariftindakan_m ON daftartindakan_m.daftartindakan_id = tariftindakan_m.daftartindakan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     LEFT JOIN jenistarif_m ON tariftindakan_m.jenistarif_id = jenistarif_m.jenistarif_id
     LEFT JOIN jenistarifpenjamin_mp ON jenistarif_m.jenistarif_id = jenistarifpenjamin_mp.jenistarif_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskelas_m ON kelaspelayanan_m.jeniskelas_id = jeniskelas_m.jeniskelas_id
     LEFT JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE tariftindakan_m.komponentarif_id = 6 AND perdatarif_m.is_active = true AND daftartindakan_m.is_active = true AND daftartindakan_m.is_deleted = false AND tariftindakan_m.is_deleted = false
  GROUP BY kategoritindakan_m.kategoritindakan_id, kategoritindakan_m.kategoritindakan_nama, kelompoktindakan_m.kelompoktindakan_id, kelompoktindakan_m.kelompoktindakan_nama, daftartindakan_m.daftartindakan_kode, daftartindakan_m.daftartindakan_nama, daftartindakan_m.daftartindakan_namalainnya, daftartindakan_m.daftartindakan_katakunci, perdatarif_m.perdatarif_id, perdatarif_m.perdanama_sk, perdatarif_m.perda_no, perdatarif_m.perda_tgl, perdatarif_m.perda_tentang, perdatarif_m.ditetapkan_oleh, perdatarif_m.tempat_ditetapkan, jenistarif_m.jenistarif_id, jenistarif_m.jenistarif_nama, tariftindakan_m.tariftindakan_id, komponentarif_m.komponentarif_id, komponentarif_m.komponentarif_nama, tariftindakan_m.harga_tariftindakan, tariftindakan_m.persendiskon_tindakan, tariftindakan_m.hargadiskon_tindakan, tariftindakan_m.persencyto_tindakan, jeniskelas_m.jeniskelas_id, jeniskelas_m.jeniskelas_nama, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, kelaspelayanan_m.kelaspelayanan_namalainnya, daftartindakan_m.daftartindakan_id, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, penjamin_m.penjamin_id, penjamin_m.penjamin_nama;
");

        $this->execute('ALTER TABLE public.tariftindakanruangan_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.paketpelayananmp_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.paketpelayananmp_v AS 
 SELECT paketpelayanan_mp.tipepaket_id,
        CASE
            WHEN paketpelayanan_mp.daftartindakan_id IS NOT NULL THEN paketpelayanan_mp.daftartindakan_id
            ELSE paketpelayanan_mp.paketdetail_id
        END AS tindakan_paket_id,
        CASE
            WHEN paketpelayanan_mp.daftartindakan_id IS NOT NULL THEN daftartindakan_m.daftartindakan_nama
            ELSE tipepaket_m.tipepaket_nama
        END AS tindakan_paket_nama,
        CASE
            WHEN paketpelayanan_mp.daftartindakan_id IS NOT NULL THEN true
            ELSE false
        END AS is_tindakan,
    paketpelayanan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    parent.is_mcu
   FROM paketpelayanan_mp
     LEFT JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON paketpelayanan_mp.paketdetail_id = tipepaket_m.tipepaket_id
     LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN ruangan_m ON paketpelayanan_mp.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
     LEFT JOIN tipepaket_m parent ON paketpelayanan_mp.tipepaket_id = parent.tipepaket_id
  WHERE paketpelayanan_mp.is_deleted = false;");

        $this->execute('ALTER TABLE public.paketpelayananmp_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.inforiwayatresep_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.inforiwayatresep_v AS 
 SELECT obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    obatalkespasien_t.tglpelayanan AS tgl_transaksi,
    penjualanresep_t.noresep AS no_resep,
    signaobat_m.signa_id,
    signaobat_m.signa_nama,
    obatalkespasien_t.qty_oa AS qty,
    satuan_kecil.satuanunit_id,
    satuan_kecil.satuanunit_nama,
    instalasi_m.instalasi_nama,
    ruangan_tujuan.ruangan_nama
   FROM obatalkespasien_t
     JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.reseptur_id IS NULL
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ruangan_m ruangan_tujuan ON penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_tujuan.instalasi_id = instalasi_m.instalasi_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true AND obatalkespasien_t.obatsudahbayar_id IS NOT NULL;
");

        $this->execute('ALTER TABLE public.inforiwayatresep_v
  OWNER TO postgres;
');

    
  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200218_025129_migrate_20200218 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200218_025129_migrate_20200218 cannot be reverted.\n";

        return false;
    }
    */
}
