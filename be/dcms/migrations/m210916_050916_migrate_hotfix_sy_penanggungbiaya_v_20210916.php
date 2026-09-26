<?php

use yii\db\Migration;

/**
 * Class m210916_050916_migrate_hotfix_sy_penanggungbiaya_v_20210916
 */
class m210916_050916_migrate_hotfix_sy_penanggungbiaya_v_20210916 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."penanggungbiaya_t" 
            ADD COLUMN IF NOT EXISTS "ruangcarabayar_id" int4;');

        $this->execute('DROP VIEW if exists public.sy_penanggungbiaya_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_penanggungbiaya_v\" AS
            SELECT penanggungbiaya_t.penanggungbiaya_id,
            penanggungbiaya_t.pasien_id,
            pasien_m.nama_pasien,
            penanggungbiaya_t.carabayar_id,
            carabayar_m.carabayar_nama,
            penanggungbiaya_t.penanggungbiaya_nama,
            penanggungbiaya_t.namabagian,
            ruangcarabayar_m.ruangcarabayar_kode AS namabagian_kode,
            penanggungbiaya_t.noindukkaryawan,
            penanggungbiaya_t.jpkm,
            penanggungbiaya_t.instansi
            FROM (((penanggungbiaya_t
            JOIN pasien_m ON ((penanggungbiaya_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN carabayar_m ON ((penanggungbiaya_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN ruangcarabayar_m ON ((penanggungbiaya_t.ruangcarabayar_id = ruangcarabayar_m.ruangcarabayar_id)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_penanggungbiaya_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210916_050916_migrate_hotfix_sy_penanggungbiaya_v_20210916 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210916_050916_migrate_hotfix_sy_penanggungbiaya_v_20210916 cannot be reverted.\n";

        return false;
    }
    */
}
