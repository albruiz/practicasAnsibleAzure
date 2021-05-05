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
    
    # lectura de datos de la conexion ssh a la máquina controladora de Azure: datosConexion.txt
    $ruta='datosConexion.txt';
    $archivoDatos = fopen($ruta,"r");
    $host='';
    $port="";
    $username="";
    $password="";

    # eliminar espacios del final de las variables leidas
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
        }
    }
    fclose($archivoDatos);

    $nombreServidor = $_POST['nombreServidor'];
    $nombreBaseDatos = $_POST['nombreBD'];
    $tipoBaseDatos = $_POST['tipo'];
    $fichero = 'carga.yml';
    $data='';

    #creacion de los posibles archivos dependiendo del servidor DB elegido
    switch($tipoBaseDatos){
        case('PostgresSQL'):
            $data = "# Description
# ===========
# This playbook create a PostgreSQL server and an instance of PostgreSQL Database

---
- hosts: localhost
  tasks:
    
    - name: Create PostgreSQL Server
      azure_rm_postgresqlserver: # creacion del servidor Postgres sobre el que trabajamos
        resource_group: ".$grupoRecursos."
        name: ".$nombreServidor."
        sku:
            name: B_Gen5_1 #Tipo de servidor, hay varios tipos, no todos estan soportados por la localizacion del grupo de recursos
            tier: Basic
            capacity: 1
        location: ".$location."
        enforce_ssl: True
        admin_username: ".$username."
        admin_password: ".$password."
        storage_mb: 51200

    - name: Create instance of PostgreSQL Database 
      azure_rm_postgresqldatabase: #Creamos la base de datos en el servidor anteriormente creado
        resource_group: ".$grupoRecursos."
        server_name: ".$nombreServidor."
        name: ".$nombreBaseDatos."
        
    - name: Create (or update) PostgreSQL firewall rule
      azure_rm_postgresqlfirewallrule: #Sera necesario para poder acceder al servidor
        resource_group: ".$grupoRecursos."
        server_name: ".$nombreServidor."
        name: rule1
  #variaran los rangos de las Ips, para permitir a unos u a otros
        start_ip_address: 0.0.0.0 
        end_ip_address: 255.255.255.255";
            break; 

        case('MySQL'):
            $data="# Description
# ===========
# This playbook create a MySQL server and an instance of MySQL Database,

---
- hosts: localhost
  tasks:

    - name: Create MySQL Server
      azure_rm_mysqlserver: #Creacion del servidor MySQL
        resource_group: ".$grupoRecursos."
        name: ".$nombreServidor."
        sku:
            name: GP_Gen5_2 # No todos los tipos de Servidores de DB están soportados en las localizaciones del grupo de recursos
            tier: GeneralPurpose
        location: ".$location."
        version: 5.6
        enforce_ssl: True
        admin_username: ".$username."
        admin_password: ".$password."
        storage_mb: 51200

    - name: Create instance of MySQL Database
      azure_rm_mysqldatabase: # Creacion de la instancia de la DB en el servidor
        resource_group: ".$grupoRecursos."
        server_name: ".$nombreServidor."
        name: ".$nombreBaseDatos."

    - name: Open firewall to access MySQL Server from outside
      azure_rm_resource: # Permitir el acceso al servidor creado
        api_version: '2017-12-01'
        resource_group: ".$grupoRecursos."
        provider: dbformysql
        resource_type: servers
        resource_name: ".$nombreServidor."
        subresource:
          - type: firewallrules
            name: externalaccess
        body:
          properties:
  #variaran los rangos de las Ips, para permitir a unos u a otros
            startIpAddress: \"0.0.0.0\"
            endIpAddress: \"255.255.255.255\"";
            break;
    }

    # se han creado los archivo que se enviarán a la DB y se ejecutaran
    file_put_contents($fichero, $data);
    # creacion de la conexion SCP y envio de archivos
    if(!ssh2_scp_send($conexionDirecta,$fichero,'/home/azureuser/'.$fichero,0644)){
        throw new Exception('No se puede cargar el archivo en remoto');
    }else{
        print_r("si que se ha podido hacer el SCP del archivo");
    }
    
    $variable = ssh2_exec($conexionDirecta,  "ansible-playbook /home/azureuser/".$fichero." >> archivoSolucion.txt 2>&1");

    if(!$variable){
        throw new Exception('No se puede ejecutar el comando');
    }else{
        print_r("Tras la ejecucion del comando esto funka");
    }

    echo "</body>";
?>