<?php

use yii\db\Migration;

/**
 * Class m201206_073146_migrate_mhkn_20201206_report_salesorder_deleted
 */
class m201206_073146_migrate_mhkn_20201206_report_salesorder_deleted extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.report_salesorder_deleted;');
        $this->execute("CREATE VIEW \"public\".\"report_salesorder_deleted\" AS
             SELECT (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_proses,
    pelayanan.tgl_pelayanan AS wipro_order_date,
    pembayaranpelayanan_t.no_pembayaran AS bill_no,
    (to_char(pembayaranpelayanan_t.tgl_pembayaran, 'YYYY-MM-DD'::text))::date AS bill_date,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelas_x.kelaspelayanan_nama
            ELSE kelas_y.kelaspelayanan_nama
        END AS bed_type,
    pasien_m.no_rekam_medik AS mr_number,
    pasien_m.nama_pasien AS patient_name,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS gender,
    pelayanan.ruang_pelayanan AS department,
    pelayanan.product,
    pelayanan.kelompok,
    pelayanan.kategori,
    pelayanan.qty,
    pelayanan.uom,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_x.penjamin_nama
            ELSE penjamin_y.penjamin_nama
        END AS payer,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_x.s_kode
            ELSE penjamin_y.s_kode
        END AS payer_code,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_x.carabayar_nama
            ELSE carabayar_y.carabayar_nama
        END AS payer_type,
    pelayanan.revenue_type_layer1,
        CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN 'OUTPATIENT'::text
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN 'EMERGENCY'::text
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 21)) THEN 'MCU'::text
            WHEN (pelayanan.revenue_type_layer1 = 'DISCOUNT'::text) THEN 'DISCOUNT'::text
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN 'INPATIENT'::text
            ELSE NULL::text
        END AS revenue_type_layer2,
    pelayanan.unit_price,
    pelayanan.cost_cogs_grn,
    pelayanan.cost_cogs_average,
    pelayanan.total_revenue,
    COALESCE(j_rumahsakit.jasa_rumahsakit, (0)::double precision) AS jasa_rumahsakit,
    COALESCE(j_dokter.jasa_dokter, (0)::double precision) AS jasa_dokter,
    COALESCE(j_obatan.obatan, (0)::double precision) AS obat_obatan,
    COALESCE(j_alkes.alkes, (0)::double precision) AS alat_kesehatan,
    COALESCE(j_insentif.insentif, (0)::double precision) AS insentif_pelayanan,
    COALESCE(j_perawat.jasa_perawat, (0)::double precision) AS jasa_perawat,
    COALESCE(j_anastesi.jasa_anastesi, (0)::double precision) AS jasa_anastesi,
    COALESCE(j_operator.jasa_operator, (0)::double precision) AS jasa_operator,
    pelayanan.dokter,
    pelayanan.spesialisasi,
    '???'::text AS type_line
   FROM ((((((((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN kelaspelayanan_m kelas_x ON ((pendaftaran_t.kelaspelayanan_id = kelas_x.kelaspelayanan_id)))
     LEFT JOIN kelaspelayanan_m kelas_y ON ((pasienadmisi_t.kelaspelayanan_id = kelas_y.kelaspelayanan_id)))
     LEFT JOIN penjamin_m penjamin_x ON ((pendaftaran_t.penjamin_id = penjamin_x.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_y ON ((pasienadmisi_t.penjamin_id = penjamin_y.penjamin_id)))
     LEFT JOIN carabayar_m carabayar_x ON ((penjamin_x.carabayar_id = carabayar_x.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_y ON ((penjamin_y.carabayar_id = carabayar_y.carabayar_id)))
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakansudahbayar_id AS sudahbayar_id,
            tindakansudahbayar_t.pembayaranpelayanan_id,
            ruangan_m.ruangan_nama AS ruang_pelayanan,
            daftartindakan_m.daftartindakan_nama AS product,
            kelompoktindakan_m.kelompoktindakan_nama AS kelompok,
            kategoritindakan_m.kategoritindakan_nama AS kategori,
            tindakanpelayanan_t.qty_tindakan AS qty,
            NULL::character varying AS uom,
                CASE
                    WHEN ((kelompoktindakan_m.kelompoktindakan_namalainnya)::text = 'LOB'::text) THEN 'LOB'::text
                    WHEN ((kelompoktindakan_m.kelompoktindakan_namalainnya)::text = 'LOS'::text) THEN 'LOS'::text
                    ELSE NULL::text
                END AS revenue_type_layer1,
            NULL::text AS revenue_type_layer3,
            (- tindakanpelayanan_t.tarif_satuan) AS unit_price,
            (- tindakanpelayanan_t.tarif_tindakan) AS total_revenue,
            tindakanpelayanan_t.tindakanpelayanan_id,
            peg_tindakan.nama_pegawai AS dokter,
            pendidikankualifikasi_m.pendkualifikasi_nama AS spesialisasi,
            0 AS cost_cogs_average,
            (- tindakanpelayanan_t.tarif_satuan) AS cost_cogs_grn
           FROM (((((((tindakanpelayanan_t
             LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             LEFT JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
             LEFT JOIN kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
             LEFT JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
             LEFT JOIN pegawai_m peg_tindakan ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = peg_tindakan.pegawai_id)))
             LEFT JOIN pendidikankualifikasi_m ON ((peg_tindakan.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
          WHERE (tindakanpelayanan_t.is_deleted = true)
        UNION ALL
         SELECT obatalkespasien_t.pendaftaran_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatsudahbayar_id AS sudahbayar_id,
            obatsudahbayar_t.pembayaranpelayanan_id,
            ruangan_m.ruangan_nama AS ruang_pelayanan,
            obatalkes_m.obatalkes_nama AS product,
            jenisobatalkes_m.jenisobatalkes_nama AS kelompok,
            fgetnamalookup((jenisobatalkes_m.group_jenisobat)::integer) AS kategori,
            obatalkespasien_t.qty_oa AS qty,
            satuanunit_m.satuanunit_nama AS uom,
                CASE
                    WHEN (jenisobatalkes_m.jenisobatalkes_id = ANY (ARRAY[1, 4, 7, 9, 16])) THEN 'LOS'::text
                    ELSE 'LOB'::text
                END AS revenue_type_layer1,
            NULL::text AS revenue_type_layer3,
            (- obatalkespasien_t.hargajual_oa) AS unit_price,
            (- obatalkespasien_t.hargajual_oa) AS total_revenue,
            NULL::integer AS tindakanpelayanan_id,
            peg_obat.nama_pegawai AS dokter,
            pendidikankualifikasi_m.pendkualifikasi_nama AS spesialisasi,
            (- stokobatalkes_t.harga_netto_avg) AS cost_cogs_average,
            (- obatalkespasien_t.harganetto_oa) AS cost_cogs_grn
           FROM ((((((((obatalkespasien_t
             LEFT JOIN ( SELECT stokobatalkes_t_1.obatalkespasien_id,
                    max(stokobatalkes_t_1.harga_netto_avg) AS harga_netto_avg
                   FROM stokobatalkes_t stokobatalkes_t_1
                  WHERE (stokobatalkes_t_1.is_deleted = false)
                  GROUP BY stokobatalkes_t_1.obatalkespasien_id) stokobatalkes_t ON ((obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id)))
             LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             LEFT JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
             LEFT JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
             LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
             LEFT JOIN pegawai_m peg_obat ON ((obatalkespasien_t.pegawai_id = peg_obat.pegawai_id)))
             LEFT JOIN pendidikankualifikasi_m ON ((peg_obat.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
          WHERE (obatalkespasien_t.is_deleted = true)) pelayanan ON ((pendaftaran_t.pendaftaran_id = pelayanan.pendaftaran_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((pelayanan.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
            sum((- tindakankomponen_t.tarif_tindakankomp)) AS jasa_rumahsakit
           FROM tindakankomponen_t
          WHERE ((tindakankomponen_t.komponentarif_id = 7) AND (tindakankomponen_t.is_deleted = false))
          GROUP BY tindakankomponen_t.tindakanpelayanan_id) j_rumahsakit ON ((pelayanan.tindakanpelayanan_id = j_rumahsakit.tindakanpelayanan_id)))
     LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
            sum((- tindakankomponen_t.tarif_tindakankomp)) AS jasa_dokter
           FROM tindakankomponen_t
          WHERE ((tindakankomponen_t.komponentarif_id = 11) AND (tindakankomponen_t.is_deleted = false))
          GROUP BY tindakankomponen_t.tindakanpelayanan_id) j_dokter ON ((pelayanan.tindakanpelayanan_id = j_dokter.tindakanpelayanan_id)))
     LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
            sum((- tindakankomponen_t.tarif_tindakankomp)) AS obatan
           FROM tindakankomponen_t
          WHERE ((tindakankomponen_t.komponentarif_id = 9) AND (tindakankomponen_t.is_deleted = false))
          GROUP BY tindakankomponen_t.tindakanpelayanan_id) j_obatan ON ((pelayanan.tindakanpelayanan_id = j_obatan.tindakanpelayanan_id)))
     LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
            sum((- tindakankomponen_t.tarif_tindakankomp)) AS alkes
           FROM tindakankomponen_t
          WHERE ((tindakankomponen_t.komponentarif_id = 12) AND (tindakankomponen_t.is_deleted = false))
          GROUP BY tindakankomponen_t.tindakanpelayanan_id) j_alkes ON ((pelayanan.tindakanpelayanan_id = j_alkes.tindakanpelayanan_id)))
     LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
            sum((- tindakankomponen_t.tarif_tindakankomp)) AS insentif
           FROM tindakankomponen_t
          WHERE ((tindakankomponen_t.komponentarif_id = 10) AND (tindakankomponen_t.is_deleted = false))
          GROUP BY tindakankomponen_t.tindakanpelayanan_id) j_insentif ON ((pelayanan.tindakanpelayanan_id = j_insentif.tindakanpelayanan_id)))
     LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
            sum((- tindakankomponen_t.tarif_tindakankomp)) AS jasa_perawat
           FROM tindakankomponen_t
          WHERE ((tindakankomponen_t.komponentarif_id = 8) AND (tindakankomponen_t.is_deleted = false))
          GROUP BY tindakankomponen_t.tindakanpelayanan_id) j_perawat ON ((pelayanan.tindakanpelayanan_id = j_perawat.tindakanpelayanan_id)))
     LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
            sum((- tindakankomponen_t.tarif_tindakankomp)) AS jasa_anastesi
           FROM tindakankomponen_t
          WHERE ((tindakankomponen_t.komponentarif_id = 13) AND (tindakankomponen_t.is_deleted = false))
          GROUP BY tindakankomponen_t.tindakanpelayanan_id) j_anastesi ON ((pelayanan.tindakanpelayanan_id = j_anastesi.tindakanpelayanan_id)))
     LEFT JOIN ( SELECT tindakankomponen_t.tindakanpelayanan_id,
            sum((- tindakankomponen_t.tarif_tindakankomp)) AS jasa_operator
           FROM tindakankomponen_t
          WHERE ((tindakankomponen_t.komponentarif_id = 14) AND (tindakankomponen_t.is_deleted = false))
          GROUP BY tindakankomponen_t.tindakanpelayanan_id) j_operator ON ((pelayanan.tindakanpelayanan_id = j_operator.tindakanpelayanan_id)))
            ;");
            $this->execute('ALTER TABLE public.report_salesorder_deleted
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201206_073146_migrate_mhkn_20201206_report_salesorder_deleted cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201206_073146_migrate_mhkn_20201206_report_salesorder_deleted cannot be reverted.\n";

        return false;
    }
    */
}
