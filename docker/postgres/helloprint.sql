-- Create the schema that we'll use to populate data and watch the effect in the binlog
CREATE SCHEMA IF NOT EXISTS helloprint AUTHORIZATION hellouser;
SET search_path TO helloprint;

-- enable PostGis 
-- CREATE EXTENSION postgis;

-- Create table
CREATE TABLE requests (
  id SERIAL NOT NULL PRIMARY KEY,
  message VARCHAR(255) NOT NULL,
  response VARCHAR(255) NULL
);