<?php

use yii\db\Migration;

/**
 * Class m210105_103206_migrate_sy_20210105_view_penanggungbiaya_v
 */
class m210105_103206_migrate_sy_20210105_view_penanggungbiaya_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.penanggungbiaya_v;');
        $this->execute("CREATE VIEW \"public\".\"penanggungbiaya_v\" AS
           SELECT penanggungbiaya_t.penanggungbiaya_id,
    penanggungbiaya_t.pasien_id,
    penanggungbiaya_t.carabayar_id,
    penanggungbiaya_t.penanggungbiaya_nama,
    penanggungbiaya_t.namabagian,
    penanggungbiaya_t.noindukkaryawan,
    penanggungbiaya_t.jpkm,
    penanggungbiaya_t.instansi
   FROM (penanggungbiaya_t
     LEFT JOIN pendaftaran_t ON ((penanggungbiaya_t.pasien_id = pendaftaran_t.pasien_id)))  
            ;");
            $this->execute('ALTER TABLE public.penanggungbiaya_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210105_103206_migrate_sy_20210105_view_penanggungbiaya_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210105_103206_migrate_sy_20210105_view_penanggungbiaya_v cannot be reverted.\n";

        return false;
    }
    */
}
