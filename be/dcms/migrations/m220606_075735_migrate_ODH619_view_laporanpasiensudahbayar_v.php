<?php

use yii\db\Migration;

/**
 * Class m220606_075735_migrate_ODH619_view_laporanpasiensudahbayar_v
 */
class m220606_075735_migrate_ODH619_view_laporanpasiensudahbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanpasiensudahbayar_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanpasiensudahbayar_v" AS  SELECT pendaftaran_t.pendaftaran_id,
                pembayaranpelayanan_t.pembayaranpelayanan_id,
                pembayaranpelayanan_t.tgl_pembayaran,
                pembayaranpelayanan_t.no_pembayaran,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_nama,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                jk.lookup_name AS jeniskelamin,
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_id,
                penjamin_m.penjamin_nama,
                status_pembayaran.lookup_name AS status_bayar,
                pendaftaran_t.instalasi_id,
                instalasi_m.instalasi_nama,
                tandabuktibayar_t.closingkasir_id,
                pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
                pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                pembayaranpelayanan_t.total_subsidiasuransi AS subsidi_asuransi,
                pembayaranpelayanan_t.total_bayartindakan AS total_sudah_dibayarkan,
                pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
                pembayaranpelayanan_t.biaya_administrasi,
                pembayaranpelayanan_t.pembulatan,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
                kelaspelayanan_m.kelaspelayanan_nama,
                pembayaranpelayanan_t.total_discount
               FROM pembayaranpelayanan_t
                 JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.tgl_pendaftaran,
                        a.umur,
                        a.instalasi_id,
                        a.ruangan_id,
                        a.pasien_id,
                        a.jeniskasuspenyakit_id,
                        a.pegawai_id,
                        a.kelaspelayanan_id
                       FROM pendaftaran_t a) pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                       FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                 JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien,
                        a.tanggal_lahir,
                        a.jeniskelamin
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN ( SELECT a.jeniskasuspenyakit_id,
                        a.jeniskasuspenyakit_nama
                       FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                 JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama
                       FROM carabayar_m a) carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
                 JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
                 JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                 JOIN ( SELECT a.closingkasir_id,
                        a.pembayaranpelayanan_id
                       FROM tandabuktibayar_t a) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
                 LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) status_pembayaran ON pembayaranpelayanan_t.statusbayar::integer = status_pembayaran.lookup_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
              WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false
            UNION ALL
             SELECT pendaftaran_t.pendaftaran_id,
                pembayaranpelayanan_t.pembayaranpelayanan_id,
                pembayaranpelayanan_t.tgl_pembayaran,
                pembayaran_t.no_pembayaran,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.ruangan_id
                        ELSE pasienadmisi.ruangan_id
                    END AS ruangan_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruangan_rj.ruangan_nama
                        ELSE ruangan_ri.ruangan_nama
                    END AS ruangan_nama,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                jk.lookup_name AS jeniskelamin,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.carabayar_id
                        ELSE pasienadmisi.carabayar_id
                    END AS carabayar_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_rj.carabayar_nama
                        ELSE carabayar_ri_1.carabayar_nama
                    END AS carabayar_nama,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
                        ELSE pasienadmisi.penjamin_id
                    END AS penjamin_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_rj.penjamin_nama
                        ELSE penjamin_ri_1.penjamin_nama
                    END AS penjamin_nama,
                status_pembayaran.lookup_name AS status_bayar,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
                        ELSE ruangan_ri.instalasi_id
                    END AS instalasi_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN instalasi_rj.instalasi_nama
                        ELSE instalasi_rj.instalasi_nama
                    END AS instalasi_nama,
                tandabuktibayar_t.closingkasir_id,
                pembayaran_t.total_tagihan,
                pembayaran_t.penggunaan_uangmuka AS total_uang_muka,
                pembayaran_t.total_dijamin AS subsidi_asuransi,
                pembayaran_t.total_dibayar AS total_sudah_dibayarkan,
                pembayaran_t.total_sisatagihan AS total_sisa_tagihan,
                pembayaran_t.total_administrasi AS biaya_administrasi,
                pembayaran_t.total_pembulatan AS pembulatan,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
                kelaspelayanan_m.kelaspelayanan_nama,
                pembayaran_t.total_discountpembayaran AS total_discount
               FROM pembayaranpelayanan_t
                 JOIN ( SELECT a.pembayaran_id,
                        a.no_pembayaran,
                        a.total_tagihan,
                        a.penggunaan_uangmuka,
                        a.total_dijamin,
                        a.total_dibayar,
                        a.total_sisatagihan,
                        a.total_administrasi,
                        a.total_pembulatan,
                        a.total_discountpembayaran
                       FROM pembayaran_t a) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
                 JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.tgl_pendaftaran,
                        a.umur,
                        a.instalasi_id,
                        a.ruangan_id,
                        a.pasien_id,
                        a.jeniskasuspenyakit_id,
                        a.pegawai_id,
                        a.kelaspelayanan_id,
                        a.pasienadmisi_id,
                        a.penjamin_id,
                        a.carabayar_id
                       FROM pendaftaran_t a) pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.penjamin_id,
                        a.carabayar_id,
                        a.ruangan_id
                       FROM pasienadmisi_t a) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                       FROM ruangan_m a) ruangan_rj ON pendaftaran_t.ruangan_id = ruangan_rj.ruangan_id
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a) ruangan_ri ON pasienadmisi.ruangan_id = ruangan_ri.ruangan_id
                 LEFT JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_rj ON pendaftaran_t.instalasi_id = instalasi_rj.instalasi_id
                 LEFT JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_ri ON ruangan_ri.instalasi_id = instalasi_ri.instalasi_id
                 JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien,
                        a.tanggal_lahir,
                        a.jeniskelamin
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN ( SELECT a.jeniskasuspenyakit_id,
                        a.jeniskasuspenyakit_nama
                       FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_ri_1 ON pasienadmisi.penjamin_id = penjamin_ri_1.penjamin_id
                 LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama,
                        a.groupcarabayar_id
                       FROM carabayar_m a) carabayar_rj ON pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id
                 LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama,
                        a.groupcarabayar_id
                       FROM carabayar_m a) carabayar_ri_1 ON pasienadmisi.carabayar_id = carabayar_ri_1.carabayar_id
                 JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                 JOIN ( SELECT a.closingkasir_id,
                        a.pembayaran_id
                       FROM tandabuktibayar_t a) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
                 LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) status_pembayaran ON pembayaranpelayanan_t.statusbayar::integer = status_pembayaran.lookup_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
              WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false
            UNION ALL
             SELECT penjualanresep_t.penjualanresep_id AS pendaftaran_id,
                pembayaranpelayanan_t.pembayaranpelayanan_id,
                pembayaranpelayanan_t.tgl_pembayaran,
                pembayaranpelayanan_t.no_pembayaran,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_nama,
                penjualanresep_t.noresep AS no_pendaftaran,
                penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
                pasien_m.no_rekam_medik,
                COALESCE(pasien_m.nama_pasien, penjualanresep_t.nama_pembeli) AS nama_pasien,
                pasien_m.tanggal_lahir,
                NULL::character varying AS umur,
                jk.lookup_name AS jeniskelamin, 
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_id,
                penjamin_m.penjamin_nama,
                status_pembayaran.lookup_name AS status_bayar,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama,
                tandabuktibayar_t.closingkasir_id,
                round(pembayaranpelayanan_t.total_biayapelayanan) AS total_tagihan,
                round(pembayaranpelayanan_t.penggunaan_uangmuka) AS total_uang_muka,
                round(pembayaranpelayanan_t.total_subsidiasuransi) AS subsidi_asuransi,
                round(pembayaranpelayanan_t.total_bayartindakan) AS total_sudah_dibayarkan,
                round(pembayaranpelayanan_t.total_sisatagihan) AS total_sisa_tagihan,
                round(pembayaranpelayanan_t.biaya_administrasi) AS biaya_administrasi,
                round(pembayaranpelayanan_t.pembulatan) AS pembulatan,
                NULL::character varying AS jeniskasuspenyakit_nama,
                peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
                NULL::character varying AS kelaspelayanan_nama,
                pembayaranpelayanan_t.total_discount
               FROM pembayaranpelayanan_t
                 JOIN ( SELECT a.penjualanresep_id,
                        a.noresep,
                        a.tglpenjualan,
                        a.ruangan_id,
                        a.pasien_id,
                        a.pegawai_id,
                        a.nama_pembeli
                       FROM penjualanresep_t a) penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                 JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a) ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
                 LEFT JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien,
                        a.tanggal_lahir,
                        a.jeniskelamin
                       FROM pasien_m a) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
                 JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama
                       FROM carabayar_m a) carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
                 JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
                 JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 JOIN ( SELECT a.closingkasir_id,
                        a.pembayaranpelayanan_id
                       FROM tandabuktibayar_t a) tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) peg_rd_rj ON penjualanresep_t.pegawai_id = peg_rd_rj.pegawai_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) status_pembayaran ON pembayaranpelayanan_t.statusbayar::integer = status_pembayaran.lookup_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
              WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220606_075735_migrate_ODH619_view_laporanpasiensudahbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220606_075735_migrate_ODH619_view_laporanpasiensudahbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
