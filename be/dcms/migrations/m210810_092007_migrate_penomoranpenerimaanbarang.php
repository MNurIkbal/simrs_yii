<?php

use yii\db\Migration;

/**
 * Class m210810_092007_migrate_penomoranpenerimaanbarang
 */
class m210810_092007_migrate_penomoranpenerimaanbarang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER if exists "no_penerimaanbarang" ON "public"."penerimaanbarang_t";');

        $this->execute('CREATE TRIGGER "no_penerimaanbarang" BEFORE INSERT ON "public"."penerimaanbarang_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."no_penerimaanbarang"();');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210810_092007_migrate_penomoranpenerimaanbarang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210810_092007_migrate_penomoranpenerimaanbarang cannot be reverted.\n";

        return false;
    }
    */
}
