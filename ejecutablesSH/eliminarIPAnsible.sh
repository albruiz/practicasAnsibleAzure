#!/bin/bash

## Eliminar una IP publica para liberar espacio porque no se puede tener mas, o nos sobran
## Se hace con Ansible, de manera remota.

#	 Parametros:
#		- $1 = grupo de recursos
#		- $2 = nombre de la direccion publica

echo "---
- hosts: localhost

  tasks:
  - name: Eliminacion de una IP publica que sobra
    azure_rm_publicipaddress:
      resource_group: $1
      name: $2
      state: absent" >> eliminaIPpublicaAuto.yml

ansible-playbook eliminaIPpublicaAuto.yml

rm eliminaIPpublicaAuto.yml

