<?php

use yii\db\Migration;

/**
 * Class m220604_142455_migrate_skema_fisioterapi_MHG2386_scheduleprogramterapi_v
 */
class m220604_142455_migrate_skema_fisioterapi_MHG2386_scheduleprogramterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.scheduleprogramterapi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"scheduleprogramterapi_v\" AS
            SELECT programterapi_t.pendaftaran_id,
            programterapi_t.programterapi_id,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.jeniskelamin AS jeniskelamin_id,
            jeniskelamin.lookup_name AS jeniskelamin_nama,
            programterapi_t.tgl_permintaan,
            programterapi_t.frekuensi,
            programterapi_t.diagnosa,
            programterapi_t.catatan
            FROM ((programterapi_t
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.jeniskelamin
            FROM pasien_m a) pasien_m ON ((programterapi_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) jeniskelamin ON (((pasien_m.jeniskelamin)::integer = jeniskelamin.lookup_id)))
            WHERE ((programterapi_t.is_active = true) AND (programterapi_t.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.scheduleprogramterapi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220604_142455_migrate_skema_fisioterapi_MHG2386_scheduleprogramterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220604_142455_migrate_skema_fisioterapi_MHG2386_scheduleprogramterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
