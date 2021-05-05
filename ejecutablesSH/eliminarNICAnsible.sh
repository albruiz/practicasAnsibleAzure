#!/bin/bash

echo "## Eliminar un NIC para liberar espacio porque no se puede tener mas, o nos sobran
## Se hace con Ansible, de manera remota.
## @author: Alberto Ruiz

---
- hosts: localhost

  tasks:
  - name: Eliminacion de un NIC que sobra

    azure_rm_networkinterface:
      resource_group: $1
      name: $2
      state: absent" >> eliminaNicAuto.yml

ansible-playbook eliminaNicAuto.yml

rm eliminaNicAuto.yml

