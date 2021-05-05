#!/bin/bash

# Script que se encarga de eliminar todos los recursos que ocupa una maquina virtual
#	Parametros:
#		- $1 = grupo de recursos
#		- $2 = nombre de la maquina virtual

echo "## Eliminar una MV
## Se hace con Ansible, de manera remota.
## @author: Alberto Ruiz

---
- hosts: localhost

  tasks:
  - name: Eliminacion de una MV
    azure_rm_virtualmachine:
      resource_group: $1
      name: $2
      remove_on_absent: all_autocreated
      state: absent
      
  - name: Eliminacion de un NIC que sobra
    azure_rm_networkinterface:
      resource_group: $1
      name: $2
      state: absent
      
  - name: Eliminacion de una IP publica que sobra
    azure_rm_publicipaddress:
      resource_group: $1
      name: $2
      state: absent
      
  - name: Eliminacion de un grupo de seguridad
    azure_rm_securitygroup:
      resource_group: $1
      name: $2
      state: absent

  - name: remove account, if it exists
    azure_rm_storageaccount:
      resource_group: $1
      name: $(echo $2 | tr '[:upper:]' '[:lower:]')
      state: absent
      force_delete_nonempty: yes" >> eliminaMVAuto.yml

ansible-playbook eliminaMVAuto.yml



rm eliminaMVAuto.yml

