<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ContactController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/index.html.twig');
    }

    #[Route('/contact/create', name: 'app_create', methods: ['GET','POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {

            $contact->setCreatedAt(new \DateTimeImmutable());
            $contact->setUpdatedAt(new \DateTimeImmutable());
            
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success','le contacte a été ajouté en base de données.');

            return $this->redirectToRoute('app_index');
        }

        return $this->render('pages/create.html.twig', [
            'form'=> $form->createView(),
        ]);
    }
}
