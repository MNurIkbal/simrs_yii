<?php

use yii\db\Migration;

/**
 * Class m221021_103820_migrate_MHG3977_view_tandabuktibayar_v
 */
class m221021_103820_migrate_MHG3977_view_tandabuktibayar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."tandabuktibayar_v";
        ');

        $this->execute('
            CREATE VIEW "public"."tandabuktibayar_v" AS  SELECT tandabuktibayar_t.tandabuktibayar_id,
                tandabuktibayar_t.nobuktibayar,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                tandabuktibayar_t.tglbuktibayar,
                pembayaran_t.total_ditagihkan AS jmlpembayaran,
                pembayaranpelayanan_t.no_pembayaran,
                carabayar_m.carabayar_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.instalasi_nama
                        ELSE pulang_ri.instalasi_nama
                    END AS instalasi_akhir,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.ruangan_nama
                        ELSE pulang_ri.ruangan_nama
                    END AS ruangan_akhir,
                pendaftaran_t.tgl_pendaftaran,
                pasien_m.tanggal_lahir AS tgl_lahir,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.tglpasienpulang
                        WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pulang_ri.tglpasienpulang
                        ELSE pendaftaran_t.tgl_stopakomodasi
                    END AS tgl_keluar,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_rj.klsrawat
                        ELSE bpjs_ri.klsrawat
                    END AS hak_kelas,
                    CASE
                        WHEN retur.tandabuktibayar_id IS NULL THEN false
                        ELSE true
                    END AS is_returbayarpelayanan
               FROM tandabuktibayar_t
                 JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                 JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id AND pembayaran_t.is_deleted = false
                 JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
                 JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
                 JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN bpjs_t bpjs_rj ON pendaftaran_t.bpjs_id = bpjs_rj.bpjs_id
                 LEFT JOIN bpjs_t bpjs_ri ON pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id
                 LEFT JOIN ( SELECT pasienpulang_t.pasienpulang_id,
                        pasienpulang_t.tglpasienpulang,
                        ruangan_m.ruangan_nama,
                        instalasi_m.instalasi_nama
                       FROM pasienpulang_t
                         JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
                         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pulang_rj ON pendaftaran_t.pasienpulang_id = pulang_rj.pasienpulang_id
                 LEFT JOIN ( SELECT pasienpulang_t.pasienpulang_id,
                        pasienpulang_t.tglpasienpulang,
                        ruangan_m.ruangan_nama,
                        instalasi_m.instalasi_nama
                       FROM pasienpulang_t
                         JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
                         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
                 LEFT JOIN ( SELECT returbayarpelayanan_t.tandabuktibayar_id,
                        sum(returbayarpelayanan_t.total_biayaretur) AS total_retur,
                        tandabuktibayar_t_1.uangditerima - sum(returbayarpelayanan_t.total_biayaretur) AS sisa_pembayaran
                       FROM returbayarpelayanan_t
                         JOIN tandabuktibayar_t tandabuktibayar_t_1 ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t_1.tandabuktibayar_id
                      WHERE returbayarpelayanan_t.is_deleted = false
                      GROUP BY returbayarpelayanan_t.tandabuktibayar_id, tandabuktibayar_t_1.uangditerima) retur ON retur.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
            UNION ALL
             SELECT tandabuktibayar_t.tandabuktibayar_id,
                tandabuktibayar_t.nobuktibayar,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                tandabuktibayar_t.tglbuktibayar,
                pembayaran_t.total_ditagihkan AS jmlpembayaran,
                pembayaran_t.no_pembayaran,
                carabayar_m.carabayar_id,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.instalasi_nama
                        ELSE pulang_ri.instalasi_nama
                    END AS instalasi_akhir,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.ruangan_nama
                        ELSE pulang_ri.ruangan_nama
                    END AS ruangan_akhir,
                pendaftaran_t.tgl_pendaftaran,
                pasien_m.tanggal_lahir AS tgl_lahir,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.tglpasienpulang
                        WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pulang_ri.tglpasienpulang
                        ELSE pendaftaran_t.tgl_stopakomodasi
                    END AS tgl_keluar,
                    CASE
                        WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_rj.klsrawat
                        ELSE bpjs_ri.klsrawat
                    END AS hak_kelas,
                    CASE
                        WHEN retur.tandabuktibayar_id IS NULL THEN false
                        ELSE true
                    END AS is_returbayarpelayanan
               FROM tandabuktibayar_t
                 JOIN pembayaran_t ON tandabuktibayar_t.pembayaran_id = pembayaran_t.pembayaran_id AND pembayaran_t.is_deleted = false
                 JOIN pembayaranpelayanan_t ON pembayaran_t.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
                 JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
                 JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
                 JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN bpjs_t bpjs_rj ON pendaftaran_t.bpjs_id = bpjs_rj.bpjs_id
                 LEFT JOIN bpjs_t bpjs_ri ON pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id
                 LEFT JOIN ( SELECT pasienpulang_t.pasienpulang_id,
                        pasienpulang_t.tglpasienpulang,
                        ruangan_m.ruangan_nama,
                        instalasi_m.instalasi_nama
                       FROM pasienpulang_t
                         JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
                         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pulang_rj ON pendaftaran_t.pasienpulang_id = pulang_rj.pasienpulang_id
                 LEFT JOIN ( SELECT pasienpulang_t.pasienpulang_id,
                        pasienpulang_t.tglpasienpulang,
                        ruangan_m.ruangan_nama,
                        instalasi_m.instalasi_nama
                       FROM pasienpulang_t
                         JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
                         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
                 LEFT JOIN ( SELECT returbayarpelayanan_t.tandabuktibayar_id,
                        sum(returbayarpelayanan_t.total_biayaretur) AS total_retur,
                        tandabuktibayar_t_1.uangditerima - sum(returbayarpelayanan_t.total_biayaretur) AS sisa_pembayaran
                       FROM returbayarpelayanan_t
                         JOIN tandabuktibayar_t tandabuktibayar_t_1 ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t_1.tandabuktibayar_id
                      WHERE returbayarpelayanan_t.is_deleted = false
                      GROUP BY returbayarpelayanan_t.tandabuktibayar_id, tandabuktibayar_t_1.uangditerima) retur ON retur.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
            UNION ALL
             SELECT tandabuktibayar_t.tandabuktibayar_id,
                tandabuktibayar_t.nobuktibayar,
                penjualanresep.noresep AS no_pendaftaran,
                NULL::character varying AS no_rekam_medik,
                penjualanresep.nama_pembeli AS nama_pasien,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                NULL::character varying AS kelaspelayanan_nama,
                tandabuktibayar_t.tglbuktibayar,
                pembayaran_t.total_ditagihkan AS jmlpembayaran,
                pembayaran_t.no_pembayaran,
                carabayar_m.carabayar_id,
                instalasi_m.instalasi_nama AS instalasi_akhir,
                ruangan_m.ruangan_nama AS ruangan_akhir,
                penjualanresep.tglresep AS tgl_pendaftaran,
                NULL::date AS tgl_lahir,
                penjualanresep.tglresep AS tgl_keluar,
                NULL::integer AS hak_kelas,
                    CASE
                        WHEN retur.tandabuktibayar_id IS NULL THEN false
                        ELSE true
                    END AS is_returbayarpelayanan
               FROM pembayaranpelayanan_t
                 JOIN ( SELECT a.pembayaran_id,
                        a.pendaftaran_id,
                        a.total_tagihan,
                        a.total_administrasi,
                        a.total_discount,
                        a.total_discountpembayaran,
                        a.total_tunai,
                        a.total_kembalian,
                        a.total_nontunai,
                        a.total_dijamin,
                        a.no_pembayaran,
                        a.is_deleted,
                        a.pemberianpiutang_id,
                        a.created_by,
                        a.total_dibayar,
                        a.total_pembulatan,
                        a.pembulatan,
                        a.total_ditagihkan
                       FROM pembayaran_t a) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
                 JOIN ( SELECT e.penjualanresep_id,
                        e.penjamin_id,
                        e.pendaftaran_id,
                        e.catatan,
                        e.noresep,
                        e.nama_pembeli,
                        e.ruangan_id,
                        e.tglresep
                       FROM penjualanresep_t e
                      WHERE e.pendaftaran_id IS NULL) penjualanresep ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep.penjualanresep_id
                 JOIN ( SELECT e.pembayaran_id,
                        e.tandabuktibayar_id,
                        e.ruangan_id,
                        e.closingkasir_id,
                        e.shift_id,
                        e.nobuktibayar,
                        e.tglbuktibayar,
                        e.uangditerima
                       FROM tandabuktibayar_t e) tandabuktibayar_t ON pembayaran_t.pembayaran_id = tandabuktibayar_t.pembayaran_id
                 JOIN ( SELECT e.loginpemakai_id,
                        e.pegawai_id
                       FROM loginpemakai_k e) loginpemakai_k ON pembayaran_t.created_by = loginpemakai_k.loginpemakai_id
                 LEFT JOIN ( SELECT e.penjamin_id,
                        e.penjamin_nama,
                        e.carabayar_id
                       FROM penjamin_m e) penjamin_m ON penjualanresep.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN ( SELECT e.carabayar_id,
                        e.carabayar_nama
                       FROM carabayar_m e) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a) ruangan_m ON penjualanresep.ruangan_id = ruangan_m.ruangan_id
                 LEFT JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN ( SELECT e.pemberianpiutang_id,
                        e.total_piutang
                       FROM pemberianpiutang_t e) pemberianpiutang_t ON pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
                 LEFT JOIN ( SELECT returbayarpelayanan_t.tandabuktibayar_id,
                        sum(returbayarpelayanan_t.total_biayaretur) AS total_retur,
                        tandabuktibayar_t_1.uangditerima - sum(returbayarpelayanan_t.total_biayaretur) AS sisa_pembayaran
                       FROM returbayarpelayanan_t
                         JOIN tandabuktibayar_t tandabuktibayar_t_1 ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t_1.tandabuktibayar_id
                      WHERE returbayarpelayanan_t.is_deleted = false
                      GROUP BY returbayarpelayanan_t.tandabuktibayar_id, tandabuktibayar_t_1.uangditerima) retur ON retur.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
            UNION ALL
             SELECT tandabuktibayar_t.tandabuktibayar_id,
                tandabuktibayar_t.nobuktibayar,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama, 
                kelaspelayanan_m.kelaspelayanan_nama,
                tandabuktibayar_t.tglbuktibayar,
                tandabuktibayar_t.jmlpembayaran,
                NULL::character varying AS no_pembayaran,
                carabayar_m.carabayar_id,
                NULL::character varying AS instalasi_akhir,
                NULL::character varying AS ruangan_akhir,
                NULL::timestamp without time zone AS tgl_pendaftaran,
                NULL::date AS tgl_lahir,
                NULL::timestamp without time zone AS tgl_keluar,
                NULL::integer AS hak_kelas,
                NULL::boolean AS is_returbayarpelayanan
               FROM tandabuktibayar_t
                 JOIN ( SELECT a.bayaruangmuka_id,
                        a.pendaftaran_id
                       FROM bayaruangmuka_t a) bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
                 JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.pasien_id,
                        a.carabayar_id,
                        a.penjamin_id,
                        a.kelaspelayanan_id
                       FROM pendaftaran_t a) pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama
                       FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                 JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                 JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221021_103820_migrate_MHG3977_view_tandabuktibayar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221021_103820_migrate_MHG3977_view_tandabuktibayar_v cannot be reverted.\n";

        return false;
    }
    */
}
