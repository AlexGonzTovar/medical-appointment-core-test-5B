<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from = config('services.twilio.whatsapp_from');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        } else {
            Log::warning('Twilio credentials are not set in the configuration.');
        }
    }

    /**
     * Sends a WhatsApp message using Twilio.
     *
     * @param string $to The recipient's phone number (with country code, e.g., +521234567890)
     * @param string $message The content of the message
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance|null
     * @throws \Exception
     */
    public function sendMessage($to, $message)
    {
        if (!$this->client) {
            Log::error('Cannot send WhatsApp message. Twilio client is not initialized.');
            return null;
        }

        // Twilio requires phone numbers to be in a specific format for WhatsApp: "whatsapp:+1234567890"
        
        // Remove all non-numeric characters first
        $cleanNumber = preg_replace('/[^0-9]/', '', $to);

        // Ajuste específico para México (+521) requerido por WhatsApp/Twilio
        if (strlen($cleanNumber) === 10) {
            // Si son 10 dígitos, asumimos México y agregamos 521
            $cleanNumber = '521' . $cleanNumber;
        } elseif (strlen($cleanNumber) === 12 && str_starts_with($cleanNumber, '52')) {
            // Si son 12 dígitos y empieza con 52, insertamos el 1 de celulares en México
            $cleanNumber = '521' . substr($cleanNumber, 2);
        }

        $recipient = "whatsapp:+" . $cleanNumber;
        $sender = "whatsapp:" . $this->from;

        try {
            return $this->client->messages->create(
                $recipient,
                [
                    'from' => $sender,
                    'body' => $message,
                ]
            );
        } catch (\Exception $e) {
            Log::error("Error sending WhatsApp message to {$to}: " . $e->getMessage());
            // Depending on the requirement, throwing the exception or just logging it.
            return null;
        }
    }
}
