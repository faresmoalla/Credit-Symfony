<?php

namespace App\Controller;

use App\Entity\Client;

use App\Repository\ClientRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Date;


/**
 * @Route ("/Clientjson")
 */
class Clientjson extends AbstractController
{
    ######################################afficher tous les reclamations et les offres###########################
    /**
     * @Route("/Client/liste")
     * @throws ExceptionInterface
     */
    public function listeClient(ClientRepository $ClientRepository, NormalizerInterface $normalizer)
    {
        $Clients = $ClientRepository->findAll();
        $jsonContent = $normalizer->normalize($Clients, 'json', ['groups' => 'post:read']);

        return new Response(json_encode($jsonContent), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }





###########################################################################################################
##############afficher par id ##########################################################################

    /**
     * @Route("/Client/lire/{id}")
     */
    public function ClientId(Request $request,$id,NormalizerInterface $Normalizer)
    {

        $em = $this -> getDoctrine()->getManager();
        $participation =$em->getRepository(Client::class)->find($id);
        $jsonContent =$Normalizer->normalize($participation,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));

}


###########################################################################################################
##############ajouter ##########################################################################




    /**
     * @Route("/Client/ajout")
     */
    public function ajoutClient(Request $request,NormalizerInterface $normalizer, UserPasswordEncoderInterface $ClientPasswordEncoder)
    {
        // On vérifie si la requête est une requête Ajax

        $em =$this->getDoctrine()->getManager();
        $Client = new Client();
        $Client->setNom($request->get('nom')) ;
        $Client->setPrenom($request->get('prenom')) ;


        $Client->setEmail($request->get('email')) ;
        $Client->setTel($request->get('tel')) ;


// On sauegarde en base
        $em->persist($Client);
        $em->flush();
        $jsonContent = $normalizer->normalize($Client,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));


    }




###########################################################################################################
##############supprimer ##########################################################################




    /**
     * @Route("/Client/supprimer/{id}")
     */
    public function supprimClient(Request $request,SerializerInterface  $serializer, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $Client=$em->getRepository(Client::class)->find($id);
        $em->remove($Client);
        $em->flush();
        $jsonContent = $serializer->serialize($Client, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getIdUser();
            }
        ]);

        // On instancie la réponse
        $response = new Response($jsonContent);

        // On ajoute l'entête HTTP
        $response->headers->set('Content-Type', 'application/json');

        // On envoie la réponse
        return $response;


    }


###########################################################################################################
##############modifier ##########################################################################


    /**
     * @Route("/Client/modif/{id}")
     */
    public function modifClient(Request $request, SerializerInterface $serializer, $id)
    {
        $em = $this -> getDoctrine()->getManager();
        $Client = $em->getRepository(Client::class)->find($id);
        // On hydrate l'objet

        $Client->setNom($request->get('nom')) ;
        $Client->setPrenom($request->get('prenom')) ;


        $Client->setEmail($request->get('email')) ;
        $Client->setTel($request->get('tel')) ;

        $em->flush();
        $jsonContent = $serializer->serialize($Client, 'json', [
            'groups'=>'post:read' ,
            'circular_reference_handler' => function ($object) {
                return $object->getIdUser();
            }
        ]);


        // On instancie la réponse
        $response = new Response($jsonContent);

        // On ajoute l'entête HTTP
        $response->headers->set('Content-Type', 'application/json');

        // On envoie la réponse
        return $response;

    }

    /**
     * @Route("/Client/liste/{id}")
     */
    public function listeClientparNom(ClientRepository $ClientRepository ,NormalizerInterface $Normalizer,$id)
    {
        $jsonContent=array();
        $entityManager = $this->getDoctrine()->getManager();
        $Client = $entityManager->getRepository(Client::class)->findAll() ;
        $output=[];
        foreach ($Client as $plc){
            if($plc->getNom()==$id){

                $jsonContent1 = $Normalizer->normalize($plc) ;
                array_push($jsonContent,$jsonContent1);}
        }


        return new Response(json_encode($jsonContent));

    }







}
