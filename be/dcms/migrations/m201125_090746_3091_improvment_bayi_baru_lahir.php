<?php

use yii\db\Migration;

/**
 * Class m201125_090746_3091_improvment_bayi_baru_lahir
 */
class m201125_090746_3091_improvment_bayi_baru_lahir extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE kelahiranbayi_t ADD IF NOT EXISTS pegawai_id int4;
        '); 

         $this->execute('
            ALTER TABLE kelahiranbayi_t ADD IF NOT EXISTS kamartempattidur_id int4;
        '); 

         $this->execute('
            ALTER TABLE kelahiranbayi_t ADD IF NOT EXISTS lingkar_kepala float4;
        '); 

         $this->execute('
            ALTER TABLE kelahiranbayi_t ADD IF NOT EXISTS golongan_darah VARCHAR(30);
        '); 

         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_090746_3091_improvment_bayi_baru_lahir cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_090746_3091_improvment_bayi_baru_lahir cannot be reverted.\n";

        return false;
    }
    */
}
