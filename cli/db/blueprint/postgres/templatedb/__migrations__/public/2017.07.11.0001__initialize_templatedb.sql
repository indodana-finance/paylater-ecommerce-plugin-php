CREATE TABLE sample (
    id character varying NOT NULL,
    is_active boolean DEFAULT false,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    created_by character varying DEFAULT 'postgres'::character varying NOT NULL,
    last_modified_at timestamp without time zone DEFAULT now() NOT NULL,
    last_modified_by character varying DEFAULT 'postgres'::character varying NOT NULL
);