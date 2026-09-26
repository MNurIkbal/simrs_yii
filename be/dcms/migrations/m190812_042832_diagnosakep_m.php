<?php

use yii\db\Migration;

/**
 * Class m190812_042832_diagnosakep_m
 */
class m190812_042832_diagnosakep_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP SEQUENCE if  exists public.diagnosakep_m_seq;');

        $this->execute('CREATE SEQUENCE public.diagnosakep_m_seq
                        INCREMENT 1
                        MINVALUE 1
                        MAXVALUE 98418581591195
                        START 1
                        CACHE 1;
                        ');

        $this->execute('ALTER TABLE public.diagnosakep_m_seq
                        OWNER TO postgres;');

        $this->execute('DROP TABLE if exists public.diagnosakep_m;');

        $this->execute('CREATE TABLE public.diagnosakep_m
                    (
                      diagnosakep_id integer NOT NULL DEFAULT nextval(\'diagnosakep_m_seq\'::regclass),
                      diagnosakep_kode character varying(20),
                      diagnosakep_nama character varying(255) NOT NULL,
                      additional_data text,
                      created_date timestamp(6) without time zone DEFAULT now(),
                      created_by integer,
                      modified_count integer,
                      last_modified_date timestamp(6) without time zone,
                      last_modified_by integer,
                      is_deleted boolean DEFAULT false,
                      is_active boolean DEFAULT true,
                      deleted_date timestamp(6) without time zone,
                      deleted_by integer,
                      CONSTRAINT diagnosakep_m_pkey PRIMARY KEY (diagnosakep_id)
                    )
                    WITH (
                      OIDS=FALSE
                    );');

        $this->execute('ALTER TABLE public.diagnosakep_m
                        OWNER TO postgres;');

        $this->execute('ALTER SEQUENCE "public"."diagnosakep_m_seq"
                        OWNED BY "public"."diagnosakep_m"."diagnosakep_id";');

        $this->execute("
                INSERT INTO public.diagnosakep_m(diagnosakep_id, diagnosakep_kode, diagnosakep_nama, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
                (1, 'KEP1', 'Ketidak Efektifan Pembersihan Jalan Nafas', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (2, 'KEP2', 'Ketidak Efektifan Pola Napas', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (3, 'KEP3', 'Gangguan Pertukaran Gas', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (4, 'KEP4', 'Kurang Pengetahuan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (5, 'KEP5', 'RESIKO TERJADI ASPIRASI', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (6, 'KEP6', 'Hipertermia', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (7, 'KEP7', 'Ketidakseimbangan nutrisi kurang dari kebutuhan tubuh', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (8, 'KEP8', 'Defisit Volume Cairan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (9, 'KEP9', 'Kelebihan Volume Cairan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (10, 'KEP10', 'Risiko Infeksi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (11, 'KEP11', 'Intoleransi aktivitas', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (12, 'KEP12', 'Kerusakan Integritas Kulit', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (13, 'KEP13', 'Kecemasan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (14, 'KEP14', 'Takut', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (15, 'KEP15', 'Penurunan Curah Jantung', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (16, 'KEP16', 'Perfusi Jaringan Kardiopulminal Tidak Efektif', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (17, 'KEP17', 'Perfusi Jaringan Cerebral Tidak Efektif', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (18, 'KEP18', 'Perfusi Jaringan Gastrointestinal Tidak Efektif', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (19, 'KEP19', 'Perfusi Jaringan Renal Tidak Efektif', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (20, 'KEP20', 'Defisit Perawatan DIri', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (21, 'KEP21', 'Risiko Gangguan Integritas Kulit', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (22, 'KEP22', 'Ketidakseimbangan nutrisi lebih dari kebutuhan tubuh', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (23, 'KEP23', 'Nyeri Akut', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (24, 'KEP24', 'Nyeri Kronis', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (25, 'KEP25', 'Gangguan Mobilitas Fisik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (26, 'KEP26', 'Risiko Trauma / Jatuh', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (27, 'KEP27', 'Risiko Injury /. Cidera', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (28, 'KEP28', 'Mual', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (29, 'KEP29', 'Diare', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (30, 'KEP30', 'Konstipasi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (31, 'KEP31', 'Gangguan Pola Tidur', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (32, 'KEP32', 'Retensi Urin', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (33, 'KEP33', 'Kerusakan Integritas Jaringan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (34, 'KEP34', 'Gangguan Body Image / Citra Tubuh', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (35, 'KEP35', 'Manejemen Regimen Terapeutik Tidak Efektif', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (36, 'KEP36', 'Kelelahan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (37, 'KEP37', 'Risiko Kekurangan Cairan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (38, 'KEP38', 'Risiko Kerusakan Integritas Kulit', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (39, 'KEP39', 'Kerusakan Integritas Kulit', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (40, 'KEP40', 'Nyeri', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (41, 'KEP41', 'Distre Spritual', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (42, 'KEP42', 'Gangguan Ventilasi Spontan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (43, 'KEP43', 'Hipotermia', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
                (44, 'KEP44', 'Ketidak Efektifan Pola Pemberian ASI', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
                ");

        $this->execute('SELECT setval(\'diagnosakep_m_seq\', 
                        (SELECT COALESCE(MAX(diagnosakep_id) ,1)+1 FROM diagnosakep_m), false);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190812_042832_diagnosakep_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190812_042832_diagnosakep_m cannot be reverted.\n";

        return false;
    }
    */
}
