<?php

use yii\db\Migration;

/**
 * Class m201124_082801_migrate_20201124_infopasiensudahbayar_v
 */
class m201124_082801_migrate_20201124_infopasiensudahbayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if  exists "public"."infopasiensudahbayar_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasiensudahbayar_v\" AS  SELECT pendaftaran.pendaftaran_id,
    pendaftaran.pembayaranpelayanan_id,
    pendaftaran.tgl_pembayaran,
    pendaftaran.no_pembayaran,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.ruangan_id
            ELSE ruang_ri.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.ruangan_nama
            ELSE ruang_ri.ruangan_nama
        END AS ruangan_nama,
    pendaftaran.no_pendaftaran,
    pendaftaran.tgl_pendaftaran,
    pendaftaran.tgl_pulang,
    pendaftaran.no_rekam_medik,
        CASE
            WHEN pendaftaran.nama_pasien IS NULL THEN pendaftaran.nama_pembeli
            ELSE pendaftaran.nama_pasien
        END AS nama_pasien,
    pendaftaran.tanggal_lahir,
    pendaftaran.umur,
    pendaftaran.jeniskelamin,
    pendaftaran.penjamin_id,
    pendaftaran.penjamin_nama,
    pendaftaran.carabayar_id,
    pendaftaran.carabayar_nama,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
        END AS carabayar_nama1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
        END AS penjamin_nama1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.instalasi_id
            ELSE ruang_ri.instalasi_id
        END AS instalasi_id1,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NULL THEN pendaftaran.instalasi_nama
            ELSE ins_ri.instalasi_nama
        END AS instalasi_nama,
    pendaftaran.status_bayar,
    pendaftaran.closingkasir_id,
    pendaftaran.total_tagihan::integer AS total_tagihan,
    pendaftaran.total_uang_muka::integer AS total_uang_muka,
    pendaftaran.subsidi_asuransi::integer AS subsidi_asuransi,
    pendaftaran.total_sudah_dibayarkan::integer AS total_sudah_dibayarkan,
    pendaftaran.total_sisa_tagihan::integer AS total_sisa_tagihan,
    pendaftaran.biaya_administrasi::integer AS biaya_administrasi,
    pendaftaran.pembulatan,
    pendaftaran.jeniskasuspenyakit_nama,
    pendaftaran.pegawai_rd_rj,
    pendaftaran.kelaspelayanan_id,
    pendaftaran.kelaspelayanan_nama,
    pendaftaran.penjualanresep_id,
    pendaftaran.pembayaran_id,
    pendaftaran.total_ditagihkan,
    pendaftaran.returbayarpelayanan_id,
    pendaftaran.pagawaikasir_id,
    pasienadmisi_t.is_stoptitipan,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    pendaftaran.kelas_ditagihkan_nama,
    pembayaran.tagihan,
    pembayaran.total_dibayar,
    pembayaran.total_dijamin
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.tglpasienpulang
                    ELSE pulang_ri.tglpasienpulang
                END AS tgl_pulang,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
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
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_pendaftaran.kelaspelayanan_id
                    ELSE kelas_admisi.kelaspelayanan_id
                END AS kelaspelayanan_id,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelas_pendaftaran.kelaspelayanan_nama
                    ELSE kelas_admisi.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            NULL::integer AS penjualanresep_id,
            NULL::character varying AS nama_pembeli,
            pembayaranpelayanan_t.pembayaran_id,
            pembayaran_t.total_ditagihkan,
            tandabuktibayar_t.returbayarpelayanan_id,
            loginpemakai_k.pegawai_id AS pagawaikasir_id,
            kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama
           FROM pembayaranpelayanan_t
             JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN pasienadmisi_t pasienadmisi_t_1 ON pembayaranpelayanan_t.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN pegawai_m peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
             LEFT JOIN kelaspelayanan_m kelas_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelas_pendaftaran.kelaspelayanan_id
             LEFT JOIN kelaspelayanan_m kelas_admisi ON pasienadmisi_t_1.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id
             LEFT JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             LEFT JOIN loginpemakai_k ON pembayaranpelayanan_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON pasienadmisi_t_1.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
             LEFT JOIN pasienpulang_t pulang_rj ON pendaftaran_t.pasienpulang_id = pulang_rj.pasienpulang_id
             LEFT JOIN pasienpulang_t pulang_ri ON pasienadmisi_t_1.pasienpulang_id = pulang_ri.pasienpulang_id
          WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.no_pembayaran,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglresep AS tgl_pendaftaran,
            NULL::timestamp without time zone AS tgl_pulang,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            NULL::character varying AS umur,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer) AS status_bayar,
            NULL::integer AS instalasi_id,
            instalasi_m.instalasi_nama,
            tandabuktibayar_t.closingkasir_id,
            pembayaranpelayanan_t.total_biayapelayanan AS total_tagihan,
            pembayaranpelayanan_t.penggunaan_uangmuka AS total_uang_muka,
            pembayaranpelayanan_t.total_subsidiasuransi AS subsidi_asuransi,
            pembayaranpelayanan_t.total_bayartindakan AS total_sudah_dibayarkan,
            pembayaranpelayanan_t.total_sisatagihan AS total_sisa_tagihan,
            pembayaranpelayanan_t.biaya_administrasi,
            pembayaranpelayanan_t.pembulatan,
            NULL::character varying AS jeniskasuspenyakit_nama,
            peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
            NULL::integer AS kelaspelayanan_id,
            NULL::character varying AS kelaspelayanan_nama,
            pembayaranpelayanan_t.penjualanresep_id,
            penjualanresep_t.nama_pembeli,
            pembayaranpelayanan_t.pembayaran_id,
            pembayaran_t.total_ditagihkan,
            tandabuktibayar_t.returbayarpelayanan_id,
            loginpemakai_k.pegawai_id AS pagawaikasir_id,
            NULL::character varying AS kelas_ditagihkan_nama
           FROM pembayaranpelayanan_t
             JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
             LEFT JOIN pegawai_m peg_rd_rj ON penjualanresep_t.pegawai_id = peg_rd_rj.pegawai_id
             LEFT JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
             LEFT JOIN loginpemakai_k ON pembayaranpelayanan_t.created_by = loginpemakai_k.loginpemakai_id
          WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false) pendaftaran
     LEFT JOIN pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ruangan_m ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
     LEFT JOIN instalasi_m ins_ri ON ruang_ri.instalasi_id = ins_ri.instalasi_id
     LEFT JOIN carabayar_m carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi - pembayaran_t.total_discountpembayaran AS tagihan,
            pembayaran_t.total_tunai + pembayaran_t.total_nontunai + pembayaran_t.total_sisatagihan + pembayaran_t.penggunaan_uangmuka AS total_dibayar,
            pembayaran_t.total_dijamin + pemberianpiutang_t.total_piutang AS total_dijamin
           FROM pembayaran_t
             LEFT JOIN pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
          WHERE pembayaran_t.is_deleted = false) pembayaran ON pendaftaran.pembayaran_id = pembayaran.pembayaran_id;");

        $this->execute('ALTER TABLE "public"."infopasiensudahbayar_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201124_082801_migrate_20201124_infopasiensudahbayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201124_082801_migrate_20201124_infopasiensudahbayar_v cannot be reverted.\n";

        return false;
    }
    */
}
