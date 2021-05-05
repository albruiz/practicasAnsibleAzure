#!/bin/bash

# Conectar con el servidor postgreSQL indicado y dentro de ese servidor con la base de datos que se quiera
# Parametros: 
# 		- $1 = nombre del servidor postgres (recien creado o existente)
#		- $2 = nombre de la base de datos a la que quieres acceder, tiene que pertenecer a este servidor

psql --host=$1.postgres.database.azure.com --port=5432 --username=usuario1@$1 --dbname=$2
