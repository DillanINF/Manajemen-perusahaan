<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class InvoiceDetailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $jatuhTempo;
    public $items;
    public $adminEmail;
    public $adminName;

    /**
     * Create a new message instance.
     */
    public function __construct($jatuhTempo, $items, $adminEmail, $adminName)
    {
        $this->jatuhTempo = $jatuhTempo;
        $this->items = $items;
        $this->adminEmail = $adminEmail;
        $this->adminName = $adminName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from($this->adminEmail, $this->adminName)
                    ->subject('Detail Invoice - ' . $this->jatuhTempo->no_invoice)
                    ->withSymfonyMessage(function (Email $message) {
                        $candidates = [
                            public_path('image/cam.png'),
                            public_path('image/cam.jpg'),
                            public_path('image/cam.jpeg'),
                            public_path('image/cam.webp'),
                        ];
                        foreach ($candidates as $path) {
                            if (is_file($path)) {
                                $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/png';
                                // embed as CID named 'logo'
                                $message->embedFromPath($path, 'logo', $mime);
                                break;
                            }
                        }
                    })
                    ->view('emails.invoice_detail');
    }
}
