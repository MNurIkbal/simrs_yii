<?php

use yii\db\Migration;

/**
 * Class m241114_033947_migrate_DSV1498_pemeriksaanfisik_t
 */
class m241114_033947_migrate_DSV1498_pemeriksaanfisik_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE pemeriksaanfisik_t ADD IF NOT EXISTS pemeriksaan_spesialis TEXT;");
        $this->execute("ALTER TABLE pemeriksaanfisik_t ADD IF NOT EXISTS konsulpoli_id int4;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241114_033947_migrate_DSV1498_pemeriksaanfisik_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241114_033947_migrate_DSV1498_pemeriksaanfisik_t cannot be reverted.\n";

        return false;
    }
    */
}
