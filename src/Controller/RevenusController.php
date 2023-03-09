<?php

namespace App\Controller;

use App\Entity\Revenus;
use App\Form\RevenusType;
use App\Repository\RevenusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RevenusController extends AbstractController
{
    #[Route("/afficherrevenus",name :"afficherrevenus")]

    public function Affiche(Request $request,RevenusRepository $repository,PaginatorInterface $paginator){
        $tablerevenus=$repository->findAll();
        $tablerevenus = $paginator->paginate(
            $tablerevenus,
            $request->query->getInt('page', 1),
            2
        );
        return $this->render('revenus/afficherRevenus.html.twig'
            ,['tablerevenus'=>$tablerevenus
            ]);
    }
    #[Route("/ajouterrevenus",name:"ajouterrevenus")]

    public function ajouterrevenus(EntityManagerInterface $em,Request $request ,RevenusRepository $revenus){
        $revenus= new Revenus();
        $form= $this->createForm(RevenusType::class,$revenus);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($revenus);
            $em->flush();

            return $this->redirectToRoute("afficherrevenus");
        }
        return $this->render("revenus/ajouterrevenus.html.twig",array("form"=>$form->createView()));

    }


    #[Route("/supprimerrevenus/{id}",name:"supprimerrevenus")]

    public function delete($id,EntityManagerInterface $em ,RevenusRepository $repository){
        $rec=$repository->find($id);
        $em->remove($rec);
        $em->flush();

        return $this->redirectToRoute('afficherrevenus');
    }




    #[Route("/{id}/modifierrevenus", name:"modifierrevenus")]

    public function edit(Request $request, Revenus $revenus): Response
    {
        $form = $this->createForm(RevenusType::class, $revenus);
        $form->add('Confirmer',SubmitType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('afficherrevenus');
        }

        return $this->render('revenus/Modiferrevenus.html.twig', [
            'creditrevenus' => $revenus,
            'form' => $form->createView(),
        ]);
    }


}
