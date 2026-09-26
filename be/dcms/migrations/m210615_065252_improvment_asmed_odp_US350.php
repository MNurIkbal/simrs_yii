<?php

use yii\db\Migration;

/**
 * Class m210615_065252_improvment_asmed_odp_US350
 */
class m210615_065252_improvment_asmed_odp_US350 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pemeriksaanfisik_t ADD IF NOT EXISTS kesadaran TEXT;
        ');

        $this->execute('
            ALTER TABLE pemeriksaanfisik_t ADD IF NOT EXISTS kategori_nadi TEXT;
        ');

        $this->execute('
            ALTER TABLE pemeriksaanfisik_t ADD IF NOT EXISTS kategori_pernapasan TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210615_065252_improvment_asmed_odp_US350 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210615_065252_improvment_asmed_odp_US350 cannot be reverted.\n";

        return false;
    }
    */
}
