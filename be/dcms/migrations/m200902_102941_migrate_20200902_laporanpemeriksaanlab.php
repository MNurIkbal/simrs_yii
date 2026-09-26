<?php

use yii\db\Migration;

/**
 * Class m200902_102941_migrate_20200902_laporanpemeriksaanlab
 */
class m200902_102941_migrate_20200902_laporanpemeriksaanlab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpemeriksaanlab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpemeriksaanlab_v\" AS  SELECT 'NON_PAKET'::text AS tipe,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    pasienmasukpenunjang_t.pegawai_id,
    dokter.nama_pegawai AS dokter,
    pemeriksaanlab_m.kelompokpemeriksaanlab_id,
    kelompokpemeriksaanlab_m.nama_kelompok,
    pemeriksaanlab_m.jenispemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan
   FROM ((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dokter ON ((pasienmasukpenunjang_t.pegawai_id = dokter.pegawai_id)))
     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN pemeriksaanlab_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
     JOIN kelompokpemeriksaanlab_m ON ((pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id)))
     JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa)::integer = 474)
UNION ALL
 SELECT 'PAKET'::text AS tipe,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    pasienmasukpenunjang_t.pegawai_id,
    dokter.nama_pegawai AS dokter,
    pemeriksaanlab_m.kelompokpemeriksaanlab_id,
    kelompokpemeriksaanlab_m.nama_kelompok,
    pemeriksaanlab_m.jenispemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    paketpelayanan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan
   FROM ((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dokter ON ((pasienmasukpenunjang_t.pegawai_id = dokter.pegawai_id)))
     JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN pemeriksaanlab_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
     JOIN kelompokpemeriksaanlab_m ON ((pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id)))
     JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa)::integer = 474);");

        $this->execute('ALTER TABLE "public"."laporanpemeriksaanlab_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200902_102941_migrate_20200902_laporanpemeriksaanlab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200902_102941_migrate_20200902_laporanpemeriksaanlab cannot be reverted.\n";

        return false;
    }
    */
}
