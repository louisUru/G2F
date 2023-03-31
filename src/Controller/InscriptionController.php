<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Employe;
use App\Form\TypeFormation;
use Symfony\Component\HttpFoundation\Session\Session;



class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(): Response
    {
        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }

    //Inscription Valider
    #[Route('/formation/inscription/{id}', name: 'app_inscription_valider')]
    public function inscValider($id,ManagerRegistry $doctrine)
   {   
       $insc=$doctrine->getManager()->getRepository(Inscription::class)->find($id);
       $insc->setStatut("Validé");
       $em = $doctrine->getManager();
       $em->persist($insc);
       $em->flush();
       

       return $this->redirectToRoute('app_liste_Formation', [
           'controller_name' => 'FormationController',
       ]);
   }

   #[Route('/formation/inscription/{id}', name: 'app_inscription_refuser')]
   public function inscRefuser($id,ManagerRegistry $doctrine)
  {
    $insc=$doctrine->getManager()->getRepository(Inscription::class)->find($id);
    $insc->setStatut("Refusé");
    $em = $doctrine->getManager();
    $em->persist($insc);
    $em->flush();

      return $this->render('formation/index.html.twig', [
          'controller_name' => 'FormationController',
      ]);
  }
   
    //Fonction pour l'employé qui lorsque il s'inscrit , cela met en attente
   #[Route('/formation/inscriptionAtt/{id}', name: 'app_inscription_attente')]
   public function ajoutInscEnAtt($id,ManagerRegistry $doctrine)
  {

      $entityManager = $doctrine->getManager();
      $session = new Session();
      $inscriptionVerif= $doctrine->getManager()->getRepository(Inscription::class)->trouverParIdFormationEmpl($id, $session->get("employeId"));

      if(!$inscriptionVerif){
        $insc1 = new Inscription();
        $insc1->setStatut("Attente");
        $employe=$doctrine->getManager()->getRepository(Employe::class)->find($session->get("employeId"));
        $insc1->setLemploye($employe);
        $form1=$doctrine->getManager()->getRepository(Formation::class)->find($id);
        $insc1->setLaFormation($form1);
        $entityManager->persist($insc1);
        $entityManager->flush();
      }
      else{
        return $this->redirectToRoute('app_erreur_inscrit');
      }
      

      return $this->render('inscription/ajoute.twig', [
          'controller_name' => 'FormationController',
      ]);
  }


  #[Route("/erreurInscription", "app_erreur_inscrit")]
  public function erreur(ManagerRegistry $doctrine){
      $message = "Vous êtes déjà inscrit à la formation.";
       return $this->render("inscription/message_erreurInscrit.twig", ["message" => $message]);
  }

  //Afficher les inscriptions de l'employé Connecter 
   #[Route('/formation/affInscription', name: 'app_affInscriptionId')]
   public function afficheInscriptionAction(ManagerRegistry $doctrine){
        $session= new Session();
       $insc=$doctrine->getManager()->getRepository(Inscription::class)->findByVoirInscEmploye($session->get("employeId"));
       
       if (!$insc){
           $message = "Pas de formation";
       }
       else {
           $message = null;
       }
       return $this->render('inscription/listeFormInsc.html.twig', array('ensInscs'=>$insc, 'message'=>$message));
   }


   //Demande a l'admin de valider ou refuser
   #[Route('/formation/InscEnAttente/{id}', name: 'app_InscFormationAttente')]
   public function formationAttenteAction($id,ManagerRegistry $doctrine){

       $form =$doctrine->getManager()->getRepository(Inscription::class)->getInscEnAtt($id);
       if (!$form){
           $message = "Pas d'inscription en attente pour cette formation";
       }
       else {
           $message = "Inscription en attente : ";
       }
       return $this->render('formation/listeinscriptions_enAttente.twig', array('lesInscriptions'=> $form, 'message'=>$message)
       );
   }

//question 2
#[Route('/formation/InscStatut/{id}', name: 'app_InscFormationStatut')]
public function formationStatutAction($id,ManagerRegistry $doctrine){

    $form =$doctrine->getManager()->getRepository(Inscription::class)->getInscStatut($id);
    if (!$form){
        $message = "Aucune Inscription";
    }
    else {
        $message = "Inscription: ";
    }
    return $this->render('formation/listeFormStatut.twig', array('lesInscriptions'=> $form, 'message'=>$message)
    );
}

#[Route('/formation/afficherInscEmp/{id}', name: 'app_affInscriptionEmpId')]
   public function afficheInscEmp($id,ManagerRegistry $doctrine){
         $insc=$doctrine->getManager()->getRepository(Inscription::class)->findByVoirInscEmploye($id);
       
       if (!$insc){ 
           $message = "Pas d'inscription";
       }
       else {
           $message = null;
       }
       return $this->render('inscription/listeFormInsc.html.twig', array('ensInscs'=>$insc, 'message'=>$message));
   }





   /*
   #[Route('/formation/checkEdit/{id}', name: 'app_checkEmployeId')]
   public function checkEmployeIdAction($id,ManagerRegistry $doctrine, Request $request){
      
    $check =$doctrine->getRepository(Inscription::class)->find($id);
    $form = $this->createForm(InscriptionType::class, $check);    
       $form->handleRequest($request);
     
       if ($form->isSubmitted() && $form->isValid()){
           $em = $doctrine->getManager();
           $em->persist($check);
           $em->flush();
           return $this->redirectToRoute('app_InscFormationAttente');
       }
       return $this->render('formation/editerlisteinscriptions_enAttente.html.twig', 
                           array('form'=>$form->createView()));
   }
   */
}
