-- Create the schema that we'll use to populate data and watch the effect in the binlog
CREATE SCHEMA IF NOT EXISTS helloprint AUTHORIZATION hellouser;
SET search_path TO helloprint;

-- Create table
CREATE TABLE requests (
  id VARCHAR(50) PRIMARY KEY NOT NULL,
  message VARCHAR(255) NOT NULL
);