<?php

use yii\db\Migration;

/**
 * Class m190114_042131_create_table_syncpendaftaran_t
 */
class m190114_042131_create_table_syncpendaftaran_t extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('
            CREATE SEQUENCE syncpendaftaran_t_syncpendaftaran_id_seq
                INCREMENT 1
                MINVALUE 1
                MAXVALUE 9223372036854775807
                START 1
                CACHE 1;
        ');
        $this->execute('
            ALTER TABLE syncpendaftaran_t_syncpendaftaran_id_seq
                OWNER TO postgres;
        ');
        $this->execute("
            SELECT setval('public.syncpendaftaran_t_syncpendaftaran_id_seq', 1, true);
            ");
        $this->execute("
            CREATE TABLE IF NOT EXISTS syncpendaftaran_t
                (
                syncpendaftaran_id integer NOT NULL DEFAULT nextval('syncpendaftaran_t_syncpendaftaran_id_seq'::regclass),
                pendaftaran_id integer,
                additional_sync text,
                is_sync boolean DEFAULT false,
                CONSTRAINT syncpendaftaran_t_pkey PRIMARY KEY (syncpendaftaran_id)
                )
            WITH (
                OIDS=FALSE
            );
        ");
        $this->execute('
            ALTER TABLE syncpendaftaran_t
            OWNER TO postgres;
        ');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190114_042131_create_table_syncpendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190114_042131_create_table_syncpendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
