<?php

use yii\db\Migration;

/**
 * Class m210921_144637_improvment_perubahan_trigger_US1280
 */
class m210921_144637_improvment_perubahan_trigger_US1280 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TRIGGER IF EXISTS "asesmenperawatrd_insert_r" ON "public"."asesmenperawatrd_t";
        ');

        $this->execute('
            CREATE TRIGGER "asesmenperawatrd_insert_r" AFTER INSERT OR UPDATE ON "public"."asesmenperawatrd_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."asesmenperawatrd_insert_r"();
        ');

        $this->execute('
            DROP TRIGGER IF EXISTS "asesmenperawatrd_r" ON "public"."asesmenperawatrd_t";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210921_144637_improvment_perubahan_trigger_US1280 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210921_144637_improvment_perubahan_trigger_US1280 cannot be reverted.\n";

        return false;
    }
    */
}
