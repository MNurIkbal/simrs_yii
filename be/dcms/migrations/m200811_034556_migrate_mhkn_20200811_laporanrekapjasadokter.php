<?php

use yii\db\Migration;

/**
 * Class m200811_034556_migrate_mhkn_20200811_laporanrekapjasadokter
 */
class m200811_034556_migrate_mhkn_20200811_laporanrekapjasadokter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    gabung.jenis_transaksi
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
            'pelayanan'::text AS jenis_transaksi
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
            'pelayanan'::text AS jenis_transaksi
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
            (- pembayarantransaksi_t.jumlah) AS tarif_tindakankomp,
            'Pengeluaran'::character varying AS komponentarif_nama,
            NULL::character varying AS status_bayar,
            'Transaksi Kasir'::text AS jenis_transaksi
           FROM (pembayarantransaksi_t
             JOIN pegawai_m dokter ON ((pembayarantransaksi_t.pegawai_id = dokter.pegawai_id)))
          WHERE ((pembayarantransaksi_t.tipe_transaksi = 701) AND (pembayarantransaksi_t.jenis_transaksi = 669))
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
            (- pelayananjasadokter_t.total_jasa) AS tarif_tindakankomp,
            jasadokter_m.jasadokter_nama AS komponentarif_nama,
            NULL::character varying AS status_bayar,
            'Transaksi Jasa Dokter'::text AS jenis_transaksi
           FROM ((pelayananjasadokter_t
             JOIN pegawai_m dokter ON ((pelayananjasadokter_t.pegawai_id = dokter.pegawai_id)))
             JOIN jasadokter_m ON ((pelayananjasadokter_t.jasadokter_id = jasadokter_m.jasadokter_id)))) gabung;");

		$this->execute('ALTER TABLE "public"."laporanrekapjasadokter_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200811_034556_migrate_mhkn_20200811_laporanrekapjasadokter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200811_034556_migrate_mhkn_20200811_laporanrekapjasadokter cannot be reverted.\n";

        return false;
    }
    */
}
