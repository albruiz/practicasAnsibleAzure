#!/bin/bash

# Eliminar el sercvidor mysql indicado
# Parametros:
#		- $1 = nombre del grupo de recursos que almacena el servidor postgres
#		- $2 = nombre del servidor MySql con el que queremos conectar

az mysql server delete -g $1 -n $2 
