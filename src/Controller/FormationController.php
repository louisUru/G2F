<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\TypeFormation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Inscription;

class FormationController extends AbstractController
{
    #[Route('/', name: 'app_formation')]
    public function index(): Response
    {
        return $this->render('accueil.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }

    //Pour l'admin
    #[Route('/listeFormation', name: 'app_liste_Formation')]
    public function afficheLesFormations(ManagerRegistry $doctrine)
    {
        $formations=$doctrine->getManager()->getRepository(Formation::class)->findAll();
        if(!$formations){
            $message = "Il n'y a pas de Formation";
        }
        else{
            $message=null;
        }
        return $this->render('formation/listeFormation.html.twig',array('ensFormation'=>$formations,'message'=>$message));
    }


    //Pour l'employé
    #[Route('/listeFormationEmp', name: 'app_liste_FormationEmp')]
    public function afficheLesFormationsEmp(ManagerRegistry $doctrine)
    {
        $formations=$doctrine->getManager()->getRepository(Formation::class)->findAll();
        if(!$formations){
            $message = "Il n'y a pas de Formation";
        }
        else{
            $message=null;
        }
        return $this->render('formation/listeFormations_s_inscrire.twig',array('ensFormation'=>$formations,'message'=>$message));
    }


    #[Route('/ajoutFormation', name: 'app_ajout_formation')]
    public function ajoutFormation(Request $request, ManagerRegistry $doctrine, $formation=null)
    {
        if ($formation==null){
            $formation= new Formation();
        }
        $form= $this->createForm(TypeFormation::class, $formation);

        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()) {
                $em= $doctrine->getManager();
                $em->persist($formation);
                $em->flush();
                return $this->redirectToRoute('app_liste_Formation');
            }
        return $this->render('formation/editerForma.html.twig',
                            array('form'=>$form->createView()));
    }




    #[Route('/suppFormation/{id}', name:'app_formation_sup')]

    public function suppFormation($id , ManagerRegistry $doctrine)
    {
        $formation = $doctrine ->getManager()->getRepository(Formation::class)->find($id);
        $inscriptions = $doctrine->getManager()->getRepository(Inscription::class)->trouverParIdFormation($formation->getId());
        
        if ($formation  && $inscriptions == null ){
        $entityManager = $doctrine->getManager();
        $entityManager -> remove($formation);
        $entityManager->flush();
        }
        else {
            //p. 5 -> exception 5.a.
            return $this->redirectToRoute("app_formation_erreur");
        }

        return $this->redirectToRoute('app_liste_Formation');
    }

    #[Route("/erreurSuppressionFormation", "app_formation_erreur")]
	public function erreurSupprFormation(ManagerRegistry $doctrine){
		$message = "Des employés sont inscrits à cette formation. Vous ne pouvez pas la supprimer";
 		return $this->render("formation/message_erreurSuppr.twig", ["message" => $message]);
	}

    
    #[Route('/modifFormation/{id}', name: 'app_formation_modif')]

    public function modiFilmAction($id, Request $request,ManagerRegistry $doctrine)
    {
        $formation = $doctrine->getRepository(Formation::class)->find($id);
        $form= $this->createForm(TypeFormation::class, $formation);

        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager -> persist($formation);
            $entityManager->flush();
            return $this->redirectToRoute('app_liste_Formation');
    }
    return $this->render('formation/editerForma.html.twig',
    array('form'=>$form->createView()));
}



}

