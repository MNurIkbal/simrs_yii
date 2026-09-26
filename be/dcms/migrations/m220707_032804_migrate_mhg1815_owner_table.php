<?php

use yii\db\Migration;

/**
 * Class m220707_032804_migrate_mhg1815_owner_table
 */
class m220707_032804_migrate_mhg1815_owner_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."pemberianinfus_t" OWNER TO "postgres";
        ');

        $this->execute('
            ALTER TABLE "public"."pemberianinfusrespon_t" OWNER TO "postgres";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220707_032804_migrate_mhg1815_owner_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220707_032804_migrate_mhg1815_owner_table cannot be reverted.\n";

        return false;
    }
    */
}
