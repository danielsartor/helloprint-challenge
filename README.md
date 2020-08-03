# helloprint-challenge (UNDER DEVELOPMENT)

## REQUIREMENTS
- Docker
- Docker Compose

## SETUP ENVIROMENT  
Open your terminal and run the following command  
`docker-compose up -d`  

When the installation is done run the following commands from the CONFIG folder to create the connect configuration.

Postgres Source  
`curl -X POST -H "Accept:application/json" -H "Content-Type: application/json" --data @postgres-source.json http://localhost:8083/connectors`  

Postgres Sink  
`curl -X POST -H "Accept:application/json" -H "Content-Type: application/json" --data @postgres-sink.json http://localhost:8083/connectors`  

Run the following command to check if both connectors were created.  
`curl -H "Accept:application/json" localhost:8083/connectors/`  

## HOW TO EXECUTE
Open 4 Terminals and execute each command in a terminal.  
Terminal 1: `exec php php /usr/helloprint/start/connector.php`.  
Terminal 2: `exec php php /usr/helloprint/start/servicea.php`.  
Terminal 3: `exec php php /usr/helloprint/start/serviceb.php`.  
Terminal 4: `exec php php /usr/helloprint/start/requester.php`.  
