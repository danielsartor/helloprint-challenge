# helloprint-challenge (UNDER DEVELOPMENT)

## REQUIREMENTS
- Docker
- Docker Compose

## SETUP ENVIROMENT
Open your terminal and run `docker-compose up -d`

When the installation is done run the following command to create the connect configuration.
`curl -i -X POST -H "Accept:application/json" -H "Content-Type:application/json" 127.0.0.1:8083/connectors/ -d "{ \"name\": \"helloprint-connector\", \"config\": { \"connector.class\": \"io.debezium.connector.postgresql.PostgresConnector\", \"tasks.max\": \"1\", \"database.hostname\": \"postgres\", \"database.port\": \"5432\", \"database.user\": \"hellouser\", \"database.password\": \"hellopass\", \"database.dbname\" : \"helloprint\", \"database.server.name\": \"helloprint\", \"database.whitelist\": \"helloprint\", \"database.history.kafka.bootstrap.servers\": \"kafka:9092\", \"database.history.kafka.topic\": \"schema-changes.helloprint\" } }"`

The command will be executed correctly when you receive the following response.
`HTTP/1.1 201 Created
Date: Tue, 28 Jul 2020 19:35:16 GMT
Location: http://127.0.0.1:8083/connectors/helloprint-connector
Content-Type: application/json
Content-Length: 516
Server: Jetty(9.4.24.v20191120)`

## HOW TO EXECUTE
Open 3 Terminals and execute each command in a terminal.
Terminal 1: `exec php php /usr/helloprint/public/ServiceA.php`
Terminal 2: `exec php php /usr/helloprint/public/ServiceB.php`
Terminal 3: `exec php php /usr/helloprint/public/Requester.php`