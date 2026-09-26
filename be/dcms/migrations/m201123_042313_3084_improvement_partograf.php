<?php

use yii\db\Migration;

/**
 * Class m201123_042313_3084_improvement_partograf
 */
class m201123_042313_3084_improvement_partograf extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE persalinan_t ADD IF NOT EXISTS jenis_persalinan int4;
        '); 
        
        $this->execute('
            select public.deps_save_and_drop_dependencies(\'public\', \'persalinan_t\');
        '); 
        
        $this->execute('
            ALTER TABLE "public"."persalinan_t" ALTER COLUMN "penolong" TYPE int4 USING "penolong"::int4;
        '); 
        
        $this->execute('
            select public.deps_restore_dependencies(\'public\', \'persalinan_t\');
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201123_042313_3084_improvement_partograf cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201123_042313_3084_improvement_partograf cannot be reverted.\n";

        return false;
    }
    */
}
