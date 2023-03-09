<?php

namespace App\Controller;
use App\Repository\ClientRepository;

use Symfony\Component\HttpFoundation\Session\Session;

use App\Entity\Credit;
use App\Entity\Depense;
use App\Form\CreditType;
use App\Repository\CreditRepository;
use App\Repository\DepenseRepository;
use App\Repository\RevenusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreditController extends AbstractController
{
    #[Route("/affichercredit",name :"affichercredit")]

    public function Affiche(Request $request,CreditRepository $repository,PaginatorInterface $paginator){
        $tablecredits=$repository->findAll();
        $tablecredits = $paginator->paginate(
            $tablecredits,
            $request->query->getInt('page', 1),
            2
        );
        return $this->render('credit/affichercredit.html.twig'
            ,['tablecredits'=>$tablecredits
            ]);
    }
    #[Route("/ajoutercredit",name:"ajoutercredit")]

    public function ajoutercredit(ClientRepository $clientRepo,EntityManagerInterface $em,Request $request ,CreditRepository $creditRepo,RevenusRepository $revenusRepo,DepenseRepository $depenseRepo){
        $revenus= $revenusRepo->findAll();
        $depense= $depenseRepo->findAll();
        $sommerevenus= 0;
        $sommedepense = 0;

        foreach ($depense as $d){
            $sommedepense+= $d->getPrix();

        }
        foreach ($revenus as $r){
            $sommerevenus+= $r->getPrix();

        }

        $etat = $sommerevenus-$sommedepense;
        $session = new Session();

        $credit= new Credit();

        $form= $this->createForm(CreditType::class,$credit);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);






        if($form->isSubmitted() && $form->isValid()){
         /*   $sommecreditclioents = 0;
            $client=$clientRepo->findOneBy(['id' =>    $credit->getClient()->getId()]);
            $listcredits = $client->getCredit();

            foreach($listcredits as $ls){
                $sommecreditclioents +=  $ls->getValeur();

            }*/

            if($etat >= 1000){


            $em->persist($credit);
            $em->flush();
               return $this->redirectToRoute("affichercredit");
           }
           else if ($credit->getClient()->getCredit()){
               $session->getFlashBag()->add('error', 'la somme des crédits ne doit pas dépasser 500dt');

           }
           else {

               $session->getFlashBag()->add('error', 'Vous ne pouvez pas donner un crédit');


           }


        }
        return $this->render("credit/ajoutercredit.html.twig",array("form"=>$form->createView()
        ,'etat' => $etat
       // ,'sommecreditclioents' => $sommecreditclioents
        ));

    }
    #[Route("/supprimercredit/{id}",name:"supprimercredit")]

    public function delete($id,EntityManagerInterface $em ,CreditRepository $repository){
        $rec=$repository->find($id);
        $em->remove($rec);
        $em->flush();

        return $this->redirectToRoute('affichercredit');
    }

    #[Route("/envoyeremail/{id}",name:"envoyeremail")]

    public function envoyeremail($id,Request $request,\Swift_Mailer $mailer,CreditRepository $repository,MailerInterface $mailer2){
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('ghada.souissi@esprit.tn')
            ->setTo('fares.moalla@esprit.tn')
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'email/contact.html.twig',

                ),
                'text/html'
            )

            // you can remove the following code if you don't define a text version for your emails

        ;

        $mailer->send($message);

       // return $this->render(...);
    }

    #[Route("/{id}/modifiercredit", name:"modifiercredit")]

    public function edit(Request $request, Credit $credit): Response
    {
        $form = $this->createForm(CreditType::class, $credit);
        $form->add('Confirmer',SubmitType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('affichercredit');
        }

        return $this->render('credit/Modifercredit.html.twig', [
            'creditmodif' => $credit,
            'form' => $form->createView(),
        ]);
    }
    #[Route("/pdf/{id}",name:"pdf", methods: ['GET'])]
    public function pdf($id,CreditRepository $repository): Response{
        $credit=$repository->find($id);
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('credit/pdf.html.twig', [
            'pdf' => $credit,

        ]);
        $dompdf->loadHtml($html);
        //  $dompdf->loadHtml('<h1>Hello, World!</h1>');

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        //  $dompdf->stream();
        // Output the generated PDF to Browser (force download)
        /* $dompdf->stream($reclamation->getType(), [
             "Attachment" => false
         ]);*/
        $pdfOutput = $dompdf->output();
        return new Response($pdfOutput, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="credits.pdf"'
        ]);

    }


    #[Route("/stat",name:"stat")]

    public function statAction(CreditRepository $creditRepository,ClientRepository $clientRepo)
    {


        $client= $clientRepo->findAll();
        $listclients=[];

        foreach($client as $c){
            $listclients[]=$c->getNom();

            $nbrcredit[]=sizeof($c->getCredit());
        }

        return $this->render('credit/stat.html.twig',
            [

                'listclients'=> json_encode($listclients),
                'nbrcredit'=> json_encode($nbrcredit),


            ]);


    }



}
