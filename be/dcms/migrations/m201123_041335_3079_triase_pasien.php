<?php

use yii\db\Migration;

/**
 * Class m201123_041335_3079_triase_pasien
 */
class m201123_041335_3079_triase_pasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('
            ALTER TABLE triase_t ADD IF NOT EXISTS hasil_triase TEXT;
        '); 
        
        $this->execute('
            ALTER TABLE triase_t ADD IF NOT EXISTS alergi TEXT;
        '); 
        
        $this->execute('
            ALTER TABLE "public"."triase_t" ADD IF NOT EXISTS "saturasi_oksigen" text;
        '); 
        
        $this->execute('
            ALTER TABLE triase_t ADD IF NOT EXISTS trauma TEXT;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201123_041335_3079_triase_pasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201123_041335_3079_triase_pasien cannot be reverted.\n";

        return false;
    }
    */
}
