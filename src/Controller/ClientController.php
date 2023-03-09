<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Reclamation;
use App\Form\ClientType;
use App\Form\ReclamationType;
use App\Repository\ClientRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ClientController extends AbstractController
{
    #[Route("/afficherClients",name :"afficherClients")]

    public function Affiche(Request $request,ClientRepository $repository,PaginatorInterface $paginator){
        $tableclients=$repository->findAll();
        $tableclients = $paginator->paginate(
            $tableclients,
            $request->query->getInt('page', 1),
            2
        );


        return $this->render('client/afficherclients.html.twig'
            ,['tableclients'=>$tableclients
               ]);
    }
    #[Route("/ajouterclient",name:"ajouterclient")]

    public function ajouterreclamation(EntityManagerInterface $em,Request $request ,ClientRepository $client){
        $client= new Client();
        $form= $this->createForm(ClientType::class,$client);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute("afficherClients");
        }
        return $this->render("client/ajouterclient.html.twig",array("form"=>$form->createView()));

    }
    #[Route("/supprimerclient/{id}",name:"supprimerclient")]

    public function delete($id,EntityManagerInterface $em ,ClientRepository $repository){
        $rec=$repository->find($id);
        $em->remove($rec);
        $em->flush();

        return $this->redirectToRoute('afficherClients');
    }
    #[Route("/{id}/modifierclient", name:"modifierclient")]

    public function edit(Request $request, Client $client): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->add('Confirmer',SubmitType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('afficherClients');
        }

        return $this->render('client/Modiferclient.html.twig', [
            'clientmodif' => $client,
            'form' => $form->createView(),
        ]);
    }


}
