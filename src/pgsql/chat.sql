-- Table: chat

-- DROP TABLE chat;

CREATE TABLE chat
(
  id serial NOT NULL,
  channel_email text,
  sender text,
  message text,
  "timestamp" bigint,
  type text, -- Valid params are USER or SYSTEM currently
  CONSTRAINT pk_id PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE chat
  OWNER TO pg_sql_account_here;
COMMENT ON COLUMN chat.type IS 'Valid params are USER or SYSTEM currently';

