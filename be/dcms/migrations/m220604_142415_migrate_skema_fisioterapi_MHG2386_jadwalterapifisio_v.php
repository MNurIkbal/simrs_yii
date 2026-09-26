<?php

use yii\db\Migration;

/**
 * Class m220604_142415_migrate_skema_fisioterapi_MHG2386_jadwalterapifisio_v
 */
class m220604_142415_migrate_skema_fisioterapi_MHG2386_jadwalterapifisio_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."jadwalterapifisio_t" 
            ADD COLUMN IF NOT EXISTS "keterangan_drop_out" text COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "soapfisioterapi_id" int4;
            ');

        $this->execute('DROP VIEW if exists public.jadwalterapifisio_v;');
        $this->execute("
            CREATE VIEW \"public\".\"jadwalterapifisio_v\" AS
            SELECT jadwalterapifisio_t.jadwalterapifisio_id,
            jadwalterapifisio_t.programterapi_id,
            jadwalterapifisio_t.programterapidetail_id,
            jadwalterapifisio_t.pasien_id,
            jadwalterapifisio_t.pendaftaran_id,
            jadwalterapifisio_t.tgl_penjadwalan_awal,
            jadwalterapifisio_t.tgl_penjadwalan_akhir,
            jadwalterapifisio_t.tgl_realisasi,
            jadwalterapifisio_t.is_active,
            jadwalterapifisio_t.created_date,
            jadwalterapifisio_t.kunjunganke,
            jadwalterapifisio_t.status_kunjungan_fisio AS status_kunjungan_fisio_id,
            look_status_kunjungan_fisio.lookup_name AS status_kunjungan_fisio_nama,
            jadwalterapifisio_t.pegawai_id,
            pegawai_m.nama_pegawai AS pegawai_nama,
            jadwalterapifisio_t.tgl_penjadwalan,
            jadwalterapifisio_t.keterangan_drop_out
            FROM ((jadwalterapifisio_t
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) look_status_kunjungan_fisio ON ((jadwalterapifisio_t.status_kunjungan_fisio = look_status_kunjungan_fisio.lookup_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((jadwalterapifisio_t.pegawai_id = pegawai_m.pegawai_id)))
            WHERE ((jadwalterapifisio_t.is_active = true) AND (jadwalterapifisio_t.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.jadwalterapifisio_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220604_142415_migrate_skema_fisioterapi_MHG2386_jadwalterapifisio_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220604_142415_migrate_skema_fisioterapi_MHG2386_jadwalterapifisio_v cannot be reverted.\n";

        return false;
    }
    */
}
