<?php
namespace App;

/**
 * [Utils: Esta clase es utilizada como clase de comprobaciones generales a la hora de realizar ciertas operaciones crud en muchos de las clases Controllers existentes]
 */
 class Utils{
     /**
      * @param mixed $tlf
      * Comprobacion de que el numero de Tlf cumple el patron establecido como de 9 a 12 numero y que puede empezar por + para indicar el prefijo o no
      * @return [boolean]
      */
     public static function validarTlf($tlf)
     {
        if (preg_match("/^(\+?)([0-9]{9,12}$)/", $tlf)) //Comprobacion de numero de Tlf
             return true;
         return false;
     }

     /**
      * @param mixed $email
      * Comprobacion de que el email cumple el patron establecido en los estandares de [FILTER_VALIDATE_EMAIL]
      * @return [boolean]
      */
     public static function validarcorreo($email)
     {
         if (filter_var($email, FILTER_VALIDATE_EMAIL))
             return true;

        return false;
     }
}