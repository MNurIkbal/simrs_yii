<?php

use yii\db\Migration;

/**
 * Class m200916_102905_migrate_20200916_laporanpenunjangbsl
 */
class m200916_102905_migrate_20200916_laporanpenunjangbsl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpenunjangbsl_v";');

        $this->execute('
            CREATE VIEW "public"."laporanpenunjangbsl_v" AS  SELECT row_number() OVER (ORDER BY pasienmasukpenunjang_t.pasienmasukpenunjang_id) AS "No.",
    NULL::text AS "Emp. No",
    pasien_m.no_rekam_medik AS "No. Medical Record",
    pasien_m.nama_pasien AS "Nama Lengkap",
    pasien_m.alamat_pasien AS alamat,
    to_char((pasien_m.tanggal_lahir)::timestamp with time zone, \'\'\'dd/mm/yyyy\'::text) AS "Tanggal Lahir",
    "left"((pendaftaran_t.umur)::text, 8) AS "Usia",
    fgetvaluelookup((pasien_m.jeniskelamin)::integer) AS "Jenis Kelamin",
    fgetvaluelookup((pasien_m.statusperkawinan)::integer) AS "Status Perkawainan",
    NULL::text AS "Departemen",
    NULL::text AS " Posisi/Bagian",
    pemeriksaan.tindakan AS "Jenis Medical Chcek Up/Nama Tindakan",
    \'Mayapada Hospital Kuningan\'::text AS "Nama Perusahaan",
    to_char((pasienmasukpenunjang_t.tglmasukpenunjang)::timestamp with time zone, \'\'\'dd/mm/yyyy\'::text) AS "Tanggal Pemeriksaan",
    NULL::text AS passport,
    pasien_m.alamatemail AS "Email",
    pasien_m.no_telepon_pasien AS "MobilePhone",
    pasien_m.no_identitas_pasien AS "KTPNo",
    kabupaten_m.kabupaten_nama AS kotaalamat,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_pemeriksaan
   FROM (((((((pasienmasukpenunjang_t
     LEFT JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
     JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            daftartindakan_m.daftartindakan_nama AS tindakan
           FROM ((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN pemeriksaanlab_m ON (((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id) AND (pemeriksaanlab_m.is_deleted = false))))
          WHERE (tindakanpelayanan_t.is_deleted = false)
        UNION ALL
         SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            daftartindakan_m.daftartindakan_nama AS tindakan
           FROM (((tindakanpelayanan_t
             JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN pemeriksaanlab_m ON (((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id) AND (pemeriksaanlab_m.is_deleted = false))))
          WHERE (tindakanpelayanan_t.is_deleted = false)) pemeriksaan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = pemeriksaan.pasienmasukpenunjang_id)))
  WHERE (pasienmasukpenunjang_t.is_deleted = false);');
        
        $this->execute('ALTER TABLE "public"."laporanpenunjangbsl_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200916_102905_migrate_20200916_laporanpenunjangbsl cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200916_102905_migrate_20200916_laporanpenunjangbsl cannot be reverted.\n";

        return false;
    }
    */
}
