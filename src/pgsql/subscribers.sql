-- Table: subscribers

-- DROP TABLE subscribers;

CREATE TABLE subscribers
(
  host_account text NOT NULL,
  subscriber text NOT NULL,
  CONSTRAINT subscribers_pk PRIMARY KEY (host_account, subscriber),
  CONSTRAINT host_account_fk FOREIGN KEY (host_account)
      REFERENCES users (email) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE subscribers
  OWNER TO pg_sql_account_here;
COMMENT ON TABLE subscribers
  IS 'Table for managing subscribers for channels';
