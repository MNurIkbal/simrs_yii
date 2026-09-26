<?php

use yii\db\Migration;

/**
 * Class m210226_071248_migrate_20210226_data_penomoran
 */
class m210226_071248_migrate_20210226_data_penomoran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute('DELETE from penomoran_k WHERE penomoran_id=191;');

    $this->execute("INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(191, 'No Purchase Req Barang', 'PRB', 'PRB20212260024', '0024', '0');");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_071248_migrate_20210226_data_penomoran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_071248_migrate_20210226_data_penomoran cannot be reverted.\n";

        return false;
    }
    */
}
