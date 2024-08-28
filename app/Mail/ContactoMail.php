<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $tipo_mensaje;
    private $rut;
    private $nombre;
    private $patente_vehiculo;
    private $fecha_denuncia;
    private $lugar_denunciado;
    private $ciudad_origen;
    private $ciudad_destino;
    private $telefono;
    private $email;
    private $ciudad_residencia;
    private $mensaje;

    public function __construct(
        $tipo_mensaje,
        $rut,
        $nombre,
        $patente_vehiculo,
        $fecha_denuncia,
        $lugar_denunciado,
        $ciudad_origen,
        $ciudad_destino,
        $telefono,
        $email,
        $ciudad_residencia,
        $mensaje
    )
    {
         $this->tipo_mensaje = $tipo_mensaje;
         $this->rut=$rut;
         $this->nombre=$nombre;
         $this->patente_vehiculo=$patente_vehiculo;
         $this->fecha_denuncia=$fecha_denuncia;
         $this->lugar_denunciado=$lugar_denunciado;
         $this->ciudad_origen=$ciudad_origen;
         $this->ciudad_destino=$ciudad_destino;
         $this->telefono=$telefono;
         $this->email=$email;
         $this->ciudad_residencia=$ciudad_residencia;
         $this->mensaje=$mensaje;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->view('email.email',[
            'tipo_mensaje'=>$this->tipo_mensaje,
            'rut'=>$this->rut,
            'nombre'=>$this->nombre,
            'patente_vehiculo'=>$this->patente_vehiculo,
            'fecha_denuncia'=>$this->fecha_denuncia,
            'lugar_denunciado'=>$this->lugar_denunciado,
            'ciudad_origen'=>$this->ciudad_origen,
            'ciudad_destino'=>$this->ciudad_destino,
            'telefono'=>$this->telefono,
            'email'=>$this->email,
            'ciudad_residencia'=>$this->ciudad_residencia,
            'mensaje'=>$this->mensaje
        ])->subject('Formulario de Contacto PlussChile');
    }
}
