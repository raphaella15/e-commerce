<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request /*\Swift_Mailer $mailer*/)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $contact = $form->getData();

           /* $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom('noreply@boutique.fr')
                ->setTo('conatct@boutique.fr')
                ->setReplyTo($contact->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig', compact('contact')
                    ),
                    'text/html'
                )
            ;
            // On envoie le message
            $mailer->send($message);
            $this->addFlash('success', 'Le message a ete envoye avec succes');
            return $this->redirectToRoute('home');*/
        }
        return $this->render('contact/index.html.twig', [
            'ContactForm' => $form->createView()
        ]);
    }
}
