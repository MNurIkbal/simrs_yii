<?php

use yii\db\Migration;

/**
 * Class m200825_123103_migrate_mhkn_20200825_worklist
 */
class m200825_123103_migrate_mhkn_20200825_worklist extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."worklistresep_v";');

         $this->execute("
            CREATE VIEW \"public\".\"worklistresep_v\" AS  SELECT penjualanresep_t.penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    concat(fgetnamalookup((pasien_m.namadepan)::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    array_agg(anamnesa_t.riwayat_alergiobat) AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup((reseptur_t.status_worklist)::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur,
    periksa_fisik_rj.tinggi AS tinggi_badan,
    periksa_fisik_rj.berat AS berat_badan,
    reseptur_t.additional_data AS add_reseptur,
    penjualanresep_t.additional_data AS add_penjualaanresep
   FROM (((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
     LEFT JOIN ( SELECT pemeriksaanfisik_t.pendaftaran_id,
            pemeriksaanfisik_t.tinggibadan_cm AS tinggi,
            pemeriksaanfisik_t.beratbadan_kg AS berat
           FROM pemeriksaanfisik_t
          WHERE (pemeriksaanfisik_t.is_deleted = false)) periksa_fisik_rj ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
  GROUP BY reseptur_t.noresep, penjualanresep_t.noresep, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, pegawai_m.nama_pegawai, reseptur_t.status_worklist, ruangan_asal.instalasi_id, penjualanresep_t.tglpenjualan, penjualanresep_t.status_bayar, penjualanresep_t.penjualanresep_id, reseptur_t.reseptur_id, penjualanresep_t.status_reseptur, periksa_fisik_rj.tinggi, periksa_fisik_rj.berat, pasien_m.namadepan
UNION ALL
 SELECT NULL::integer AS penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    NULL::character varying AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    concat(fgetnamalookup((pasien_m.namadepan)::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup((reseptur_t.status_worklist)::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    reseptur_t.tglreseptur AS tanggal,
    NULL::smallint AS status_bayar_id,
    NULL::character varying AS status_bayar,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN periksa_fisik_rd.tinggi
            ELSE periksa_fisik_ri.tinggi
        END AS tinggi_badan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN periksa_fisik_rd.berat
            ELSE periksa_fisik_ri.berat
        END AS berat_badan,
    reseptur_t.additional_data AS add_reseptur,
    NULL::text AS add_penjualaanresep
   FROM ((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN ( SELECT asesmenperawatrd_t.pendaftaran_id,
            asesmenperawatrd_t.tinggi_badan AS tinggi,
            asesmenperawatrd_t.berat_badan AS berat
           FROM asesmenperawatrd_t
          WHERE (asesmenperawatrd_t.is_deleted = false)) periksa_fisik_rd ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id)))
     LEFT JOIN ( SELECT asesmenmedis_t.pendaftaran_id,
            asesmenmedis_t.tinggi_badan AS tinggi,
            asesmenmedis_t.berat_badan AS berat
           FROM asesmenmedis_t
          WHERE (asesmenmedis_t.is_deleted = false)) periksa_fisik_ri ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id)))
  WHERE (ruangan_asal.instalasi_id <> 1)
UNION ALL
 SELECT penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
    NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_rm,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN (concat(fgetnamalookup((pasien_m.namadepan)::integer), ' ', pasien_m.nama_pasien))::character varying
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN (concat(fgetnamalookup((pegawai_m.gelardepan)::integer), ' ', karyawan.nama_pegawai))::character varying
            ELSE NULL::character varying
        END AS nama_pasien,
        CASE
            WHEN (penjualanresep_t.pasien_id IS NOT NULL) THEN pasien_m.tanggal_lahir
            ELSE NULL::date
        END AS tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    penjualanresep_t.status_worklist AS status_worklist_id,
    fgetnamalookup((penjualanresep_t.status_worklist)::integer) AS status_worklist,
    pendaftaran_t.instalasi_id,
    penjualanresep_t.jenispenjualan AS jenispenjualan_id,
    fgetnamalookup((penjualanresep_t.jenispenjualan)::integer) AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur,
    NULL::integer AS tinggi_badan,
    NULL::integer AS berat_badan,
    NULL::text AS add_reseptur,
    penjualanresep_t.additional_data AS add_penjualaanresep
   FROM ((((penjualanresep_t
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
  WHERE (penjualanresep_t.reseptur_id IS NULL);");

         $this->execute('ALTER TABLE "public"."worklistresep_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200825_123103_migrate_mhkn_20200825_worklist cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200825_123103_migrate_mhkn_20200825_worklist cannot be reverted.\n";

        return false;
    }
    */
}
