<?php

use yii\db\Migration;

/**
 * Class m230304_022244_alter_klaiminacbg_namadokter
 */
class m221213_022244_alter_klaiminacbg_namadokter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS nama_dokter varchar(255) NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230304_022244_alter_klaiminacbg_namadokter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230304_022244_alter_klaiminacbg_namadokter cannot be reverted.\n";

        return false;
    }
    */
}
