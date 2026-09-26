<?php

use yii\db\Migration;

/**
 * Class m201015_024110_migrate_20201015_laporanpemakaianbmhp
 */
class m201015_024110_migrate_20201015_laporanpemakaianbmhp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpemakaianbmhp_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpemakaianbmhp_v\" AS  SELECT obatalkespasien_t.tglpelayanan AS tgl_transaksi,
    pasien_m.no_rekam_medik AS no_rm,
    pendaftaran_t.no_pendaftaran,
    concat(fgetnamalookup((pasien_m.namadepan)::integer), pasien_m.nama_pasien) AS nama_pasien,
    daftartindakan_m.daftartindakan_nama AS tindakan,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_nama,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    obatalkespasien_t.satuankecil_id,
    (satuanunit_m.satuanunit_nama)::text AS satuan_kecil_nama,
    ((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text) AS qty_input,
        CASE
            WHEN (obatalkespasien_t.qty_konversi IS NOT NULL) THEN COALESCE(obatalkespasien_t.qty_konversi, (0)::double precision)
            ELSE COALESCE(obatalkespasien_t.qty_oa, (0)::double precision)
        END AS qty,
    obatalkes_m.harganetto AS harga_netto,
        CASE
            WHEN (obatalkespasien_t.qty_konversi IS NOT NULL) THEN (COALESCE(obatalkespasien_t.qty_konversi, (0)::double precision) * obatalkes_m.harganetto)
            ELSE (COALESCE(obatalkespasien_t.qty_oa, (0)::double precision) * obatalkes_m.harganetto)
        END AS total,
        CASE
            WHEN (obatalkespasien_t.hargajual_oa = (0)::double precision) THEN false
            ELSE true
        END AS is_ditagihkan,
    obatalkespasien_t.ruangan_id
   FROM (((((((obatalkespasien_t
     JOIN pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN instruksitindakanbmhp_t ON ((obatalkespasien_t.instruksitindakanbmhp_id = instruksitindakanbmhp_t.instruksitindakanbmhp_id)))
     LEFT JOIN tindakanpelayanan_t ON ((obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id)))
     LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN satuanunit_m ON ((obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id)))
  WHERE ((obatalkespasien_t.is_deleted = false) AND ((instruksitindakanbmhp_t.status_implementasi)::text = ANY ((ARRAY['455'::character varying, '456'::character varying])::text[])));");
        
        $this->execute('ALTER TABLE "public"."laporanpemakaianbmhp_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201015_024110_migrate_20201015_laporanpemakaianbmhp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201015_024110_migrate_20201015_laporanpemakaianbmhp cannot be reverted.\n";

        return false;
    }
    */
}
