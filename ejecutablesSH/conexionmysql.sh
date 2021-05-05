#!/bin/bash

# Conectar con el servidor de base de datos MySQL indicado, si se usa otro usuario simplemente se cambia el usuario
# Parametros:
#		- $1 = nombre del servidor MySql con el que queremos conectar

mysql -h $1.mysql.database.azure.com -u usuario1@$1 -p
