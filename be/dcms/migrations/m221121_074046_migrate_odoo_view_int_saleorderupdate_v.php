<?php

use yii\db\Migration;

/**
 * Class m221121_074046_migrate_odoo_view_int_saleorderupdate_v
 */
class m221121_074046_migrate_odoo_view_int_saleorderupdate_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_saleorderupdate_v;
        ');

        $this->execute('
            CREATE VIEW "public"."int_saleorderupdate_v" AS  SELECT pembayaran_r.id, 
    pendaftaran_t.pendaftaran_id::character varying AS sync_id_api,
    pendaftaran_t.no_pendaftaran AS name,
        CASE
            WHEN pembayaran.is_deleted = false THEN pembayaran.no_pembayaran
            ELSE \'-\'::character varying
        END AS billno,
        CASE
            WHEN pembayaran.is_deleted = false THEN pembayaran.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    pendaftaran_t.pasien_id::character varying AS partner_id,
    pendaftaran_t.tgl_pendaftaran AS date_order,
        CASE
            WHEN pendaftaran_t.is_aps = true AND pendaftaran_t.instalasi_id <> 21 THEN \'1\'::text
            WHEN pendaftaran_t.instalasi_id = 1 THEN \'1\'::text
            WHEN pendaftaran_t.instalasi_id = 3 THEN \'2\'::text
            WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NOT NULL THEN \'2\'::text
            WHEN pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.pasienadmisi_id IS NULL THEN \'3\'::text
            WHEN pendaftaran_t.instalasi_id = 6 THEN \'6\'::text
            WHEN pendaftaran_t.instalasi_id = 21 THEN \'5\'::text
            ELSE \'4\'::text
        END AS patient_type,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN concat(\'PEN\', pendaftaran_t.penjamin_id)
            ELSE concat(\'PEN\', pasienadmisi_t.penjamin_id)
        END AS payer_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(p1.penjamin_kode, \'-\'::character varying)
            ELSE COALESCE(p2.penjamin_kode, \'-\'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), \'-\'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), \'-\'::character varying)
        END AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaran.is_deleted = true THEN \'draft\'::text
            ELSE \'done\'::text
        END AS state,
    COALESCE(pembayaran.personal_amount, 0::double precision) AS personal_amount,
    COALESCE(pembayaran.total_tagihan - COALESCE(pembayaranpelayanan_t.subpayer_amount, 0::double precision) - pembayaran.personal_amount, 0::double precision) AS payer_amount,
        CASE
            WHEN pembayaran.is_deleted = true THEN 0::double precision
            ELSE COALESCE(pembayaran.total_tagihan, 0::double precision)
        END AS total_amount,
    pembayaran_r.is_update,
    pembayaranpelayanan_t.subpayer_amount,
    NULL::text AS tariff_id,
    concat(\'PEG\', pendaftaran_t.pegawai_id) AS primary_doc_id,
    concat(\'REF\', rujukan_t.perujuk_id) AS referral_doc_id,
    rujukan_t.no_rujukan AS referral_number,
    NULL::text AS cob_bill,
    NULL::text AS cob_billno,
    NULL::text AS cob_sequence,
    false AS is_bpjs,
    NULL::text AS inacbgs_code,
    NULL::text AS inacbgs_amount
   FROM pembayaran_r
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.penjamin_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran,
            a.pasien_id,
            a.instalasi_id,
            a.is_aps,
            a.pegawai_id,
            a.rujukan_id
           FROM pendaftaran_t a) pendaftaran_t ON pembayaran_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.id,
            a.pendaftaran_id
           FROM pendaftaran_r a
          WHERE a.is_sent = true AND a.keterangan::text = \'INSERT\'::text) pendaftaran_r ON pendaftaran_t.pendaftaran_id = pendaftaran_r.pendaftaran_id
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.penjamin_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_kode
           FROM penjamin_m a) p1 ON pendaftaran_t.penjamin_id = p1.penjamin_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_kode
           FROM penjamin_m a) p2 ON pasienadmisi_t.penjamin_id = p2.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.groupcarabayar_id
           FROM carabayar_m a) cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.groupcarabayar_id
           FROM carabayar_m a) cb2 ON p2.carabayar_id = cb2.carabayar_id
     LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pendaftaran_id,
            a.no_pembayaran,
            a.created_date AS tgl_pembayaran,
            a.is_deleted,
            a.pembayaran_id,
            sum(a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - a.total_discountpembayaran - a.total_discount) AS total_tagihan,
            sum(a.total_tunai + a.total_nontunai + a.total_sisatagihan - a.total_kembalian + a.penggunaan_uangmuka) AS personal_amount,
            sum(a.total_dijamin) AS payer_amount
           FROM pembayaran_t a
          WHERE a.is_deleted = false
          GROUP BY a.pendaftaran_id, a.no_pembayaran, a.created_date, a.is_deleted, a.pembayaran_id) pembayaran ON pembayaran_r.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            sum(a.total_subsidiasuransi + a.pembulatan) AS subpayer_amount
           FROM pembayaranpelayanan_t a
          WHERE a.is_deleted = false AND a.is_penjaminutama = false
          GROUP BY a.pembayaran_id) pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.pembulatan
           FROM pembayaranpelayanan_t a
          WHERE a.is_deleted = false AND a.is_penjaminutama = true) payer_amount ON pembayaran_r.pembayaran_id = payer_amount.pembayaran_id
     LEFT JOIN ( SELECT a.rujukan_id,
            a.no_rujukan,
            a.rujukandari_id AS perujuk_id
           FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
  WHERE pembayaran_r.is_update = false
UNION ALL
 SELECT pembayaran_r.id,
    concat(\'RSPB\', penjualanresep_t.penjualanresep_id) AS sync_id_api,
    penjualanresep_t.noresep AS name,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN \'-\'::character varying
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.no_pembayaran
            ELSE \'-\'::character varying
        END AS billno,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN NULL::timestamp without time zone
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaranpelayanan.tgl_pembayaran
            ELSE NULL::timestamp without time zone
        END AS confirmation_date,
    penjualanresep_t.partner_id,
    penjualanresep_t.tglresep AS date_order,
    \'1\'::text AS patient_type,
    concat(\'PEN\', penjualanresep_t.penjamin_id) AS payer_id,
    COALESCE(penjamin_m.penjamin_kode, \'-\'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), \'-\'::character varying) AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN \'draft\'::text
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN \'done\'::text
            ELSE \'draft\'::text
        END AS state,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaran.total_tunai + pembayaran.total_nontunai + pembayaran.total_sisatagihan - pembayaran.total_kembalian + pembayaran.penggunaan_uangmuka
            ELSE 0::double precision
        END AS personal_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaran.total_dijamin
            ELSE 0::double precision
        END AS payer_amount,
        CASE
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = true THEN 0::double precision
            WHEN pembayaranpelayanan.pembayaranpelayanan_id IS NOT NULL AND pembayaranpelayanan.is_deleted = false THEN pembayaran.total_tagihan
            ELSE 0::double precision
        END AS total_amount,
    pembayaran_r.is_update,
    0 AS subpayer_amount,
    NULL::text AS tariff_id,
    NULL::text AS primary_doc_id,
    NULL::text AS referral_doc_id,
    NULL::text AS referral_number,
    NULL::text AS cob_bill,
    NULL::text AS cob_billno,
    NULL::text AS cob_sequence,
    false AS is_bpjs,
    NULL::text AS inacbgs_code,
    NULL::text AS inacbgs_amount
   FROM pembayaran_r
     JOIN ( SELECT pembayaranpelayanan_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.penjualanresep_id,
            pembayaranpelayanan_t.no_pembayaran,
            pembayaranpelayanan_t.tgl_pembayaran,
            pembayaranpelayanan_t.is_deleted,
            pembayaranpelayanan_t.pembayaran_id
           FROM pembayaranpelayanan_t
             JOIN ( SELECT max(pembayaranpelayanan_t_1.pembayaranpelayanan_id) AS pembayaranpelayanan_id,
                    pembayaranpelayanan_t_1.penjualanresep_id,
                    pembayaranpelayanan_t_1.pembayaran_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  GROUP BY pembayaranpelayanan_t_1.penjualanresep_id, pembayaranpelayanan_t_1.pembayaran_id) max_pembayaran ON pembayaranpelayanan_t.penjualanresep_id = max_pembayaran.penjualanresep_id AND pembayaranpelayanan_t.pembayaranpelayanan_id = max_pembayaran.pembayaranpelayanan_id AND pembayaranpelayanan_t.pembayaran_id = max_pembayaran.pembayaran_id) pembayaranpelayanan ON pembayaran_r.pembayaran_id = pembayaranpelayanan.pembayaran_id
     LEFT JOIN ( SELECT b.penjualanresep_id,
            b.jenispenjualan,
            b.penjamin_id,
            b.noresep,
            b.tglresep,
                CASE
                    WHEN b.jenispenjualan::text = \'343\'::text THEN 0::character varying
                    ELSE concat(\'PEG\', b.karyawan_id)::character varying
                END AS partner_id
           FROM penjualanresep_t b) penjualanresep_t ON pembayaranpelayanan.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN ( SELECT penjualanresep_r_1.id,
            penjualanresep_r_1.penjualanresep_id
           FROM penjualanresep_r penjualanresep_r_1
             JOIN ( SELECT max(penjualanresep_r_2.id) AS id,
                    penjualanresep_r_2.penjualanresep_id
                   FROM penjualanresep_r penjualanresep_r_2
                  WHERE penjualanresep_r_2.keterangan::text = \'INSERT\'::text
                  GROUP BY penjualanresep_r_2.penjualanresep_id) max ON penjualanresep_r_1.id = max.id
          WHERE penjualanresep_r_1.is_sent = true) penjualanresep_r ON penjualanresep_t.penjualanresep_id = penjualanresep_r.penjualanresep_id
     LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan + pembayaran_t.pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan,
            pembayaran_t.total_tunai,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_kembalian,
            pembayaran_t.total_dijamin,
            pembayaran_t.penggunaan_uangmuka,
            pembayaran_t.total_sisatagihan
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_kembalian, pembayaran_t.total_dijamin, pembayaran_t.total_sisatagihan) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
  WHERE pembayaran_r.is_update = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221121_074046_migrate_odoo_view_int_saleorderupdate_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221121_074046_migrate_odoo_view_int_saleorderupdate_v cannot be reverted.\n";

        return false;
    }
    */
}
