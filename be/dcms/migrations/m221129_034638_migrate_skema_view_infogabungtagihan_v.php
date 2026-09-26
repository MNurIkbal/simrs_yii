<?php

use yii\db\Migration;

/**
 * Class m221129_034638_migrate_skema_view_infogabungtagihan_v
 */
class m221129_034638_migrate_skema_view_infogabungtagihan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infogabungtagihan_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infogabungtagihan_v" AS  SELECT gabungpelayanandetail_t.gabungpelayanandetail_id,
    gabungpelayanandetail_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS ref_no_pendaftaran,
    pendaftaran_nontujuan.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran, 
    COALESCE(pulang_ri.tglpasienpulang, pendaftaran_t.tgl_stopakomodasi, pulang_rjrd.tglpasienpulang) AS tgl_pulang,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    gender.lookup_kode AS jenis_kelamin_kode,
    gender.lookup_name AS jenis_kelamin,
    ruangan_m.ruangan_nama AS ruangan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi,
    ref_pendaftaran.ref_penjamin,
    carabayar_m.carabayar_nama AS cara_bayar,
    penjamin_m.penjamin_nama AS penjamin,
    bpjs_t.nosep AS no_sep,
    lkp_statusbayar.lookup_name AS status_bayar,
    pendaftaran_t.status_bayar AS status_bayar_id,
    gabungpelayanandetail_t.created_date AS tgl_gabung,
    gabungpelayanandetail_t.ref_pendaftaran_id,
    pembayaran_t.pembayaran_id,
        CASE
            WHEN COALESCE(pembayaran_t.total_tagihan, 0::double precision) = 0::double precision THEN COALESCE(tagihan.sum_tagihan, 0::double precision) + COALESCE(ref_tagihan.sum_tagihan, 0::double precision)
            ELSE pembayaran_t.total_tagihan
        END AS total_tagihan,
    pembayaran_t.is_deleted AS pembayaran_deleted,
    gabungpelayanandetail_t.is_deleted
   FROM gabungpelayanandetail_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran,
            a.status_bayar,
            a.tgl_stopakomodasi,
            a.pasien_id,
            a.pasienpulang_id,
            a.pasienadmisi_id,
            a.ruangan_id,
            a.penjamin_id
           FROM pendaftaran_t a) pendaftaran_t ON gabungpelayanandetail_t.ref_pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.penjamin_id
           FROM pendaftaran_t a) pendaftaran_nontujuan ON gabungpelayanandetail_t.pendaftaran_id = pendaftaran_nontujuan.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.jeniskelamin
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pasienpulang_id,
            a.bpjs_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a) pulang_rjrd ON pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama,
            a.carabayar_id
           FROM penjamin_m a) penjamin_m ON pendaftaran_nontujuan.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama,
            a.carabayar_id
           FROM penjamin_m a) penjamin_tujuan ON pendaftaran_t.penjamin_id = penjamin_tujuan.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON penjamin_tujuan.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.bpjs_id,
            a.nosep
           FROM bpjs_t a) bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN ( SELECT max(a.pembayaran_id) AS pembayaran_id,
            a.pendaftaran_id
           FROM pembayaran_t a
          GROUP BY a.pendaftaran_id) max_pembayaran_t ON gabungpelayanandetail_t.ref_pendaftaran_id = max_pembayaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.is_deleted,
            a.total_tagihan
           FROM pembayaran_t a) pembayaran_t ON max_pembayaran_t.pembayaran_id = pembayaran_t.pembayaran_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name,
            a.lookup_kode
           FROM lookup_m a) gender ON gender.lookup_id = pasien_m.jeniskelamin::integer
     LEFT JOIN ( SELECT tagihan_1.pendaftaran_id,
            sum(tagihan_1.tagihan) AS sum_tagihan
           FROM ( SELECT a.pendaftaran_id,
                    sum(a.tarif_tindakan) AS tagihan
                   FROM tindakanpelayanan_t a
                  WHERE a.is_deleted = false AND a.is_active = true AND a.tindakansudahbayar_id IS NULL
                  GROUP BY a.pendaftaran_id
                UNION ALL
                 SELECT a.pendaftaran_id,
                    sum(a.hargajual_oa) AS tagihan
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.is_active = true AND a.obatsudahbayar_id IS NULL
                  GROUP BY a.pendaftaran_id) tagihan_1
          GROUP BY tagihan_1.pendaftaran_id) tagihan ON gabungpelayanandetail_t.pendaftaran_id = tagihan.pendaftaran_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            ref_penjamin.penjamin_nama AS ref_penjamin
           FROM pendaftaran_t a
             LEFT JOIN ( SELECT a_1.penjamin_id,
                    a_1.penjamin_nama
                   FROM penjamin_m a_1) ref_penjamin ON a.penjamin_id = ref_penjamin.penjamin_id) ref_pendaftaran ON gabungpelayanandetail_t.ref_pendaftaran_id = ref_pendaftaran.pendaftaran_id
     LEFT JOIN ( SELECT tagihan_1.pendaftaran_id,
            sum(tagihan_1.tagihan) AS sum_tagihan
           FROM ( SELECT a.pendaftaran_id,
                    sum(a.tarif_tindakan) AS tagihan
                   FROM tindakanpelayanan_t a
                  WHERE a.is_deleted = false AND a.is_active = true AND a.tindakansudahbayar_id IS NULL
                  GROUP BY a.pendaftaran_id
                UNION ALL
                 SELECT a.pendaftaran_id,
                    sum(a.hargajual_oa) AS tagihan
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.is_active = true AND a.obatsudahbayar_id IS NULL
                  GROUP BY a.pendaftaran_id) tagihan_1
          GROUP BY tagihan_1.pendaftaran_id) ref_tagihan ON gabungpelayanandetail_t.ref_pendaftaran_id = ref_tagihan.pendaftaran_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lkp_statusbayar ON pendaftaran_t.status_bayar = lkp_statusbayar.lookup_id
  WHERE gabungpelayanandetail_t.is_deleted IS FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221129_034638_migrate_skema_view_infogabungtagihan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_034638_migrate_skema_view_infogabungtagihan_v cannot be reverted.\n";

        return false;
    }
    */
}
