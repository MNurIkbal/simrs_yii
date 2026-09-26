<?php

use yii\db\Migration;

/**
 * Class m240503_070558_migrate_RPP_1319_improve_laporanleadtimeresep_v
 */
class m240503_070558_migrate_RPP_1319_improve_laporanleadtimeresep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanleadtimeresep_v";');
        $this->execute("
			CREATE OR REPLACE VIEW public.laporanleadtimeresep_v
            AS   SELECT a.ruangan,
    a.tgl_reseptur,
    a.tgl_resep,
    a.no_resep,
    a.no_reseptur,
    a.jenis_resep,
    a.jumlah_r,
    a.dokter,
    a.jumlah_item,
    a.jam_resep_masuk,
    a.jam_resep_dibayar,
    a.jam_production,
    a.jam_diserahkan,
    a.waktu_tunggu,
        CASE
            WHEN a.waktu_tunggu IS NOT NULL THEN concat(
            CASE
                WHEN (date_part('day'::text, a.waktu_tunggu) * 24::double precision + date_part('hour'::text, a.waktu_tunggu)) < 9::double precision THEN '0'::text || (date_part('day'::text, a.waktu_tunggu) * 24::double precision + date_part('hour'::text, a.waktu_tunggu))
                ELSE (date_part('day'::text, a.waktu_tunggu) * 24::double precision + date_part('hour'::text, a.waktu_tunggu))::text
            END, ':',
            CASE
                WHEN date_part('minute'::text, a.waktu_tunggu)::integer < 9 THEN '0'::text || date_part('minute'::text, a.waktu_tunggu)
                ELSE date_part('minute'::text, a.waktu_tunggu)::text
            END, ':',
            CASE
                WHEN date_part('second'::text, a.waktu_tunggu) < 9::double precision THEN '0'::text || date_part('second'::text, a.waktu_tunggu)
                ELSE date_part('second'::text, a.waktu_tunggu)::text
            END)
            ELSE NULL::text
        END AS waktu_tunggu_format,
    a.ruangan_id
   FROM ( SELECT COALESCE(ruangan_reseptur.ruangan_nama, ruangan_resep.ruangan_nama) AS ruangan,
            reseptur_t.tglreseptur AS tgl_reseptur,
            penjualanresep_t.tglresep AS tgl_resep,
            penjualanresep_t.noresep AS no_resep,
            reseptur_t.noresep AS no_reseptur,
                CASE
                    WHEN jenis_resep.racikan_id = '1'::text THEN 'RACIKAN'::text
                    WHEN jenis_resep.racikan_id = '2'::text THEN 'NON-RACIKAN'::text
                    ELSE 'RACIKAN'::text
                END AS jenis_resep,
            jumlah_r.jumlah AS jumlah_r,
            dokter.nama_pegawai AS dokter,
            jumlah_obat.jumlah_qty AS jumlah_item,
                CASE
                    WHEN penjualanresep_t.reseptur_id IS NOT NULL THEN reseptur_t.tglreseptur
                    ELSE penjualanresep_t.tglresep
                END AS jam_resep_masuk,
            bayar_resep.tgl_bayar AS jam_resep_dibayar,
            COALESCE(wkt_produksi.tanggal, wkt_produksi_reseptur.tanggal) AS jam_production,
            COALESCE(wkt_diserahkan.tanggal, wkt_diserahkan_reseptur.tanggal) AS jam_diserahkan,
                CASE
                    WHEN penjualanresep_t.reseptur_id IS NOT NULL THEN COALESCE(wkt_diserahkan.tanggal, wkt_diserahkan_reseptur.tanggal) - reseptur_t.tglreseptur
                    ELSE COALESCE(wkt_diserahkan.tanggal, wkt_diserahkan_reseptur.tanggal) - penjualanresep_t.tglresep
                END AS waktu_tunggu,
            COALESCE(ruangan_reseptur.ruangan_id, penjualanresep_t.ruangan_id) AS ruangan_id
           FROM penjualanresep_t
             LEFT JOIN ( SELECT a_1.reseptur_id,
                    a_1.tglreseptur,
                    a_1.ruanganreseptur_id,
                    a_1.noresep
                   FROM reseptur_t a_1
                  WHERE a_1.is_deleted = false) reseptur_t ON penjualanresep_t.reseptur_id = reseptur_t.reseptur_id
             LEFT JOIN ( SELECT a_1.ruangan_id,
                    a_1.ruangan_nama
                   FROM ruangan_m a_1) ruangan_reseptur ON reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id
             LEFT JOIN ( SELECT a_1.ruangan_id,
                    a_1.ruangan_nama
                   FROM ruangan_m a_1) ruangan_resep ON penjualanresep_t.ruangan_id = ruangan_resep.ruangan_id
             JOIN ( SELECT string_agg(racikan.racikan_id::text, '-'::text) AS racikan_id,
                    racikan.penjualanresep_id
                   FROM ( SELECT a_1.racikan_id,
                            a_1.penjualanresep_id
                           FROM obatalkespasien_t a_1
                          WHERE a_1.is_deleted = false
                          GROUP BY a_1.racikan_id, a_1.penjualanresep_id
                          ORDER BY a_1.racikan_id) racikan
                  GROUP BY racikan.penjualanresep_id) jenis_resep ON penjualanresep_t.penjualanresep_id = jenis_resep.penjualanresep_id
             JOIN ( SELECT count_obat.penjualanresep_id,
                    count(count_obat.obatalkes_id) AS jumlah
                   FROM ( SELECT a_1.penjualanresep_id,
                            a_1.obatalkes_id
                           FROM obatalkespasien_t a_1
                          WHERE a_1.is_deleted = false
                          GROUP BY a_1.penjualanresep_id, a_1.obatalkes_id) count_obat
                  GROUP BY count_obat.penjualanresep_id) jumlah_r ON penjualanresep_t.penjualanresep_id = jumlah_r.penjualanresep_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai
                   FROM pegawai_m a_1
                  WHERE a_1.is_deleted = false) dokter ON penjualanresep_t.pegawai_id = dokter.pegawai_id
             LEFT JOIN ( SELECT total.penjualanresep_id,
                    sum(total.jumlah_qty) AS jumlah_qty
                   FROM ( SELECT a_1.penjualanresep_id,
                                CASE
                                    WHEN a_1.det_konversi IS NOT NULL THEN sum(a_1.det_konversi)
                                    WHEN a_1.qty_konversi IS NOT NULL THEN sum(a_1.qty_konversi)
                                    ELSE sum(a_1.qty_oa)
                                END AS jumlah_qty
                           FROM obatalkespasien_t a_1
                          WHERE a_1.is_deleted = false
                          GROUP BY a_1.penjualanresep_id, a_1.det_konversi, a_1.qty_konversi) total
                  GROUP BY total.penjualanresep_id) jumlah_obat ON jumlah_obat.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT a_1.pembayaran_id,
                    a_1.penjualanresep_id,
                    pembayaran_t.tgl_bayar
                   FROM obatalkespasien_t a_1
                     JOIN ( SELECT a1.pembayaran_id,
                            a1.created_date AS tgl_bayar
                           FROM pembayaran_t a1
                          WHERE a1.is_deleted = false) pembayaran_t ON a_1.pembayaran_id = pembayaran_t.pembayaran_id
                  WHERE a_1.is_deleted = false
                  GROUP BY a_1.pembayaran_id, a_1.penjualanresep_id, pembayaran_t.tgl_bayar) bayar_resep ON penjualanresep_t.penjualanresep_id = bayar_resep.penjualanresep_id
             LEFT JOIN ( SELECT penjualanresep.penjualanresep_id,
                    max(worklist.tanggal)::timestamp without time zone AS tanggal,
                    worklist.pegawai_id
                   FROM penjualanresep_t penjualanresep
                     LEFT JOIN ( SELECT log_status.penjualanresep_id,
                            log.value::json ->> 'tanggal'::text AS tanggal,
                            log.value::json ->> 'pegawai_id'::text AS pegawai_id
                           FROM penjualanresep_t log_status
                             JOIN LATERAL json_each_text(log_status.additional_data::json -> 'log_status'::text) log(key, value) ON true
                          WHERE (log.value::json ->> 'status_worklist_id'::text) = '678'::text) worklist ON worklist.penjualanresep_id = penjualanresep.penjualanresep_id
                  WHERE penjualanresep.is_deleted = false
                  GROUP BY penjualanresep.penjualanresep_id, worklist.pegawai_id) wkt_diserahkan ON penjualanresep_t.penjualanresep_id = wkt_diserahkan.penjualanresep_id
             LEFT JOIN ( SELECT a_1.penjualanresep_id,
                    a_1.tanggal,
                    a_1.pegawai_id
                   FROM ( SELECT penjualanresep.penjualanresep_id,
                            max(worklist.tanggal)::timestamp without time zone AS tanggal,
                            worklist.pegawai_id
                           FROM penjualanresep_t penjualanresep
                             LEFT JOIN ( SELECT log_status.penjualanresep_id,
                                    log.value::json ->> 'tanggal'::text AS tanggal,
                                    log.value::json ->> 'pegawai_id'::text AS pegawai_id
                                   FROM penjualanresep_t log_status
                                     JOIN LATERAL json_each_text(log_status.additional_data::json -> 'log_status'::text) log(key, value) ON true
                                  WHERE (log.value::json ->> 'status_worklist_id'::text) = '677'::text) worklist ON worklist.penjualanresep_id = penjualanresep.penjualanresep_id
                          WHERE penjualanresep.is_deleted = false
                          GROUP BY penjualanresep.penjualanresep_id, worklist.pegawai_id) a_1
                  WHERE a_1.tanggal IS NOT NULL AND a_1.pegawai_id IS NOT NULL) wkt_produksi ON penjualanresep_t.penjualanresep_id = wkt_produksi.penjualanresep_id
             LEFT JOIN ( SELECT reseptur.reseptur_id,
                    max(worklist.tanggal)::timestamp without time zone AS tanggal
                   FROM reseptur_t reseptur
                     LEFT JOIN ( SELECT log_status.reseptur_id,
                            log.value::json ->> 'tanggal'::text AS tanggal,
                            log.value::json ->> 'pegawai_id'::text AS pegawai_id
                           FROM reseptur_t log_status
                             JOIN LATERAL json_each_text(log_status.additional_data::json -> 'log_status'::text) log(key, value) ON true
                          WHERE (log.value::json ->> 'status_worklist_id'::text) = '678'::text) worklist ON worklist.reseptur_id = reseptur.reseptur_id
                  GROUP BY reseptur.reseptur_id) wkt_diserahkan_reseptur ON reseptur_t.reseptur_id = wkt_diserahkan_reseptur.reseptur_id
             LEFT JOIN ( SELECT a_1.reseptur_id,
                    a_1.tanggal
                   FROM ( SELECT reseptur.reseptur_id,
                            max(worklist.tanggal)::timestamp without time zone AS tanggal
                           FROM reseptur_t reseptur
                             LEFT JOIN ( SELECT log_status.reseptur_id,
                                    log.value::json ->> 'tanggal'::text AS tanggal,
                                    log.value::json ->> 'pegawai_id'::text AS pegawai_id
                                   FROM reseptur_t log_status
                                     JOIN LATERAL json_each_text(log_status.additional_data::json -> 'log_status'::text) log(key, value) ON true
                                  WHERE (log.value::json ->> 'status_worklist_id'::text) = '677'::text) worklist ON worklist.reseptur_id = reseptur.reseptur_id
                          GROUP BY reseptur.reseptur_id) a_1
                  WHERE a_1.tanggal IS NOT NULL) wkt_produksi_reseptur ON reseptur_t.reseptur_id = wkt_produksi_reseptur.reseptur_id
          WHERE penjualanresep_t.is_deleted = false) a ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240503_070558_migrate_RPP_1319_improve_laporanleadtimeresep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240503_070558_migrate_RPP_1319_improve_laporanleadtimeresep_v cannot be reverted.\n";

        return false;
    }
    */
}
