<?php

/*
 ###### PENDIENTE  DE HACER #########
        --------------------
 
 - 1.0 Añadir seccion de administradores, donde dar de baja y crear usuarios
 - 1.1 Añadir sección de busqueda de en listados de usuarios para los Admin (En la seccion donde crean los usuarios 1.0)
 
 - 2.0 Crear vista de factura real del cliente.
 - 2.1 Descargar vista de factura real del cliente en PDF.
 
 - 3.0 Sección de Admin, donde pueden subir logo de la empresa y cambiar detalles.
 - 3.1 Añadir logo de la empresa a la factura a descargar por el cliente (2.1).
 
 */

/*
-Ejemplos de como hacer Joins:
    $usuariosEmpresas = Usuariosempresas::select('usuarios.id as idUsuario', 'usuarios.email', 'usuariosempresas.empresa', 'usuariosempresas.tipoUsuario', 'usuariosempresas.permisoEscritura')
        ->join('usuarios', 'usuarios.id', '=', 'usuariosempresas.usuario')
        ->where('usuariosempresas.empresa', $idEmpresa)->get();

-Accede a la clave con nombre 'empresa' del array de valores que devuelve la consulta select:
    De la posicion 0 del array, extrae el valor de la key "empresa".
    dd($usuariosEmpresas[0]["empresa"]);

-Importante para evitar errores de espacios o caracteres raros al llegar el dato por la url como "España SL":  
    urldecode($valor)

-Activar el log de las consultas/querys realizadas: 
    app('db')->enableQueryLog();//Activar registro de querys 
    dd(app('db')->getQueryLog());
     app('db')->disableQueryLog();
     
-Metodo para depurar valores en ejecución:
    dd($valor);
    var_dump($valor);

-RELACIONES ENTRE TABLAS N:M ->Una Empresa puede tener muchos usuarios y viceversa:
    En primer lugar se pone el modelo con el que esta reacionado este es decir Empresas con Usuarios.
    2-El segundo parametro, indica el nombre especifico de la tabla que crea la relacion esta (Usuarios y Empresas) [ya que no seguimos la convencion de laravel].
    3-El tercer parametro, es el nombre de la clave foranea que relaciona esta nueva tabla[usuariosempresas] con la que estamos [Empresas].
    4-El cuarto parametro, es el nombre  de la clave foranea de la tabla[Usuarios] con la que estamos relacionando a esta[Empresas]
    5-El metodo withTimestamps nos permite rellenar los campos created_at y update_at automaticamente
    6-El metodo withPivot nos permite acceder a los campos de la tabla nueva[usuariosempresas] que crea la relacion entre [Usuarios-Empresas] 
         return $this->belongsToMany(Usuarios::class,'usuariosempresas','empresa','usuario')->withTimestamps()->withPivot('tipoUsuario','permisoEscritura');
-RELACIONES ENTRE TABLAS 1:N->Un cliente esta solo en una empresa y una empresa puede tener muchos clientes  [https://desarrolloweb.com/articulos/relaciones-1-a-n-laravel-eloquent.html]
    El lado 1 en el que se usara el metodo "hasMay()" hay que tener en cuenta que se metodo
    NO recibe como primer parametro el nombre de la tabla con la que se relaciona a dierencia de "belongToMany()", si no,
    que solo recibe como unico parametro el nombre de la clave foranea que lo relaciona con la tabla del lado muchos, en este caso (Clientes) es decir fk->[empresa]
        return $this->hasMany(Clientes::class,'empresa');
    El lado N o muchos se usa el metodo belongsTo(), es decir una Empresa puede tener muchos clientes, en este claso en le metodo se le indicaria la clase Empresas
    en el primer parametro, y en el segundo se le indicaria el nombre de la tabla.

 -Mis notas de ejemplo de rutas:
        //Lo que esta etre corchetes es opcional es decir puede ser nulo
        //$router->get('/{empresa}[/{email}]', ['uses'=> 'UsuariosController@listPorEmpresa']);
        //pasando mas de un campo por la URL
        //$router->delete('/{empresa}/{email}', ['uses'=> 'UsuariosController@midelete']);



        //Relaciond e claves compuestas en laravel:

        https://stackoverrun.com/es/q/10555817
        
        Como los otros han dicho, es necesario utilizar la relación HasMany y HasManyThrough.

Aquí partir de sus definiciones de la tabla, sólo tendrá acceso a:

    Person->BmCoverage(s)
    Person->BmSecurity(s) de un individuo.

Lo que creo que es el principal problema aquí es la vinculación de la BmSecurity con BmCoverage ya que aparentemente no hay coverage_id por BmSecurity sino más bien, un mapeo compuesto a través de firmId y securityId.

En este caso, Laravel no admite oficialmente claves compuestas desafortunadamente, aunque podría usar un trait like this ... pero también podría lograr lo mismo con algún truco hasMany.

es decir, sobre BmCoverage

$this->hasMany('BmSecurity', 'securityId', 'securityId') 
 ->andWhere('firmId', '=', $this->firmId); 

Lo mismo se aplica para BmSecurity de BmPerson usando HasManyThrough.

Espero que ayude.
*/


// 'numerofactura','idreparacion','idempresa','fecha','estado','numerofacturanulada'
