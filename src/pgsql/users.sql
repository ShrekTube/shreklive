-- Table: users

-- DROP TABLE users;

CREATE TABLE users
(
  email           text NOT NULL,
  password        text,
  auth_code       text,
  verified        integer,
  channel_name    text,
  channel_title   text,
  stream_key      character(10),
  display_name    text,
  profile_img     TEXT NOT NULL DEFAULT '/profiles/default/profile_default.png' :: TEXT,
  api_key         text,
  chat_jp_setting TEXT,
  is_admin        BOOLEAN,
  offline_image   TEXT NOT NULL DEFAULT '/profiles/default/offline_default.jpg' :: TEXT,
  CONSTRAINT plk_email PRIMARY KEY (email)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE users
  OWNER TO pg_sql_account_here;
COMMENT ON TABLE users
  IS 'Table for managing user information/data';
