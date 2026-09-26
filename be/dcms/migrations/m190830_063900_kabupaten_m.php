<?php

use yii\db\Migration;

/**
 * Class m190830_063900_kabupaten_m
 */
class m190830_063900_kabupaten_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."kabupaten_m"
                          DROP CONSTRAINT "fk_kabupaten_propinsi", 
                          ALTER COLUMN "created_date" DROP NOT NULL,
                          ALTER COLUMN "last_modified_date" DROP NOT NULL,
                          ALTER COLUMN "last_modified_date" DROP DEFAULT,
                          ALTER COLUMN "is_deleted" DROP NOT NULL,
                          ALTER COLUMN "is_active" DROP NOT NULL,
                          ALTER COLUMN "deleted_date" DROP NOT NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190830_063900_kabupaten_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190830_063900_kabupaten_m cannot be reverted.\n";

        return false;
    }
    */
}
