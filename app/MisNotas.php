<?php
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
-RELACIONES ENTRE TABLAS 1:N->Un cliente esta solo en una empresa y una empresa puede tener muchos clientes 
    El lado 1 en el que se usara el metodo "hasMay()" hay que tener en cuenta que se metodo
    no recibe como primer parametro el nombre de la talba con la que se relaciona a dierencia de "belongToMany()"
    Solo recibe como unico parametro el nombre de la clave foranea que lo relaciona con la tabla del lado muchos, en este caso (Clientes) es decir fk->[empresa]
        return $this->hasMany(Clientes::class,'empresa');

 -Mis notas de ejemplo de rutas:
        //Lo que esta etre corchetes es opcional es decir puede ser nulo
        //$router->get('/{empresa}[/{email}]', ['uses'=> 'UsuariosController@listPorEmpresa']);
        //pasando mas de un campo por la URL
        //$router->delete('/{empresa}/{email}', ['uses'=> 'UsuariosController@midelete']);
*/