<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Solicitud;


class SolicitudCreadaNotification extends Notification
{
    use Queueable;
    public $solicitud;
    /**
     * Create a new notification instance.
     */
    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'solicitud_id' => $this->solicitud->id_solicitud,
            'numero' => $this->solicitud->numero_solicitud,
            'mensaje' => 'Nueva solicitud creada: ' . $this->solicitud->numero_solicitud,
            'tipo' => 'Accion_Requerida',
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */

    /**
     * Get the mail representation of the notification.
     */
   // Enviar notificación
    use App\Models\Usuario;
    use App\Notifications\SolicitudCreadaNotification;

    // A todos los usuarios con rol Presupuesto
    $usuariosPresupuesto = Usuario::role('Presupuesto')->get();
    foreach($usuariosPresupuesto as $usuario) {
        $usuario->notify(new SolicitudCreadaNotification($solicitud));
    }
}
