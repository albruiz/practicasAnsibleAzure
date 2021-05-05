#!/bin/bash

# Obtener la ip de una maquina virtual creada 
# Parametros:
#		- $1 = grupo de recursos que almacena la maquina virtual
#		- $2 = nombre de la maquina virtual de la que se quiere obtener la ip

PIP=$(az vm show --show-details --resource-group $1 --name $2 --query publicIps --output tsv)
echo $PIP
