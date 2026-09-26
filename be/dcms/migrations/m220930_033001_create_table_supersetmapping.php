<?php

use yii\db\Migration;

/**
 * Class m220930_033001_create_table_supersetmapping
 */
class m220930_033001_create_table_supersetmapping extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."superset_mapping_m" (
                "superset_mapping_id" serial4 NOT NULL,
                "mapping_key" varchar NOT NULL,
                "mapping_identity" varchar NOT NULL,
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4 NULL,
                CONSTRAINT superset_mapping_m_pk PRIMARY KEY (superset_mapping_id),
                CONSTRAINT superset_mapping_m_un UNIQUE (mapping_key)
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220930_033001_create_table_supersetmapping cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220930_033001_create_table_supersetmapping cannot be reverted.\n";

        return false;
    }
    */
}
