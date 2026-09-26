<?php

use yii\db\Migration;

/**
 * Class m210916_084551_improvment_triase_US1263
 */
class m210916_084551_improvment_triase_US1263 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE triase_t ADD IF NOT EXISTS disability   TEXT;
        ');

        $this->execute('
            ALTER TABLE triase_t ADD IF NOT EXISTS is_doa   BOOLEAN DEFAULT FALSE;
        ');

        $this->execute('
            ALTER TABLE triase_t ADD IF NOT EXISTS tekanan_darah_sistolik   VARCHAR(10);
        ');

        $this->execute('
            ALTER TABLE triase_t ADD IF NOT EXISTS tekanan_darah_diastolik  VARCHAR(10);
        ');

        $this->execute('
            ALTER TABLE triase_t ADD IF NOT EXISTS observation_site VARCHAR(50);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210916_084551_improvment_triase_US1263 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210916_084551_improvment_triase_US1263 cannot be reverted.\n";

        return false;
    }
    */
}
