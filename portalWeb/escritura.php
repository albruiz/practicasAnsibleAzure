
<?php
  echo "<head>";
  echo "<!-- Required meta tags -->";
  echo "<meta charset=\"utf-8\">";
  echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";

  echo "<!-- Bootstrap CSS -->";
  echo "<link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6\" crossorigin=\"anonymous\">";
  echo "<title>Creando Archivo Para Ejecutar</title>";

  echo "</head>";
  echo "<body>";
  echo "<h2>Redacción del archivo Ansible</h2>";

  set_time_limit(300);
  /*
    * Funcion que pasa un String a un Array y lo devuelve. La forma del String es la siguiente: 1,2,3,4. 
    * Funcionamiento: elimina las comas y lo introduce en un array que inicialmente se ha declarado vacio.
    * Parametro: El String que tiene que pasar a Array
    * Returna: El array con los valores del String 
   */
  function pasarArray($cadena){
    $arrayVacio = array();
    for($i = 0; $i < strlen($cadena); $i++){
      if($i%2 === 0){
        array_push($arrayVacio, $cadena[$i]);
      }
    }
    return $arrayVacio;
  }

  # LECTURA DE DATOS DE LA CONEXION SSH DEL ARCHIVO: datosConexion.txt
  $ruta='datosConexion.txt';
  $archivoDatos = fopen($ruta,"r");
  $host='';
  $port="";
  $username="";
  $password="";

  # Elimino los espacios de los String finales que aparecen
  while(!feof($archivoDatos)){
    $linea = fgets($archivoDatos);
    $variable = explode("=", $linea);
    switch($variable[0]){
      case("host"):
        $host=$variable[1];
        $host=rtrim($host);
        break;
      case("port"):
        $port=(int)$variable[1];
        $port=rtrim($port);
        break;
      case("username"):
        $username=$variable[1];
        $username=rtrim($username);
        break;
      case("password"):
        $password=$variable[1];
        $password=rtrim($password);
        break;
    }
  }
  fclose($archivoDatos);

  # Se crea la conexion ssh con la maquina sobre la que trabajamos
  $conexionDirecta = ssh2_connect($host,$port);
  if (!$conexionDirecta){
    throw new Exception('No se puede conectar al servidor');
  }

  if(!(ssh2_auth_password($conexionDirecta, $username, $password))){
    throw new Exception('Fallo en la autenticacion');
  }

  # Se recogen los datos del servidor Azure, que esta almacenado en un archiv externo
  $ruta='datosAzure.txt';
  $archivoDatos = fopen($ruta,"r");
  $grupoRecursos='';
  $location='';
  $username='';
  $password='';
  $vnet='';
  $subnet='';

  while(!feof($archivoDatos)){
      $linea = fgets($archivoDatos);
      $variable = explode("=", $linea);
      switch($variable[0]){
        case("grupoRecursos"):
          $grupoRecursos=$variable[1];
          $grupoRecursos=rtrim($grupoRecursos);
          break;
        case("location"):
          $location=$variable[1];
          $location=rtrim($location);
          break;
        case("username"):
          $username=$variable[1];
          $username=rtrim($username);
          break;
        case("password"):
          $password=$variable[1];
          $password=rtrim($password);
          break;
        case("vnet"):
          $vnet=$variable[1];
          $vnet=rtrim($vnet);
          break;
        case("subnet"):
          $subnet=$variable[1];
          $subnet=rtrim($subnet);
          break;
      }
  }
  fclose($archivoDatos);

  # $winrmValue: es una comando que se tiene que ejecutar en una maquina windows y tiene que ir codificado en Base64 (asi lo exige la creacion de estas maquinas), tiene el valor de la codificacion
  $winrmValue='SQBuAHYAbwBrAGUALQBFAHgAcAByAGUAcwBzAGkAbwBuACAALQBDAG8AbQBtAGEAbgBkACAAKAAoAE4AZQB3AC0ATwBiAGoAZQBjAHQAIABTAHkAcwB0AGUAbQAuAE4AZQB0AC4AVwBlAGIAQwBsAGkAZQBuAHQAKQAuAEQAbwB3AG4AbABvAGEAZABTAHQAcgBpAG4AZwAoACcAaAB0AHQAcABzADoALwAvAHIAYQB3AC4AZwBpAHQAaAB1AGIAdQBzAGUAcgBjAG8AbgB0AGUAbgB0AC4AYwBvAG0ALwBhAG4AcwBpAGIAbABlAC8AYQBuAHMAaQBiAGwAZQAvAGQAZQB2AGUAbAAvAGUAeABhAG0AcABsAGUAcwAvAHMAYwByAGkAcAB0AHMALwBDAG8AbgBmAGkAZwB1AHIAZQBSAGUAbQBvAHQAaQBuAGcARgBvAHIAQQBuAHMAaQBiAGwAZQAuAHAAcwAxACcAKQApADsAIABFAG4AYQBiAGwAZQAtAFcAUwBNAGEAbgBDAHIAZQBkAFMAUwBQACAALQBSAG8AbABlACAAUwBlAHIAdgBlAHIAIAAtAEYAbwByAGMAZQA=';
  $nombreMVs = $_POST['nombreMVs'];
  $numeroMVs = $_POST['numeroMVs'];
  $sistemaOperativo = $_POST['selectorSO'];
  $data ="";

  
  for($i=0; $i < $numeroMVs; $i++){
    $fichero = "carga".$i.".yml";
    $nombreMV_minuscula = strtolower($nombreMVs);
    #muchos de los parametros tienen que ir en minuscula por eso las variables '...MINUSCULAS', en caso de que no se hagan en minuscula lanza fallo
    $parametros = array(
      "VMNAMEMINUSCULAS" => $nombreMV_minuscula.$i,
      "VMNAME" => $nombreMVs.$i,
      "GROUPNAME" => $grupoRecursos,
      "VNET" => $vnet,
      "SUBNET" => $subnet,
      "LOCATION" => $location,
      "USUARIO" => $username,
      "PASSWORD" => $password
    );
  # creacion de los posibles archivos dependiendo del SO
    switch($sistemaOperativo){
      case('LINUX'):
        $data = "- name: CREATE VM PLAYBOOK
  hosts: localhost
  connection: local
  gather_facts: False
  tasks:
    
    - name: Create storage account
      azure_rm_storageaccount: ## Creamos un almacenamiento donde iran tanto la MV como el resourceGroup 
        resource_group: ".$parametros["GROUPNAME"]."
        name: ".$parametros["VMNAMEMINUSCULAS"]." 
        account_type: Standard_LRS ## Almacenamiento local en tu region (hay varios) Premium_LRS Standard_GRS Standard_LRS Standard_RAGRS Standard_ZRS Premium_ZRS: https://docs.microsoft.com/en-us/azure/storage/common/storage-account-overview
  
    - name: Create security group that allows SSH and HTTP #firewall ademas de trabajar con ACL 
      azure_rm_securitygroup: 
        resource_group: ".$parametros["GROUPNAME"]."
        name: ".$parametros["VMNAME"]."
        rules: 
          - name: SSH  ## TIPO DE ACCESO SSH
            protocol: Tcp 
            destination_port_range: 22  
            access: Allow  
            priority: 101 
            direction: Inbound
          - name: WEB ## TIPO DE ACCESO TCP
            protocol: Tcp
            destination_port_range: 80  
            access: Allow  
            priority: 102 
            direction: Inbound

    - name: Create public IP address # Crear una ip publica para poder conectar con la MV
      azure_rm_publicipaddress: 
        resource_group: ".$parametros["GROUPNAME"]."
        allocation_method: Static 
        name: ".$parametros["VMNAME"]."
        domain_name_label: ".$parametros["VMNAMEMINUSCULAS"]."

    - name: Create NIC # Crear una interfaz de red
      azure_rm_networkinterface:
        resource_group: ".$parametros["GROUPNAME"]."
        name: ".$parametros["VMNAME"]."
        virtual_network_name: ".$parametros["VNET"]."
        subnet_name: ".$parametros["SUBNET"]."
        public_ip_name: ".$parametros["VMNAME"]." 
        security_group: ".$parametros["VMNAME"]." 

    - name: Create VM
      azure_rm_virtualmachine:
        resource_group: ".$parametros["GROUPNAME"]."
        name: ".$parametros["VMNAME"]."
        storage_account: ".$parametros["VMNAME"]."
        storage_container: ".$parametros["VMNAMEMINUSCULAS"]."
        storage_blob: ".$parametros["VMNAME"].".vhd ## ALMACENAMIENTO DEL SO
        network_interfaces: ".$parametros["VMNAME"]."
        vm_size: Standard_D2s_v3 ## tipos de máquinas cpus y ram. https://docs.microsoft.com/en-us/azure/cloud-services/cloud-services-sizes-specs
        admin_username: ".$parametros["USUARIO"]." 
        admin_password: ".$parametros["PASSWORD"]."
        image: ## SO de la MV
          offer: CentOS
          publisher: OpenLogic
          sku: '7.2'
          version: latest";
        break;

      case('WINDOWS'):
        $data = "# Description
  # ===========
  # This playbook creates an Azure Windows VM with public IP. It also cobnfigures the machine to be accessible via Ansible using WinRM. (timeout does not work)
  # This playbook originally comes from @jborean93 (https://github.com/jborean93/ansible-win-demos)
  - name: CREATE VM PLAYBOOK
    hosts: localhost
    connection: local
    gather_facts: False      
    tasks:
            
      - name: create Azure storage account
        azure_rm_storageaccount: ## Creamos un almacenamiento donde iran tanto la MV como el resourceGroup 
          resource_group: '$parametros[GROUPNAME]'
          name: '$parametros[VMNAMEMINUSCULAS]' 
          account_type: Standard_LRS ## Almacenamiento local en tu region (hay varios) Premium_LRS Standard_GRS Standard_LRS Standard_RAGRS Standard_ZRS Premium_ZRS: https://docs.microsoft.com/en-us/azure/storage/common/storage-account-overview

      - name: Create security group that allows SSH and HTTP #firewall además de trabajar con ACL 
        azure_rm_securitygroup: 
          resource_group: '$parametros[GROUPNAME]'
          name: '$parametros[VMNAME]' 
          rules: 
            - name: 'allow_rdp'
              protocol: Tcp
              destination_port_range: 3389
              access: Allow
              priority: 101
              direction: Inbound
            - name: 'allow_web_traffic'
              protocol: Tcp
              destination_port_range:
                - 80
                - 443
              access: Allow
              priority: 102
              direction: Inbound
            - name: 'allow_powershell_remoting'
              protocol: Tcp
              destination_port_range:
                - 5985
                - 5986
              access: Allow
              priority: 103
            - name: SSH
              protocol: Tcp
              destination_port_range: 22
              access: Allow
              priority: 104
              direction: Inbound

      - name: Create public IP address ## Crear una ip publica para poder conectar con la MV
        azure_rm_publicipaddress: 
          resource_group: '$parametros[GROUPNAME]'
          allocation_method: Static 
          name: '$parametros[VMNAME]'
          domain_name_label: '$parametros[VMNAMEMINUSCULAS]'

      - name: Create NIC ## Crear una interfaz de red
        azure_rm_networkinterface:
          resource_group: '$parametros[GROUPNAME]'
          name: '$parametros[VMNAME]'
          virtual_network_name: '$parametros[VNET]'
          subnet_name: '$parametros[SUBNET]'
          public_ip_name: '$parametros[VMNAME]'
          security_group: '$parametros[VMNAME]'

      - name: provision new Azure virtual host
        azure_rm_virtualmachine:
          admin_username: $parametros[USUARIO] 
          admin_password: $parametros[PASSWORD]
          os_type: Windows 
          resource_group: '$parametros[GROUPNAME]'
          name: '$parametros[VMNAME]'
          state: present 
          vm_size: Standard_D2s_v3 
          storage_account_name: '$parametros[VMNAME]' 
          network_interfaces: '$parametros[VMNAME]'
          image:
            offer: WindowsServer
            publisher: MicrosoftWindowsServer
            sku: 2016-Datacenter
            version: latest

      - name: create Azure vm extension to enable HTTPS WinRM listener     
        azure_rm_virtualmachine_extension:        
          name: winrm-extension        
          resource_group: '$parametros[GROUPNAME]'        
          virtual_machine_name: '$parametros[VMNAME]'    
          publisher: Microsoft.Compute        
          virtual_machine_extension_type: CustomScriptExtension        
          type_handler_version: 1.9        
          settings: '{\"commandToExecute\": \"powershell.exe -ExecutionPolicy ByPass -EncodedCommand ".$winrmValue."\"}'        
          auto_upgrade_minor_version: true     
        with_items: output.instances   
  #
  # ESTO ES UNA FORMA DE HACERLO AUTOMATICO PERO LAS MAQUINAS QUE LANZO NO CONSIGUEN HACERLO EN TIEMPO.
  # SOLUCION MANUAL:  http://www.hurryupandwait.io/blog/understanding-and-troubleshooting-winrm-connection-and-authentication-a-thrill-seekers-guide-to-adventure
  #    - name: wait for the WinRM port to come online     
  #      wait_for:        
  #        port: 5986        
  #        host: '{{azure_vm.properties.networkProfile.networkInterfaces[0].properties.ipConfigurations[0].properties.publicIPAddress.properties.ipAddress}}'        
  #        timeout: 300   
  #      with_items: output.instances";
        break;
    }
    //Almacenamos en el fchero seleccionado, los comandos Ansible que crearán la MV
    file_put_contents($fichero, $data);

    
    # CREAR LA CONEXION SCP PARA PASAR LOS ARCHIVOS CREADOS A LA MV PARA EJECUTARLOS
    if(!ssh2_scp_send($conexionDirecta,$fichero,'/home/azureuser/'.$fichero,0644)){
      throw new Exception('No se puede cargar el archivo en remoto');
    }

    # EJECUTAR EL COMANDO PARA CREAR LA MV
    $variable = ssh2_exec($conexionDirecta,  "ansible-playbook /home/azureuser/".$fichero." >> archivoSolucion.txt 2>&1");

    if(!$variable){
      throw new Exception('No se puede ejecutar el comando');
    }
  }

  echo "</body>";

?>