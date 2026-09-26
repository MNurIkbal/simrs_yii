<?php

use yii\db\Migration;

/**
 * Class m220624_120540_migrate_cssd_infocssdsterilisasi_v
 */
class m220624_120540_migrate_cssd_infocssdsterilisasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infocssdsterilisasi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infocssdsterilisasi_v\" AS
            SELECT cssdsterilisasi_t.cssdsterilisasi_id,
            cssdsterilisasi_t.tgl_sterilisasi,
            cssdsterilisasi_t.no_sterilisasi,
            cssdsterilisasi_t.status_cssd AS status_cssd_id,
            status_cssd.lookup_name AS status_cssd_nama,
            cssdrusak_t.tgl_selesai AS tgl_sterilisasi_selesai,
            cssdrusak_t.catatan
            FROM ((cssdsterilisasi_t
            JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) status_cssd ON ((cssdsterilisasi_t.status_cssd = status_cssd.lookup_id)))
            LEFT JOIN ( SELECT a.cssdsterilisasi_id,
            a.tgl_selesai,
            a.catatan
            FROM cssdrusak_t a) cssdrusak_t ON ((cssdsterilisasi_t.cssdsterilisasi_id = cssdrusak_t.cssdsterilisasi_id)))
            WHERE ((cssdsterilisasi_t.is_active = true) AND (cssdsterilisasi_t.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.infocssdsterilisasi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_120540_migrate_cssd_infocssdsterilisasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_120540_migrate_cssd_infocssdsterilisasi_v cannot be reverted.\n";

        return false;
    }
    */
}
