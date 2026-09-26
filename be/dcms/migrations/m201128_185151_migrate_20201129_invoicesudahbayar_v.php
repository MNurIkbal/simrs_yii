<?php

use yii\db\Migration;

/**
 * Class m201128_185151_migrate_20201129_invoicesudahbayar_v
 */
class m201128_185151_migrate_20201129_invoicesudahbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."invoicesudahbayar_v";');

        $this->execute("
            CREATE VIEW \"public\".\"invoicesudahbayar_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    pasien_m.no_rekam_medik,
        CASE
            WHEN pasien_m.nama_pasien IS NULL THEN tagihan.nama_pembeli::character varying
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    dok_1.nama_pegawai AS dok_pendaftaran,
    dok_2.nama_pegawai AS dok_ranap,
    r_1.ruangan_nama AS r_pendaftaran,
    r_2.ruangan_nama AS r_ranap,
    tagihan.carabayar_nama,
    tagihan.penjamin_nama,
    tagihan.total_tagihan::integer AS total_tagihan,
    tagihan.total_uang_muka::integer AS total_uang_muka,
    tagihan.total_sudah_dibayarkan::integer AS total_sudah_dibayarkan,
    tagihan.total_sisatagihan::integer AS total_sisatagihan,
    fgetnamalookup(tagihan.status_bayar) AS status_bayar,
    tagihan.tgl_pendaftaran,
    tagihan.pembayaranpelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.tandabuktibayar_id,
    tagihan.pembulatan::integer AS pembulatan,
    tagihan.biaya_administrasi,
    tagihan.tgl_pembayaran,
    tagihan.no_pembayaran,
        CASE
            WHEN tagihan.pasienadmisi_id IS NULL THEN i_1.instalasi_id
            WHEN tagihan.pasienadmisi_id IS NOT NULL THEN i_2.instalasi_id
            ELSE NULL::integer
        END AS instalasi_id,
    i_1.instalasi_nama AS i_pendaftaran,
    i_2.instalasi_nama AS i_ranap,
    tagihan.total_subsidiasuransi::integer AS total_subsidiasuransi,
    tagihan.penjualanresep_id,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    tagihan.adm_resep,
    tagihan.pembayaran_id,
    pasien_m.alamat_pasien AS alamat,
    COALESCE(tagihan.total_dijamin, 0::double precision) AS total_dijamin,
        CASE
            WHEN tagihan.pasienadmisi_id IS NULL THEN pulang_rj.tglpasienpulang
            ELSE pulang_ri.tglpasienpulang
        END AS tgl_pasienpulang
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
            pasienadmisi_t.pegawai_id AS dok_ranap_id,
            pendaftaran_t.ruangan_id AS r_pendaftaran_id,
            pasienadmisi_t.ruangan_id AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                CASE
                    WHEN pembayaranpelayanan_t.total_bayartindakan <= 0::double precision THEN 0::double precision
                    ELSE pembayaranpelayanan_t.pembulatan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.total_terbayar
                END AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan,
            pendaftaran_t.status_bayar,
            pendaftaran_t.tgl_pendaftaran,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pendaftaran_t.kelaspelayanan_id,
            pembayaranpelayanan_t.tandabuktibayar_id,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.total_subsidiasuransi,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            pendaftaran_t.pasienadmisi_id,
            0 AS adm_resep,
            pembayaranpelayanan_t.pembayaran_id,
            pembayaran_t.total_dijamin,
            pendaftaran_t.pasienpulang_id AS pulangrj_id,
            pasienadmisi_t.pasienpulang_id AS pulangri_id
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
            pasienadmisi_t.pegawai_id AS dok_ranap_id,
            pendaftaran_t.ruangan_id AS r_pendaftaran_id,
            pasienadmisi_t.ruangan_id AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                CASE
                    WHEN pembayaranpelayanan_t.total_bayartindakan <= 0::double precision THEN 0::double precision
                    ELSE pembayaranpelayanan_t.pembulatan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.total_terbayar
                END AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan,
            pendaftaran_t.status_bayar,
            pendaftaran_t.tgl_pendaftaran,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pendaftaran_t.kelaspelayanan_id,
            pembayaranpelayanan_t.tandabuktibayar_id,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.total_subsidiasuransi,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            pendaftaran_t.pasienadmisi_id,
            adm_resep.biaya_administrasi AS adm_resep,
            pembayaranpelayanan_t.pembayaran_id,
            0 AS total_dijamin,
            pendaftaran_t.pasienpulang_id AS pulangrj_id,
            pasienadmisi_t.pasienpulang_id AS pulangri_id
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT penjualanresep_t.pendaftaran_id,
                    sum(penjualanresep_t.biayaadministrasi) AS biaya_administrasi
                   FROM penjualanresep_t
                  GROUP BY penjualanresep_t.pendaftaran_id) adm_resep ON pendaftaran_t.pendaftaran_id = adm_resep.pendaftaran_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            1 AS obatsudahbayar_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
            pasienadmisi_t.pegawai_id AS dok_ranap_id,
            pendaftaran_t.ruangan_id AS r_pendaftaran_id,
            pasienadmisi_t.ruangan_id AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            0 AS total_tagihan,
            bayaruangmuka_t.jumlah_uangmuka AS total_uang_muka,
            0 AS total_sudah_dibayarkan,
            0 AS total_sisatagihan,
            pendaftaran_t.status_bayar,
            pendaftaran_t.tgl_pendaftaran,
            NULL::integer AS pembayaranpelayanan_id,
            pendaftaran_t.kelaspelayanan_id,
            bayaruangmuka_t.tandabuktibayar_id,
            0 AS pembulatan,
            tandabuktibayar_t.biayaadministrasi,
            bayaruangmuka_t.tgl_uangmuka,
            bayaruangmuka_t.no_uangmuka,
            0 AS total_subsidiasuransi,
            0 AS penjualanresep_id,
            NULL::text AS nama_pembeli,
            pendaftaran_t.pasienadmisi_id,
            0 AS adm_resep,
            NULL::integer AS pembayaran_id,
            0 AS total_dijamin,
            pendaftaran_t.pasienpulang_id AS pulangrj_id,
            NULL::integer AS pulangri_id
           FROM bayaruangmuka_t
             JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
             JOIN tandabuktibayar_t ON tandabuktibayar_t.tandabuktibayar_id = bayaruangmuka_t.tandabuktibayar_id
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id,
            penjualanresep_t.pasien_id,
            NULL::integer AS jeniskasuspenyakit_id,
            penjualanresep_t.pegawai_id AS dok_pendaftaran_id,
            NULL::integer AS dok_ranap_id,
            penjualanresep_t.ruangan_id AS r_pendaftaran_id,
            NULL::integer AS r_ranap_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
                CASE
                    WHEN pembayaranpelayanan_t.total_bayartindakan <= 0::double precision THEN 0::double precision
                    ELSE pembayaranpelayanan_t.pembulatan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.total_terbayar
                END AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan,
            penjualanresep_t.status_bayar,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            NULL::integer AS kelaspelayanan_id,
            pembayaranpelayanan_t.tandabuktibayar_id,
            pembayaranpelayanan_t.pembulatan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.total_subsidiasuransi,
            penjualanresep_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            NULL::integer AS pasienadmisi_id,
            penjualanresep_t.biayaadministrasi AS adm_resep,
            NULL::integer AS pembayaran_id,
            0 AS total_dijamin,
            NULL::integer AS pulangrj_id,
            NULL::integer AS pulangri_id
           FROM penjualanresep_t
             JOIN obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
             JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id) tagihan
     LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN jeniskasuspenyakit_m ON tagihan.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_1 ON tagihan.dok_pendaftaran_id = dok_1.pegawai_id
     LEFT JOIN pegawai_m dok_2 ON tagihan.dok_ranap_id = dok_2.pegawai_id
     LEFT JOIN ruangan_m r_1 ON tagihan.r_pendaftaran_id = r_1.ruangan_id
     LEFT JOIN ruangan_m r_2 ON tagihan.r_ranap_id = r_2.ruangan_id
     LEFT JOIN instalasi_m i_1 ON i_1.instalasi_id = r_1.instalasi_id
     LEFT JOIN instalasi_m i_2 ON i_2.instalasi_id = r_2.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienpulang_t pulang_rj ON tagihan.pulangrj_id = pulang_rj.pasienpulang_id
     LEFT JOIN pasienpulang_t pulang_ri ON tagihan.pulangri_id = pulang_ri.pasienpulang_id
  WHERE tagihan.tindakansudahbayar_id IS NOT NULL
  GROUP BY tagihan.pendaftaran_id, tagihan.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, dok_1.nama_pegawai, dok_2.nama_pegawai, r_1.ruangan_nama, r_2.ruangan_nama, tagihan.carabayar_nama, tagihan.penjamin_nama, tagihan.total_tagihan, tagihan.total_uang_muka, tagihan.total_sudah_dibayarkan, tagihan.total_sisatagihan, tagihan.status_bayar, tagihan.tgl_pendaftaran, tagihan.pembayaranpelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tagihan.tandabuktibayar_id, tagihan.pembulatan, tagihan.biaya_administrasi, tagihan.tgl_pembayaran, tagihan.no_pembayaran, i_1.instalasi_nama, i_2.instalasi_nama, tagihan.total_subsidiasuransi, tagihan.penjualanresep_id, tagihan.nama_pembeli, tagihan.pasienadmisi_id, i_1.instalasi_id, i_2.instalasi_id, pasien_m.tanggal_lahir, pasien_m.jeniskelamin, tagihan.adm_resep, tagihan.pembayaran_id, pasien_m.alamat_pasien, tagihan.total_dijamin, pulang_rj.tglpasienpulang, pulang_ri.tglpasienpulang;");
        
        $this->execute('ALTER TABLE "public"."invoicesudahbayar_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201128_185151_migrate_20201129_invoicesudahbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201128_185151_migrate_20201129_invoicesudahbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
