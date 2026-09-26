<?php

use yii\db\Migration;

/**
 * Class m190114_042138_create_table_syncpasien_t
 */
class m190114_042138_create_table_syncpasien_t extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
         $this->execute('
            CREATE SEQUENCE syncpasien_t_syncpasien_id_seq
                INCREMENT 1
                MINVALUE 1
                MAXVALUE 9223372036854775807
                START 1
                CACHE 1;
        ');
        $this->execute('
            ALTER TABLE syncpasien_t_syncpasien_id_seq
                OWNER TO postgres;
        ');
        $this->execute("
            CREATE TABLE IF NOT EXISTS syncpasien_t
                (
                syncpasien_id integer NOT NULL DEFAULT nextval('syncpasien_t_syncpasien_id_seq'::regclass),
                pasien_id integer,
                additional_sync text,
                is_sync boolean DEFAULT false,
                CONSTRAINT syncpasien_t_prkey PRIMARY KEY (syncpasien_id)
                )
            WITH (
                OIDS=FALSE
            );
        ");
        $this->execute('
            ALTER TABLE syncpasien_t
            OWNER TO postgres;
        ');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190114_042138_create_table_syncpasien_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190114_042139_create_table_syncpasien_t cannot be reverted.\n";

        return false;
    }
    */
}
