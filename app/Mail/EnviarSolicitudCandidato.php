<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use function PHPUnit\Framework\isNull;

class EnviarSolicitudCandidato extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

     public $mensaje;
     public $datos;

     public $solicitante;

    public function __construct($mensaje,$datos, $solicitante,$nombreSolicitante)
    {
        //
        $this->mensaje=$mensaje;
        $this->datos=$datos;
        $this->solicitante=[
            'correoSolicitante'=>$solicitante,
            'nombreSolicitante'=>$nombreSolicitante,
        ];
        
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        
        $cuenta_usuario_envio=env('MAIL_FROM_ADDRESS');
        
        if($cuenta_usuario_envio=='')
        {
            throw new \Exception('la direccion de correo es nula.');
        }
        return new Envelope(
            from: new Address($cuenta_usuario_envio,'INFO RECLUTAMIENTO'),
            subject: 'Enviar Solicitud Candidato',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'viewsmails.index',
            with:[
                'mensaje'=>$this->mensaje,
                'datosSolicitante'=>$this->solicitante,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
