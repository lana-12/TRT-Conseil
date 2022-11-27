<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
class SendMailService
{
    private $mailer;
    
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        
    }

    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context,
        // string $attachment = null
        
    ): void
    {
        // Create
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);
    // if ($attachment !=null){
    //     // $email->attachFromPath("\path\public\uploads\cvs\$attachment",'CV', 'application/pdf');
    //     $email->attachFromPath($this->getParameter('cvs_directory') . "/$attachment");
    // }

        //Send
        $this->mailer->send($email);
    }
}