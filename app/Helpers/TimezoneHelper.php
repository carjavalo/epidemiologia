<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimezoneHelper
{
    /**
     * Formatear fecha/hora para mostrar en Colombia
     */
    public static function formatForColombia($date, $format = 'Y-m-d H:i:s')
    {
        if (!$date) {
            return null;
        }
        
        return Carbon::parse($date)->setTimezone('America/Bogota')->format($format);
    }
    
    /**
     * Formatear fecha/hora con zona horaria visible
     */
    public static function formatWithTimezone($date, $format = 'Y-m-d H:i:s T')
    {
        if (!$date) {
            return null;
        }
        
        return Carbon::parse($date)->setTimezone('America/Bogota')->format($format);
    }
    
    /**
     * Formatear fecha para mostrar en formato colombiano
     */
    public static function formatColombianDate($date)
    {
        if (!$date) {
            return null;
        }
        
        return Carbon::parse($date)->setTimezone('America/Bogota')->format('d/m/Y H:i:s');
    }
    
    /**
     * Obtener fecha/hora actual de Colombia
     */
    public static function nowInColombia($format = 'Y-m-d H:i:s')
    {
        return Carbon::now('America/Bogota')->format($format);
    }
    
    /**
     * Verificar si la zona horaria está configurada correctamente
     */
    public static function isTimezoneConfiguredCorrectly()
    {
        return config('app.timezone') === 'America/Bogota';
    }
    
    /**
     * Obtener información de zona horaria
     */
    public static function getTimezoneInfo()
    {
        $now = Carbon::now();
        
        return [
            'configured_timezone' => config('app.timezone'),
            'current_datetime' => $now->format('Y-m-d H:i:s T'),
            'timezone_name' => $now->getTimezone()->getName(),
            'utc_offset' => $now->format('P'),
            'is_colombia_timezone' => self::isTimezoneConfiguredCorrectly()
        ];
    }
}
