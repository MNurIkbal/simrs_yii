<?php

use yii\db\Migration;

/**
 * Class m200621_075226_migrate_function_mhkn_20200621_3
 */
class m200621_075226_migrate_function_mhkn_20200621_3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION "public"."fgetpersenmargin_id"("vharga" float8, "xpenjamin_id" int4);');
        $this->execute('DROP FUNCTION "public"."infostokobatalkes_fn"("xpenjamin_id" int4);');

        $this->execute('DROP VIEW if exists "public"."infopermintaanbmhp_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopermintaanbmhp_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    (to_char(obatalkespasien_t.tglpelayanan, 'YYYY-MM-DD'::text))::date AS tgl_permintaan,
    pasien_m.nama_pasien,
    obatalkespasien_t.ruangan_id AS ruangan_tujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
        END AS ruangan_asal_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_asal_1.ruangan_nama
            ELSE ruangan_asal_2.ruangan_nama
        END AS ruangan_asal,
    obatalkespasien_t.status_bmhp AS status_bmhp_id,
    fgetnamalookup((obatalkespasien_t.status_bmhp)::integer) AS status_bmhp
   FROM ((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ruangan_tujuan ON ((obatalkespasien_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_asal_1 ON ((pendaftaran_t.ruangan_id = ruangan_asal_1.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_asal_2 ON ((pasienadmisi_t.ruangan_id = ruangan_asal_2.ruangan_id)))
  WHERE ((obatalkespasien_t.penjualanresep_id IS NULL) AND (obatalkespasien_t.is_deleted = false) AND (ruangan_tujuan.instalasi_id = 6) AND (obatalkespasien_t.status_bmhp = 679))
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, (to_char(obatalkespasien_t.tglpelayanan, 'YYYY-MM-DD'::text))::date, pasien_m.nama_pasien, obatalkespasien_t.ruangan_id, ruangan_tujuan.ruangan_nama, obatalkespasien_t.status_bmhp, pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id, ruangan_asal_1.ruangan_nama, ruangan_asal_2.ruangan_nama;");

        $this->execute('DROP VIEW if exists "public"."infopermintaanbmhpdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopermintaanbmhpdetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    obatalkespasien_t.obatalkespasien_id,
    (to_char(obatalkespasien_t.tglpelayanan, 'YYYY-MM-DD'::text))::date AS tgl_permintaan,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.status_bmhp,
    obatalkespasien_t.satuankecil_id
   FROM ((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m ruangan_tujuan ON ((obatalkespasien_t.ruangan_id = ruangan_tujuan.ruangan_id)))
  WHERE ((obatalkespasien_t.penjualanresep_id IS NULL) AND (obatalkespasien_t.is_deleted = false) AND (ruangan_tujuan.instalasi_id = 6));");
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200621_075226_migrate_function_mhkn_20200621_3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200621_075226_migrate_function_mhkn_20200621_3 cannot be reverted.\n";

        return false;
    }
    */
}
