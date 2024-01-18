<?php

namespace App\Controller\Visitor\Contact;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\SettingRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'visitor.contact.create', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, MailerService $mailerService, SettingRepository $settingRepository): Response
    {
        $contact = new Contact();
        $data = $settingRepository->findAll();
        $setting = $data[0];
        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contact);
            $em->flush();

            // Envoi de l'email
            $mailerService->send([
                "sender_email" => "ridelocation@hotmail.com",
                "sender_name" => "Contact",
                "recipient_email" => "ridelocation@hotmail.com",
                "subject" => "Nouveau message de contact",
                "html_template" => "email/contact.html.twig",
                "context" => [
                    "contact_email" => $contact->getEmail(),
                    "contact_last_name" => $contact->getLastName(),
                    "contact_first_name" => $contact->getFirstName(),
                    "contact_phone" => $contact->getPhone(),
                    "contact_message" => $contact->getMessage(),
                ]
            ]);

            $this->addFlash('success', 'Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais');

            return $this->redirectToRoute('visitor.contact.create');
        }
        return $this->render('pages/visitor/contact/create.html.twig', [
            "form" => $form->createView(),
            "setting" => $setting
        ]);
    }
}
