<?php

use yii\db\Migration;

/**
 * Class m231026_092454_rpp_746_statuspasien_konfigantrian
 */
class m231026_092454_rpp_746_statuspasien_konfigantrian extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.konfigantrian_m ADD COLUMN IF NOT EXISTS statuspasien int4 NULL;");

        $this->execute("COMMENT ON COLUMN konfigantrian_m.statuspasien IS 'lookup_type = status_pasien'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231026_092454_rpp_746_statuspasien_konfigantrian cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231026_092454_rpp_746_statuspasien_konfigantrian cannot be reverted.\n";

        return false;
    }
    */
}
