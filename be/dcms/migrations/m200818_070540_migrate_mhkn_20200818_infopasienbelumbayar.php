<?php

use yii\db\Migration;

/**
 * Class m200818_070540_migrate_mhkn_20200818_infopasienbelumbayar
 */
class m200818_070540_migrate_mhkn_20200818_infopasienbelumbayar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."infopasienbelumbayar_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infopasienbelumbayar_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN cb_1.carabayar_nama
            ELSE cb_2.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pj_1.penjamin_nama
            ELSE pj_2.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dr_1.nama_pegawai
            ELSE dr_2.nama_pegawai
        END AS nama_dokter,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_1.instalasi_nama
            ELSE ins_1.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.ruangan_nama
            ELSE ruang_2.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup((pendaftaran_t.status_periksa)::integer)
            ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
        END AS status_periksa,
    (COALESCE(tindakan.total_tindakan, (0)::double precision) + COALESCE(obat.total_obat, (0)::double precision)) AS total_tagihan,
    COALESCE(uang_masuk.total_uangmasuk, (0)::double precision) AS uang_masuk,
    ((COALESCE(tindakan.total_tindakan, (0)::double precision) + COALESCE(obat.total_obat, (0)::double precision)) - COALESCE(uang_masuk.total_uangmasuk, (0)::double precision)) AS sisa_tagihan,
    konfigsystem_k.kelola_tagihan,
        CASE
            WHEN ((COALESCE(tindakan.total_tindakan, (0)::double precision) + COALESCE(obat.total_obat, (0)::double precision)) >= (konfigsystem_k.kelola_tagihan)::double precision) THEN true
            ELSE false
        END AS is_kelola_tagihan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.instalasi_id
            ELSE ruang_2.instalasi_id
        END AS instalasi_id
   FROM (((((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN carabayar_m cb_1 ON ((pendaftaran_t.carabayar_id = cb_1.carabayar_id)))
     LEFT JOIN carabayar_m cb_2 ON ((pasienadmisi_t.carabayar_id = cb_2.carabayar_id)))
     LEFT JOIN penjamin_m pj_1 ON ((pendaftaran_t.penjamin_id = pj_1.penjamin_id)))
     LEFT JOIN penjamin_m pj_2 ON ((pasienadmisi_t.penjamin_id = pj_2.penjamin_id)))
     LEFT JOIN pegawai_m dr_1 ON ((pendaftaran_t.pegawai_id = dr_1.pegawai_id)))
     LEFT JOIN pegawai_m dr_2 ON ((pasienadmisi_t.pegawai_id = dr_2.pegawai_id)))
     LEFT JOIN ruangan_m ruang_1 ON ((pendaftaran_t.ruangan_id = ruang_1.ruangan_id)))
     LEFT JOIN ruangan_m ruang_2 ON ((pasienadmisi_t.ruangan_id = ruang_2.ruangan_id)))
     LEFT JOIN instalasi_m ins_1 ON ((ruang_1.instalasi_id = ins_1.instalasi_id)))
     LEFT JOIN instalasi_m ins_2 ON ((ruang_2.instalasi_id = dr_2.pegawai_id)))
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan
           FROM tindakanpelayanan_t
          WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
          GROUP BY tindakanpelayanan_t.pendaftaran_id) tindakan ON ((pendaftaran_t.pendaftaran_id = tindakan.pendaftaran_id)))
     LEFT JOIN ( SELECT obatalkespasien_t.pendaftaran_id,
            sum(obatalkespasien_t.hargajual_oa) AS total_obat
           FROM obatalkespasien_t
          WHERE (obatalkespasien_t.is_deleted = false)
          GROUP BY obatalkespasien_t.pendaftaran_id) obat ON ((pendaftaran_t.pendaftaran_id = obat.pendaftaran_id)))
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(((pembayaran_t.total_dibayar - pembayaran_t.total_kembalian) + pembayaran_t.total_dijamin)) AS total_uangmasuk
           FROM (pembayaranpelayanan_t pembayaranpelayanan_t_1
             JOIN pembayaran_t ON ((pembayaranpelayanan_t_1.pembayaran_id = pembayaran_t.pembayaran_id)))
          WHERE ((pembayaranpelayanan_t_1.is_deleted = false) AND (pembayaran_t.is_deleted = false))
          GROUP BY pembayaran_t.pendaftaran_id) uang_masuk ON ((pendaftaran_t.pendaftaran_id = uang_masuk.pendaftaran_id)))
     LEFT JOIN konfigsystem_k ON ((konfigsystem_k.is_deleted = false)))
     LEFT JOIN pembayaranpelayanan_t ON (((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pembayaranpelayanan_t.is_deleted = false))))
  WHERE (pendaftaran_t.status_bayar = 349);");

         $this->execute('ALTER TABLE "public"."infopasienbelumbayar_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200818_070540_migrate_mhkn_20200818_infopasienbelumbayar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200818_070540_migrate_mhkn_20200818_infopasienbelumbayar cannot be reverted.\n";

        return false;
    }
    */
}
