<?php

use yii\db\Migration;

/**
 * Class m201208_032240_inprove_kesimpulanrd_update_column
 */
class m201208_032240_inprove_kesimpulanrd_update_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            select public.deps_save_and_drop_dependencies(\'public\', \'kesimpulanrd_t\');
        ');

        $this->execute('
            ALTER TABLE "public"."kesimpulanrd_t" ALTER COLUMN "t" TYPE varchar(30) USING "t"::varchar(30);
        ');

        $this->execute('
            ALTER TABLE "public"."kesimpulanrd_t" ALTER COLUMN "hasil_gcs" TYPE varchar(30) USING "hasil_gcs"::varchar(30);
        ');

        $this->execute('
            select public.deps_restore_dependencies(\'public\', \'kesimpulanrd_t\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201208_032240_inprove_kesimpulanrd_update_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201208_032240_inprove_kesimpulanrd_update_column cannot be reverted.\n";

        return false;
    }
    */
}
