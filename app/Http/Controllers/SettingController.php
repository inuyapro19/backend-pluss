<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{

    public function index()
    {
        $locale = \request('locale');
        // Obtener todas las configuraciones
        $settings = Setting::where('locale', $locale)->get();

        // Crear un array para almacenar los datos saneados
        $sanitizedSettings = [];

        // Iterar sobre cada configuración
        foreach ($settings as $setting) {
            // Obtener el array de la configuración
            $settingArray = $setting->toArray();

            // Sanear el valor de la clave y asignarlo al array
            $sanitizedSettings[$settingArray['key']] = filter_var($settingArray['value'], FILTER_SANITIZE_STRING);
        }

        // Devolver una respuesta JSON con todas las configuraciones saneadas
        return response()->json($sanitizedSettings);
    }

    public function store(Request $request)
    {
        try {
            foreach ($request->all() as $key => $value) {
                // Ignorar el token CSRF y otros campos no deseados
                if (in_array($key, ['_token', 'otros_campos_a_ignorar'])) {
                    continue;
                }

                // Comprobar si el valor es una imagen
                if ($request->hasFile($key) && $request->file($key)->isValid()) {
                    // Guardar la imagen y obtener la ruta
                    $path = $request->file($key)->store('uploads/setting', 'public');
                    // Get only the name of the file
                    $value = basename($path);
                }

                // Actualizar o crear el registro con la clave y el valor o ruta de la imagen
                // Buscar el registro o crear uno nuevo si no existe
                $setting = Setting::firstOrCreate(['key' => $key]);

                // Actualizar el valor del registro
                $setting->update(['value' => $value]);
            }

            // Devolver una respuesta JSON con un mensaje de éxito
            return response()->json(['message' => 'Configuraciones guardadas correctamente.']);
        } catch (\Exception $e) {
            // Handle the exception
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
