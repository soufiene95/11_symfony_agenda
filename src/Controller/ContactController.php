<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ContactController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();

        return $this->render('pages/index.html.twig',[
            "contacts"=>$contacts
        ]);
    }

    #[Route('/contact/create', name: 'app_create', methods: ['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {

            $contact->setCreatedAt(new DateTimeImmutable());
            $contact->setUpdatedAt(new DateTimeImmutable());
            
            $em->persist($contact);
            $em->flush();

            $this->addFlash("success","Le contact <em>{$contact->getFirstname()} {$contact->getLastname()}</em> a été ajouté à la liste.");

            return $this->redirectToRoute('app_index');
        }

        return $this->render('pages/create.html.twig', [
            'form'=> $form->createView(),
        ]);
    }

    #[Route('/contact/{id<\d+>}/edit', name: 'app_edit', methods: ['GET', 'PUT'])]
    public function edit(Contact $contact, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ContactFormType::class, $contact, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $contact->setUpdatedAt(new DateTimeImmutable());

            $em->persist($contact);
            $em->flush();

            $this->addFlash("success","Le contact <em>{$contact->getFirstname()} {$contact->getLastname()}</em> a été modifié.");

            return $this->redirectToRoute("app_index");
        }

        return $this->render("pages/edit.html.twig", [
            "contact" => $contact,
            "form" => $form->createView()
        ]);
    }

    #[Route('/contact/{id<\d+>}/delete', name: 'app_delete', methods: ['DELETE'])]
    public function delete(Contact $contact, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_contact_' . $contact->getId(),$request->request->get('csrf_token'))) 
        {
            $em->remove($contact);
            $em->flush();

            $this->addFlash("success","Le contact <em>{$contact->getFirstname()} {$contact->getLastname()}</em> a été supprimé de la liste.");
        }
        
        return $this->redirectToRoute("app_index");
    }  
}
