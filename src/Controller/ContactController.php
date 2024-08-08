<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ContactController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request): Response
    {
        $dto = new ContactDTO();
        
        $form = $this->createForm(ContactType::class ,$dto  , [
            "action" => $this->generateUrl("contact_send_email")
        ]);


        return $this->render('contact/contact.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/contact/send_email', name: 'contact_send_email')]

    public function sendEmail(Request $request , MailerInterface $mailer): Response
    {
        $dto = new ContactDTO();
        $form = $this->createForm(ContactType::class ,$dto);

        $dto->name = "Najoro";


        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()){
            try{
                $mail = (new TemplatedEmail())
                        ->to($dto->service)
                        ->from('teste@contact.fr')
                        ->subject($dto->message)
                        ->htmlTemplate("emails/send-email.html.twig")
                        ->context(["data"=> $dto]);

                $mailer->send($mail);

                $this->addFlash("success" , "votre email a ete bien envoyer");
                return $this->redirectToRoute("contact");

            }catch(TransportExceptionInterface $e){

                $this->addFlash("danger", "votre message n'est pas envoyer");
            }


        }

        return $this->render('contact/contact.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
