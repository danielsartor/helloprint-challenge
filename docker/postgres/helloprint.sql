-- Create the schema
CREATE SCHEMA IF NOT EXISTS helloprint AUTHORIZATION hellouser;
SET search_path TO helloprint;

-- Create table
CREATE TABLE requests (
  id CHAR(13) PRIMARY KEY,
  message VARCHAR(255) NOT NULL
);