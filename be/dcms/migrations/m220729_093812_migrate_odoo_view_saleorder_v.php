<?php

use yii\db\Migration;

/**
 * Class m220729_093812_migrate_odoo_view_saleorder_v
 */
class m220729_093812_migrate_odoo_view_saleorder_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS saleorder_v;
        ');

        $this->execute('
            CREATE VIEW "public"."saleorder_v" AS  SELECT \'RUMAH_SAKIT\'::text AS jenis,
                pendaftaran_r.id,
                pendaftaran_r.pendaftaran_id::character varying AS sync_id_api,
                pendaftaran_r.no_pendaftaran AS name,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN \'-\'::character varying
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN pembayaran.no_pembayaran
                        ELSE \'-\'::character varying
                    END AS billno,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN NULL::timestamp without time zone
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN pembayaran.tgl_pembayaran
                        ELSE NULL::timestamp without time zone
                    END AS confirmation_date,
                pendaftaran_r.pasien_id::character varying AS partner_id,
                pendaftaran_r.tgl_pendaftaran AS date_order,
                    CASE
                        WHEN pendaftaran_r.is_aps = true AND pendaftaran_r.instalasi_id <> 21 THEN \'1\'::text
                        WHEN pendaftaran_r.instalasi_id = 1 THEN \'1\'::text
                        WHEN pendaftaran_r.instalasi_id = 3 THEN \'2\'::text
                        WHEN pendaftaran_r.instalasi_id = 2 AND pendaftaran_r.pasienadmisi_id IS NOT NULL THEN \'2\'::text
                        WHEN pendaftaran_r.instalasi_id = 2 AND pendaftaran_r.pasienadmisi_id IS NULL THEN \'3\'::text
                        WHEN pendaftaran_r.instalasi_id = 6 THEN \'6\'::text
                        WHEN pendaftaran_r.instalasi_id = 21 THEN \'5\'::text
                        ELSE \'4\'::text
                    END AS patient_type,
                    CASE
                        WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN concat(\'PEN\', pendaftaran_r.penjamin_id)
                        ELSE concat(\'PEN\', pasienadmisi_t.penjamin_id)
                    END AS payer_id,
                    CASE
                        WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN COALESCE(p1.penjamin_kode, \'-\'::character varying)
                        ELSE COALESCE(p2.penjamin_kode, \'-\'::character varying)
                    END AS payer_code,
                    CASE
                        WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), \'-\'::character varying)
                        ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), \'-\'::character varying)
                    END AS payer_type,
                6 AS sync_type,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN \'draft\'::text
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN \'done\'::text
                        ELSE \'draft\'::text
                    END AS state,
                pendaftaran_r.keterangan,
                pendaftaran_r.is_sending,
                pendaftaran_r.is_sent,
                    CASE
                        WHEN pendaftaran_r.asuransipasien_id IS NULL THEN COALESCE(asuransipasien_m.namapemilikasuransi, \'-\'::character varying)
                        ELSE COALESCE(asuransipasien_m.namapemilikasuransi, \'-\'::character varying)
                    END AS nama_asuransi,
                    CASE
                        WHEN pendaftaran_r.asuransipasien_id IS NULL THEN COALESCE(asuransipasien_m.nokartuasuransi, \'-\'::character varying)
                        ELSE COALESCE(asuransipasien_m.nokartuasuransi, \'-\'::character varying)
                    END AS no_asuransi,
                    CASE
                        WHEN pendaftaran_r.is_sending = true AND pendaftaran_r.is_sent = false AND pendaftaran_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN pendaftaran_r.is_sending = true AND pendaftaran_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN pendaftaran_r.is_sending = false AND pendaftaran_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN pendaftaran_r.is_sending = true AND pendaftaran_r.is_sent = false AND pendaftaran_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
                        WHEN pendaftaran_r.is_sending = false AND pendaftaran_r.is_sent = false AND pendaftaran_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        ELSE NULL::text
                    END AS status_proses,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.personal_amount, 0::double precision)
                        ELSE 0::double precision
                    END AS personal_amount,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.total_dijamin, 0::double precision)
                        ELSE 0::double precision
                    END AS payer_amount,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.total_tagihan, 0::double precision)
                        ELSE 0::double precision
                    END AS total_amount,
                pendaftaran_r.sync_response,
                pendaftaran_r.sync_payload,
                pembayaranpelayanan_t.subpayer_amount,
                concat(\'PEG\', pendaftaran_r.pegawai_id) AS primary_doc_id,
                concat(\'REF\', rujukan_t.perujuk_id) AS referral_doc_id,
                rujukan_t.no_rujukan AS referral_number,
                NULL::text AS secondary_diagnosis_code,
                NULL::text AS secondary_diagnosis_name,
                NULL::text AS primary_diagnosis_code,
                NULL::text AS primary_diagnosis_name,
                NULL::text AS repeat_diagnosis_code,
                NULL::text AS repeat_diagnosis_name,
                pendaftaran_r.no_pendaftaran AS ref_admission_no,
                COALESCE(rujukan_t.rujukan, \'DATANG SENDIRI\'::character varying) AS admission_type,
                    CASE
                        WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN pulang_rj.tglpasienpulang
                        ELSE pulang_ri.tglpasienpulang
                    END AS discharge_date,
                    CASE
                        WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN pulang_rj.kondisi
                        ELSE pulang_ri.kondisi
                    END AS discharge_reason,
                NULL::text AS tariff_id,
                    CASE
                        WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN kelas_rj.kelaspelayanan_nama
                        ELSE kelas_ri.kelaspelayanan_nama
                    END AS bed_type_id,
                bpjs_ri.klsrawat AS eligible_bed_type_id,
                kelas_ri.kelaspelayanan_nama AS alloted_bed_type_id
               FROM pendaftaran_r
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.penjamin_id,
                        a.pasienpulang_id,
                        a.kelaspelayanan_id,
                        a.bpjs_id
                       FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_r.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.carabayar_id,
                        a.penjamin_kode
                       FROM penjamin_m a) p1 ON pendaftaran_r.penjamin_id = p1.penjamin_id
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
                 LEFT JOIN ( SELECT a.asuransipasien_id,
                        a.namapemilikasuransi,
                        a.nokartuasuransi
                       FROM asuransipasien_m a) asuransipasien_m ON pendaftaran_r.asuransipasien_id = asuransipasien_m.asuransipasien_id
                 LEFT JOIN ( SELECT DISTINCT ON (a.pendaftaran_id) a.pembayaran_id,
                        a.pendaftaran_id,
                        a.no_pembayaran,
                        a.created_date AS tgl_pembayaran,
                        a.is_deleted,
                        a.total_tunai + a.total_nontunai - a.total_kembalian AS personal_amount,
                        a.total_dijamin + a.total_pembulatan AS total_dijamin,
                        a.total_tagihan + a.total_administrasi + a.total_pembulatan + a.pembulatan - a.total_discountpembayaran - a.total_discount AS total_tagihan
                       FROM pembayaran_t a) pembayaran ON pendaftaran_r.pendaftaran_id = pembayaran.pendaftaran_id
                 LEFT JOIN ( SELECT a.pembayaran_id,
                        sum(a.total_subsidiasuransi) AS subpayer_amount
                       FROM pembayaranpelayanan_t a
                      WHERE a.is_deleted = false AND a.is_penjaminutama = false
                      GROUP BY a.pembayaran_id) pembayaranpelayanan_t ON pembayaran.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
                 LEFT JOIN ( SELECT a.rujukan_id,
                        asalrujukan_m.asalrujukan_nama AS rujukan,
                        a.no_rujukan,
                        a.rujukandari_id AS perujuk_id
                       FROM rujukan_t a
                         JOIN asalrujukan_m ON a.asalrujukan_id = asalrujukan_m.asalrujukan_id) rujukan_t ON pendaftaran_r.rujukan_id = rujukan_t.rujukan_id
                 LEFT JOIN ( SELECT a.pasienpulang_id,
                        a.tglpasienpulang,
                        kondisikeluar_m.kondisikeluar_nama AS kondisi
                       FROM pasienpulang_t a
                         JOIN kondisikeluar_m ON a.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id) pulang_rj ON pendaftaran_r.pasienpulang_id = pulang_rj.pasienpulang_id
                 LEFT JOIN ( SELECT a.pasienpulang_id,
                        a.tglpasienpulang,
                        kondisikeluar_m.kondisikeluar_nama AS kondisi
                       FROM pasienpulang_t a
                         JOIN kondisikeluar_m ON a.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
                 LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelas_rj ON pendaftaran_r.kelaspelayanan_id = kelas_rj.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelas_ri ON pasienadmisi_t.kelaspelayanan_id = kelas_ri.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.bpjs_id,
                        a.klsrawat
                       FROM bpjs_t a) bpjs_ri ON pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id
            UNION ALL
             SELECT
                    CASE
                        WHEN penjualanresep_r.jenispenjualan::text = \'343\'::text THEN \'BEBAS\'::text
                        ELSE \'KARYAWAN\'::text
                    END AS jenis,
                penjualanresep_r.id,
                concat(\'RSPB\', penjualanresep_r.penjualanresep_id) AS sync_id_api,
                penjualanresep_r.noresep AS name,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN \'-\'::character varying
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN pembayaran.no_pembayaran
                        ELSE \'-\'::character varying
                    END AS billno,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN NULL::timestamp without time zone
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN pembayaran.tgl_pembayaran
                        ELSE NULL::timestamp without time zone
                    END AS confirmation_date,
                    CASE
                        WHEN penjualanresep_r.jenispenjualan::text = \'343\'::text THEN 0::character varying
                        WHEN penjualanresep_r.jenispenjualan::text = \'345\'::text THEN concat(\'PEG\', penjualanresep_r.karyawan_id)::character varying
                        ELSE 0::character varying
                    END AS partner_id,
                penjualanresep_r.tglresep AS date_order,
                \'1\'::text AS patient_type,
                concat(\'PEN\', penjualanresep_r.penjamin_id) AS payer_id,
                COALESCE(penjamin_m.penjamin_kode, \'-\'::character varying) AS payer_code,
                COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), \'-\'::character varying) AS payer_type,
                6 AS sync_type,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN \'draft\'::text
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN \'done\'::text
                        ELSE \'draft\'::text
                    END AS state,
                penjualanresep_r.keterangan,
                penjualanresep_r.is_sending,
                penjualanresep_r.is_sent,
                \'-\'::character varying AS nama_asuransi,
                \'-\'::character varying AS no_asuransi,
                    CASE
                        WHEN penjualanresep_r.is_sending = true AND penjualanresep_r.is_sent = false AND penjualanresep_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        WHEN penjualanresep_r.is_sending = true AND penjualanresep_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN penjualanresep_r.is_sending = false AND penjualanresep_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN penjualanresep_r.is_sending = true AND penjualanresep_r.is_sent = false AND penjualanresep_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
                        WHEN penjualanresep_r.is_sending = false AND penjualanresep_r.is_sent = false AND penjualanresep_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        ELSE NULL::text
                    END AS status_proses,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.personal_amount, 0::double precision)
                        ELSE 0::double precision
                    END AS personal_amount,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.total_dijamin, 0::double precision)
                        ELSE 0::double precision
                    END AS payer_amount,
                    CASE
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = true THEN 0::double precision
                        WHEN pembayaran.pembayaran_id IS NOT NULL AND pembayaran.is_deleted = false THEN COALESCE(pembayaran.total_tagihan, 0::double precision)
                        ELSE 0::double precision
                    END AS total_amount,
                penjualanresep_r.sync_response,
                penjualanresep_r.sync_payload,
                0 AS subpayer_amount,
                NULL::text AS primary_doc_id,
                NULL::text AS referral_doc_id,
                NULL::text AS referral_number,
                NULL::text AS secondary_diagnosis_code,
                NULL::text AS secondary_diagnosis_name,
                NULL::text AS primary_diagnosis_code,
                NULL::text AS primary_diagnosis_name,
                NULL::text AS repeat_diagnosis_code,
                NULL::text AS repeat_diagnosis_name,
                NULL::text AS ref_admission_no,
                NULL::text AS admission_type,
                NULL::date AS discharge_date,
                NULL::text AS discharge_reason,
                NULL::text AS tariff_id,
                NULL::text AS bed_type_id,
                NULL::integer AS eligible_bed_type_id,
                NULL::character varying AS alloted_bed_type_id
               FROM penjualanresep_r
                 LEFT JOIN ( SELECT penjamin.penjamin_id,
                        penjamin.carabayar_id,
                        penjamin.penjamin_kode
                       FROM penjamin_m penjamin) penjamin_m ON penjualanresep_r.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN ( SELECT carabayar.carabayar_id,
                        carabayar.groupcarabayar_id
                       FROM carabayar_m carabayar) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                 LEFT JOIN ( SELECT DISTINCT ON (pembayaranpelayanan.penjualanresep_id) pembayaranpelayanan.penjualanresep_id,
                        pembayaran_t.pembayaran_id,
                        pembayaran_t.no_pembayaran,
                        pembayaran_t.created_date AS tgl_pembayaran,
                        pembayaran_t.is_deleted,
                        pembayaran_t.total_tunai + pembayaran_t.total_nontunai - pembayaran_t.total_kembalian AS personal_amount,
                        pembayaran_t.total_dijamin + pembayaran_t.total_pembulatan AS total_dijamin,
                        pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan + pembayaran_t.pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan
                       FROM pembayaran_t
                         JOIN ( SELECT pembayaranpelayanan_t.pendaftaran_id,
                                pembayaranpelayanan_t.penjualanresep_id,
                                pembayaranpelayanan_t.pembayaran_id
                               FROM pembayaranpelayanan_t) pembayaranpelayanan ON pembayaran_t.pembayaran_id = pembayaranpelayanan.pembayaran_id) pembayaran ON penjualanresep_r.penjualanresep_id = pembayaran.penjualanresep_id
              WHERE penjualanresep_r.jenispenjualan::text = ANY (ARRAY[\'343\'::character varying::text, \'345\'::character varying::text]);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_093812_migrate_odoo_view_saleorder_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_093812_migrate_odoo_view_saleorder_v cannot be reverted.\n";

        return false;
    }
    */
}
