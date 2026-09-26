<?php

use yii\db\Migration;

/**
 * Class m220729_093649_migrate_odoo_view_int_rspb
 */
class m220729_093649_migrate_odoo_view_int_rspb extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS int_rspb;
        ');

        $this->execute('
            CREATE VIEW "public"."int_rspb" AS  SELECT 6 AS sync_type,
                obatalkespasien_r.tgl_proses AS tglproses,
                concat(\'OBT\', obatalkespasien_r.id) AS sync_id_api,
                concat(\'OBT\', obatalkespasien_r.obatalkes_id) AS product_id,
                obatalkes_m.obatalkes_nama AS name,
                obatalkes_m.satuankecil_id AS product_uom,
                    CASE
                        WHEN obatalkespasien_r.det_konversi IS NOT NULL THEN obatalkespasien_r.det_konversi
                        WHEN obatalkespasien_r.qty_konversi IS NOT NULL THEN obatalkespasien_r.qty_konversi
                        ELSE obatalkespasien_r.qty_oa
                    END AS product_uom_qty,
                    CASE
                        WHEN ((obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) IS NOT NULL OR (obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text) = \'\'::text) AND obatalkespasien_r.penjualanresep_id IS NOT NULL THEN obatalkespasien_r.hargasatuan_oa / ((obatalkespasien_r.additional_data::json ->> \'nilai_konversi\'::text)::double precision)
                        ELSE obatalkespasien_r.hargasatuan_oa
                    END AS price_unit,
                obatalkespasien_r.hargajual_oa AS price_subtotal,
                obatalkespasien_r.hargajual_oa AS price_total,
                    CASE
                        WHEN obatalkespasien_r.tarif_dijamin = 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
                        WHEN obatalkespasien_r.tarif_dibayarkan <> 0::double precision AND obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dibayarkan, 0::double precision)
                        ELSE 0::double precision
                    END AS personal_amount,
                    CASE
                        WHEN obatalkespasien_r.tarif_dijamin <> 0::double precision THEN COALESCE(obatalkespasien_r.tarif_dijamin, 0::double precision) + COALESCE(obatalkespasien_r.tarif_diskon, 0::double precision)
                        ELSE 0::double precision
                    END AS payer_amount,
                concat(\'RSPB\', obatalkespasien_r.penjualanresep_id) AS order_id,
                concat(\'CATEG\', jenisobatalkes_m.servicecategory_id) AS service_categ_id,
                concat(\'PEG\', penjualanresep_t.pegawai_id) AS primary_doc_id,
                concat(\'PEG\', penjualanresep_t.pegawai_id) AS prescribe_doc_id,
                NULL::text AS perform_doc_id,
                obatalkespasien_r.ruangan_id::text AS location_id,
                ruangan_m.ruangan_nama AS department_id,
                pembayaranpelayanan_t.no_pembayaran AS billno,
                pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
                obatalkespasien_r.keterangan AS type_line,
                \'LOS\'::text AS revenue_type,
                jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
                \'OPD\'::text AS patient_group,
                NULL::text AS special_group,
                \'PHARMACY OUTPATIENT\'::text AS special_group2,
                servicegroup_m.servicegroup_nama AS service_group,
                COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'GENERAL\'::character varying) AS bed_type,
                concat(\'PEN\', obatalkespasien_r.penjamin_id) AS payer,
                penjamin_m.penjamin_kode AS payer_code,
                carabayar_m.carabayar_nama AS payer_type,
                penjamin_m.penjamin_nama AS payer_name,
                COALESCE(penjualanresep_t.no_resep) AS order_no,
                obatalkespasien_r.tglpelayanan AS order_date,
                false AS is_package,
                NULL::text AS package_name,
                NULL::text AS cost_unit,
                NULL::text AS cost_total,
                concat(\'PEG\', penjualanresep_t.pegawai_id) AS account_analytic_id,
                concat(\'PEG\', penjualanresep_t.pegawai_id) AS backup_analytic_id,
                penjualanresep_r.kota,
                penjualanresep_r.kecamatan,
                penjualanresep_r.kelurahan,
                penjualanresep_r.pasien_id AS partner_id,
                penjualanresep_r.no_rekam_medik AS registration_code,
                penjualanresep_r.noresep AS number_admission,
                \'-\'::text AS manufacture,
                penjualanresep_r.tglpasienpulang::character varying AS discharge_date,
                pegawai.spesialis_nama AS specialization_primary,
                obatalkespasien_r.is_sent,
                obatalkespasien_r.is_sending,
                obatalkespasien_r.id,
                    CASE
                        WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
                        ELSE \'done\'::text
                    END AS status_bill,
                    CASE
                        WHEN obatalkespasien_r.keterangan::text = ANY (ARRAY[\'ACCRUAL\'::character varying::text, \'ACCRUAL REVERSAL\'::character varying::text]) THEN \'draft\'::text
                        ELSE \'done\'::text
                    END AS state,
                    CASE
                        WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = true THEN \'SUKSES\'::text
                        WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false THEN \'MENUNGGU PROSES\'::text
                        WHEN obatalkespasien_r.is_sending = true AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NULL THEN \'DALAM PROSES\'::text
                        WHEN obatalkespasien_r.is_sending = false AND obatalkespasien_r.is_sent = false AND obatalkespasien_r.id_sync_sercon IS NOT NULL THEN \'GAGAL\'::text
                        ELSE NULL::text
                    END AS status_proses,
                penjualanresep_r.nama_pasien,
                penjualanresep_r.tglresep AS admit_date,
                int_billing_r.id::text AS billing_id,
                int_billing_r.is_sent AS is_sent_billing
               FROM obatalkespasien_r
                 JOIN ( SELECT obatalkespasien_t_1.obatalkespasien_id,
                        obatalkespasien_t_1.tarif_dibayarkan,
                        obatalkespasien_t_1.tarif_dijamin,
                        obatalkespasien_t_1.tarif_diskon
                       FROM obatalkespasien_t obatalkespasien_t_1) obatalkespasien_t ON obatalkespasien_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id
                 JOIN ( SELECT penjualanresep_r_1.id,
                        penjualanresep_r_1.penjualanresep_id,
                        penjualanresep_r_1.pegawai_id,
                        penjualanresep_r_1.pasienadmisi_id,
                        0 AS pasien_id,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        penjualanresep_r_1.noresep,
                        kabupaten_m.kabupaten_nama AS kota,
                        kecamatan_m.kecamatan_nama AS kecamatan,
                        kelurahan_m.kelurahan_nama AS kelurahan,
                        penjualanresep_r_1.tglresep AS tglpasienpulang,
                        penjualanresep_r_1.tglresep
                       FROM penjualanresep_r penjualanresep_r_1
                         JOIN ( SELECT max(resep_max.id) AS id,
                                resep_max.penjualanresep_id
                               FROM penjualanresep_r resep_max
                              WHERE resep_max.keterangan::text = \'ACCRUAL\'::text
                              GROUP BY resep_max.penjualanresep_id) max ON penjualanresep_r_1.id = max.id
                         JOIN pasien_m ON 0 = pasien_m.pasien_id
                         LEFT JOIN kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
                         LEFT JOIN kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
                         LEFT JOIN kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
                      WHERE penjualanresep_r_1.is_sent = true) penjualanresep_r ON obatalkespasien_r.penjualanresep_id = penjualanresep_r.penjualanresep_id
                 JOIN obatalkes_m ON obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                 LEFT JOIN servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
                 JOIN ruangan_m ON obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN penjamin_m ON obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                 LEFT JOIN ( SELECT obatalkespasien_r_1.pendaftaran_id,
                        sum(obatalkespasien_r_1.hargajual_oa) AS total_tagihan
                       FROM obatalkespasien_r obatalkespasien_r_1
                      WHERE obatalkespasien_r_1.is_deleted = false
                      GROUP BY obatalkespasien_r_1.pendaftaran_id) total_tagihan ON obatalkespasien_r.pendaftaran_id = total_tagihan.pendaftaran_id
                 LEFT JOIN obatsudahbayar_t ON obatalkespasien_r.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
                 LEFT JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                 LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
                        sum(pembayaran_t.total_dibayar) AS total_dibayar,
                        sum(pembayaran_t.total_dijamin) AS total_dijamin
                       FROM pembayaran_t
                      GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON obatalkespasien_r.pendaftaran_id = pembayaran.pendaftaran_id
                 LEFT JOIN kelaspelayanan_m ON obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                        spesialis_m.spesialis_nama
                       FROM pegawai_m
                         JOIN spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id) pegawai ON obatalkespasien_r.pegawai_id = pegawai.pegawai_id
                 LEFT JOIN ( SELECT penjualanresep_t_1.penjualanresep_id,
                        penjualanresep_t_1.pegawai_id,
                            CASE
                                WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                                ELSE reseptur_t.noresep
                            END AS no_resep
                       FROM penjualanresep_t penjualanresep_t_1
                         LEFT JOIN reseptur_t ON penjualanresep_t_1.penjualanresep_id = reseptur_t.penjualanresep_id
                      GROUP BY penjualanresep_t_1.penjualanresep_id, penjualanresep_t_1.pegawai_id, (
                            CASE
                                WHEN reseptur_t.penjualanresep_id IS NULL THEN penjualanresep_t_1.noresep
                                ELSE reseptur_t.noresep
                            END)) penjualanresep_t ON obatalkespasien_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
                 LEFT JOIN int_billing_r ON obatalkespasien_r.pembayaran_id = int_billing_r.pembayaran_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_093649_migrate_odoo_view_int_rspb cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_093649_migrate_odoo_view_int_rspb cannot be reverted.\n";

        return false;
    }
    */
}
