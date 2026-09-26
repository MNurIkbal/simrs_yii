<?php

use yii\db\Migration;

/**
 * Class m200814_023123_migrate_mhkn_20200814_jasadokter
 */
class m200814_023123_migrate_mhkn_20200814_jasadokter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute("CREATE VIEW \"public\".\"tenagamedis_v\" AS  SELECT pegawai_m.pegawai_id,
    pegawai_m.nomorindukpegawai,
    pegawai_m.nama_pegawai
   FROM pegawai_m
  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_deleted = false));");

         $this->execute('ALTER TABLE "public"."tenagamedis_v" OWNER TO "postgres";');

         $this->execute('DROP VIEW if exists "public"."laporanrekapjasadokter_v";');

         $this->execute("
            CREATE VIEW \"public\".\"laporanrekapjasadokter_v\" AS  SELECT gabung.pendaftaran_id,
    gabung.pasienadmisi_id,
    gabung.no_pendaftaran,
    gabung.tindakanpelayanan_id,
    gabung.tgl_tindakan,
    gabung.dokterpenanggungjawab_id,
    gabung.nama_pegawai,
    gabung.pasien_id,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    gabung.daftartindakan_id,
    gabung.daftartindakan_nama,
    gabung.tarif_tindakankomp,
    gabung.komponentarif_nama,
    gabung.status_bayar,
    gabung.jenis_transaksi,
    gabung.pelayananjasadokter_id
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tgl_tindakan,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakankomponen_t.tarif_tindakankomp,
            komponentarif_m.komponentarif_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            'pelayanan'::text AS jenis_transaksi,
            NULL::integer AS pelayananjasadokter_id
           FROM ((((((pendaftaran_t
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN tindakankomponen_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id)))
             JOIN komponentarif_m ON (((tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id) AND (komponentarif_m.is_dokter = true))))
             LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.tgl_tindakan,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            tindakankomponen_t.tarif_tindakankomp,
            komponentarif_m.komponentarif_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            'pelayanan'::text AS jenis_transaksi,
            NULL::integer AS pelayananjasadokter_id
           FROM ((((((pendaftaran_t
             JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
             JOIN tindakankomponen_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id)))
             JOIN komponentarif_m ON (((tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id) AND (komponentarif_m.is_dokter = true))))
             LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pembayarantransaksi_t.no_transaksi AS no_pendaftaran,
            NULL::integer AS tindakanpelayanan_id,
            pembayarantransaksi_t.tgl_transaksi AS tgl_tindakan,
            pembayarantransaksi_t.pegawai_id AS dokterpenanggungjawab_id,
            dokter.nama_pegawai,
            NULL::integer AS pasien_id,
            NULL::character varying AS no_rekam_medik,
            NULL::character varying AS nama_pasien,
            NULL::integer AS daftartindakan_id,
            pembayarantransaksi_t.deskripsi AS daftartindakan_nama,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN pembayarantransaksi_t.jumlah
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN (- pembayarantransaksi_t.jumlah)
                    ELSE NULL::double precision
                END AS tarif_tindakankomp,
                CASE
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 668) THEN 'Penerimaan'::text
                    WHEN (pembayarantransaksi_t.jenis_transaksi = 669) THEN 'Pengeluaran'::text
                    ELSE NULL::text
                END AS komponentarif_nama,
            NULL::character varying AS status_bayar,
            'Transaksi Kasir'::text AS jenis_transaksi,
            NULL::integer AS pelayananjasadokter_id
           FROM (pembayarantransaksi_t
             JOIN pegawai_m dokter ON (((pembayarantransaksi_t.pegawai_id = dokter.pegawai_id) AND (dokter.kelompokpegawai_id = 1))))
          WHERE (pembayarantransaksi_t.tipe_transaksi = 701)
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pelayananjasadokter_t.no_transaksi AS no_pendaftaran,
            NULL::integer AS tindakanpelayanan_id,
            pelayananjasadokter_t.tgl_transaksi AS tgl_tindakan,
            pelayananjasadokter_t.pegawai_id AS dokterpenanggungjawab_id,
            dokter.nama_pegawai,
            NULL::integer AS pasien_id,
            NULL::character varying AS no_rekam_medik,
            NULL::character varying AS nama_pasien,
            NULL::integer AS daftartindakan_id,
            pelayananjasadokter_t.deskripsi AS daftartindakan_nama,
            pelayananjasadokter_t.total_jasa AS tarif_tindakankomp,
            jasadokter_m.jasadokter_nama AS komponentarif_nama,
            NULL::character varying AS status_bayar,
            'Transaksi Jasa Dokter'::text AS jenis_transaksi,
            pelayananjasadokter_t.pelayananjasadokter_id
           FROM ((pelayananjasadokter_t
             JOIN pegawai_m dokter ON ((pelayananjasadokter_t.pegawai_id = dokter.pegawai_id)))
             JOIN jasadokter_m ON ((pelayananjasadokter_t.jasadokter_id = jasadokter_m.jasadokter_id)))
          WHERE (pelayananjasadokter_t.is_deleted = false)
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            NULL::integer AS tindakanpelayanan_id,
            pembayaran_t.created_date AS tgl_tindakan,
            pembayarandiskon_t.pegawai_id AS dokterpenanggungjawab_id,
            dokter.nama_pegawai,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            NULL::integer AS daftartindakan_id,
            NULL::character varying AS daftartindakan_nama,
            (- pembayarandiskon_t.total_diskon) AS tarif_tindakankomp,
            komponentarif_m.komponentarif_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            'Diskon'::text AS jenis_transaksi,
            NULL::bigint AS pelayananjasadokter_id
           FROM ((((((pembayarandiskon_t
             JOIN pegawai_m dokter ON ((pembayarandiskon_t.pegawai_id = dokter.pegawai_id)))
             JOIN pembayaran_t ON ((pembayarandiskon_t.pembayaran_id = pembayaran_t.pembayaran_id)))
             JOIN pembayaranpelayanan_t ON (((pembayaran_t.pembayaran_id = pembayaranpelayanan_t.pembayaran_id) AND (pembayaranpelayanan_t.is_deleted = false))))
             JOIN pendaftaran_t ON ((pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN komponentarif_m ON ((pembayarandiskon_t.komponentarif_id = komponentarif_m.komponentarif_id)))
          WHERE (pembayarandiskon_t.is_deleted = false)) gabung;");

         $this->execute('ALTER TABLE "public"."laporanrekapjasadokter_v" OWNER TO "postgres";');

         $this->execute('DROP VIEW if exists "public"."infotindakanpenatajasa_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infotindakanpenatajasa_v\" AS  SELECT 'tindakan'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
        CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS obatalkes_id,
    NULL::character varying AS obatalkes_nama,
    NULL::double precision AS qty_oa,
    NULL::double precision AS hargajual_oa,
    NULL::integer AS satuanobat_id,
    NULL::character varying AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang
   FROM ((((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          WHERE (tindakansudahbayar_t.is_deleted = false)
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
  WHERE (tindakanpelayanan_t.is_deleted = false)
UNION ALL
 SELECT 'paket'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    tipepaket_m.tipepaket_id AS daftartindakan_id,
    tipepaket_m.tipepaket_nama AS daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
        CASE COALESCE(tindakansudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang
   FROM (((((((((((tindakanpelayanan_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          WHERE (tindakansudahbayar_t.is_deleted = false)
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON ((tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id)))
     LEFT JOIN obatalkespasien_t ON (((tindakanpelayanan_t.tindakanpelayanan_id = obatalkespasien_t.tindakanpelayanan_id) AND (obatalkespasien_t.is_deleted = false))))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN pasienmasukpenunjang_t ON (((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id) AND (pasienmasukpenunjang_t.is_deleted = false))))
  WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
UNION ALL
 SELECT 'obat'::text AS jenis,
    NULL::integer AS tindakanpelayanan_id,
    obatalkespasien_t.pendaftaran_id,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    obatalkespasien_t.ruangan_id,
    ruangan_m.ruangan_nama,
    obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    obatalkespasien_t.qty_oa AS qty_tindakan,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarifcyto_tindakan,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan,
    obatalkespasien_t.is_penatajasa,
        CASE COALESCE(obatsudahbayar.telahbayar, (0)::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    obatalkespasien_t.is_deleted,
    NULL::text AS keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    NULL::integer AS kelaspelayanan_id,
    NULL::character varying AS kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_masukpenunjang
   FROM (((((((((obatalkespasien_t
     JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT obatsudahbayar_t.obatalkespasien_id,
            count(*) AS telahbayar
           FROM obatsudahbayar_t
          WHERE (obatsudahbayar_t.is_deleted = false)
          GROUP BY obatsudahbayar_t.obatsudahbayar_id) obatsudahbayar ON ((obatalkespasien_t.obatalkespasien_id = obatsudahbayar.obatalkespasien_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN tindakanpelayanan_t ON (((obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id) AND (tindakanpelayanan_t.is_deleted = false))))
     LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
  WHERE (obatalkespasien_t.is_deleted IS FALSE);");

         $this->execute('ALTER TABLE "public"."infotindakanpenatajasa_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200814_023123_migrate_mhkn_20200814_jasadokter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200814_023123_migrate_mhkn_20200814_jasadokter cannot be reverted.\n";

        return false;
    }
    */
}
