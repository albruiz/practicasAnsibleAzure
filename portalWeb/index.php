

<!-- Alberto Ruiz Andrés-->

<!-- Boostrap trabaja con contenedores("container"), sobre ellos se pueden crear diversos container y sobre ellos filas y columnas, esa es la estructura básica de este esqueleto de portal web --->
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <title>Formulario de prueba para reservar MVs</title>

  </head>
  <body>
    <!-- Título de la página -->
    <div class="container-lg" style="background-color:#F2F4FF">
        <div class="row justify-content-md-center">
            <div class="col-3">
                <img src="imagenes/nuevoLogo.png" class="rounded float-start"  width="275" height="auto">
            </div>
            <div class="col-9">
                <p class="fs-1 text-center fw-bold">PORTAL WEB SERVICIOS IAAS Y PAAS</p>
            </div>
        </div>
    </div>
    
    <!-- Contenedor principal que almacena el menú de selección si IaaS o PaaS -->
    <div class="container" style="background-color:#F2F4FF">
        <!-- Menu de seleccion -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="IaaS_tab" data-bs-toggle="tab" data-bs-target="#iaasForm" type="button" role="tab" aria-controls="iaas" aria-selected="true">IaaS</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="PaaS-tab" data-bs-toggle="tab" data-bs-target="#paasForm" type="button" role="tab" aria-controls="paas" aria-selected="false">PaaS</button>
            </li>
        </ul>

        <!-- Primera opcion del menu que contiene las opciones de IaaS -->
        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active mt-3" id="iaasForm" role="tabpanel" aria-labelledby="IaaS_tab">
                <div class="container">
                    <!-- Comienza el formulario de datos IaaS -->
                    <form name="formularioDatos" id="formularioDatos"> 
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-2 ">
                                    <label for="nombreMVS" class="form-label">Nombre de la MV</label>
                                </div>
                                <div class="col-auto "> 
                                    <input type="string" class="form-control" id="nombreMVs" name="nombreMVs">
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-2 ">
                                    <label for="sistemaOperativo" class="form-label">Sistema Operativo:</label>
                                </div>
                                <div class="col-auto ">
                                    <select class="form-select"  aria-label="Default select example" id="selectorSO">
                                        <option value="LINUX" selected>LINUX</option>
                                        <option value="WINDOWS">WINDOWS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-2 ">
                                    <label for="numeroMaquinas" class="form-label">Número de máquinas:</label>
                                </div>
                                <div class="col-auto ">
                                    <select class="form-select"  aria-label="Default select example" id="numeroMVs">
                                        <option value="1" selected>1</option>
                                        <option value="2">2</option>
                                    </select>
                                </div>                    
                            </div>
                        </div>
                    </form> 
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col-2 ">
                                <button class="btn btn-primary  mb-3" name="botonEnviar" id="boton1" onclick="recogeDatos()">Enviar</button>
                            </div>
                        </div>
                    </div>
                        <script languaje="javascript">                            
                            /*
                            *    Función que recoge los datos del formulario, mediante el evento onClick del botón del formulario. Una vez tiene estos datos está listo para hacer la llamada a 
                            *    AJAX y hacer el envio de datos al servidor de Azure.
                            */
                            function recogeDatos(){
                                var nombreMaquina = document.getElementById("nombreMVs").value;
                                var sistemaOperativo = document.getElementById("selectorSO").value;
                                var numeroMVs = document.getElementById("numeroMVs").value;
                                                                
                                var n1 = nombreMaquina;
                                var n2 = numeroMVs;
                                var n3 = sistemaOperativo;

                                var informacion = "nombreMVs="+n1+"&numeroMVs="+n2+"&selectorSO="+n3;
                                
                                    // Llamada a Ajax con todos los elementos preparados
                                $.ajax({
                                url: "./escritura.php", 
                                type: "POST",  
                                dataType: "text",
                                data: informacion, 
                                }).done(function(resultado){ 
                            
                                });
                            }
                        </script>
                    
                </div>
            </div>

            <!-- Segunda opcion del menu que contiene las opciones de PaaS -->
            <div class="tab-pane fade  mt-3" id="paasForm" role="tabpanel" aria-labelledby="PaaS-tab">
            <div class="container">
                <div class="container">
                    <form name="formularioDatosPaas" id="formularioDatosPaas">
                        <div class="row mb-3">
                            <div class="col-3">
                                <label for="nombreServidorDB" class="form-label">Nombre del servidor de Bases de Datos</label>
                            </div>
                            <div class="col-auto">
                                <input type="string" class="form-control" id="nombreServidorDB" name="nombreServidorDB">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-3">
                                <label for="nombreDB" class="form-label">Nombre de la Base de Datos</label>
                            </div>
                            <div class="col-auto">
                                <input type="string" class="form-control" id="nombreDB" name="nombreDB">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-3">
                                <label for="tipoServidorDB" class="form-label">Tipo de servidor</label>
                            </div>
                            <div class="col-auto ">
                                <select class="form-select" aria-label=".form-select-lg example" id="selectorDB">
                                    <option selected="true" disabled="disabled">Elija una opción...</option>
                                    <option value="PostgresSQL">PostgresSQL</option>
                                    <option value="MySQL">MySQL</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <!-- Botón de envio de datos -->
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col-2 ">
                                <button class="btn btn-primary mb-3" id="boton3" onclick="formularioPaas()">Enviar</button> 
                            </div>
                        </div>
                    </div>
                </div>
                <script languaje="javascript">
                    /*
                            Función que envia mediante Ajax los datos recogidos para la creacion del archivo generador de los servicios de PaaS 
                    */
                    function formularioPaas(){
                        var nombreServidor = document.getElementById("nombreServidorDB").value;
                        var nombreBaseDatos = document.getElementById("nombreDB").value;
                        var tipoBaseDatos = document.getElementById("selectorDB").value; 

                        var informacion = "nombreServidor="+nombreServidor+"&nombreBD="+nombreBaseDatos+"&tipo="+tipoBaseDatos;

                        // Llamada a ajax
                        $.ajax({
                            url: "./escritura2.php",
                            type: "POST",
                            data: informacion,
                        }).done(function(resultado1){

                        });
                    }

                </script>
            </div>
            </div>
        </div>
    </div>      

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
    <script languaje="javascript"> //funcion por defecto del menu de seleccion de Boostrap
            var triggerTabList = [].slice.call(document.querySelectorAll('#myTab'))
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)

                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
    </script> 


    </body>
</html>

