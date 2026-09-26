<?php

use yii\db\Migration;

/**
 * Class m251014_080001_feature_eklaim_schema
 */
class m251014_080001_feature_eklaim_schema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.sy_kunjungan ADD IF NOT EXISTS status_idrg int4 NULL;");

        $this->execute("ALTER TABLE public.sy_kunjungan ADD IF NOT EXISTS status_inacbg int4 NULL;");

        $this->execute("ALTER TABLE public.sy_klaiminacbg ADD IF NOT EXISTS spesial_cmg_option text NULL;");

        $this->execute('ALTER TABLE public.diagnosa_m ADD IF NOT EXISTS "type" varchar(10) NULL;');

        $this->execute("ALTER TABLE public.diagnosa_m ADD IF NOT EXISTS accpdx varchar(10) NULL;");

        $this->execute("ALTER TABLE public.diagnosa_m ADD IF NOT EXISTS asterik int4 NULL;");

        $this->execute("ALTER TABLE public.diagnosa_m ADD IF NOT EXISTS validcode_idrg int4 DEFAULT 0 NULL;");

        $this->execute("ALTER TABLE public.sy_koreksidiagnosa ADD IF NOT EXISTS is_idrg bool DEFAULT false NULL;");

        $this->execute("ALTER TABLE public.sy_koreksidiagnosa ADD IF NOT EXISTS multiplicity int4 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251014_080001_feature_eklaim_schema cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251014_080001_feature_eklaim_schema cannot be reverted.\n";

        return false;
    }
    */
}
