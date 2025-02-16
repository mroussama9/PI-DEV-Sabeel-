<?php

namespace App\Controller;

use App\Entity\Capteur;
use App\Entity\Parcelle;
use App\Form\CapteurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CapteurController extends AbstractController
{
    #[Route('/capteur', name: 'capteur_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $capteurs = $entityManager->getRepository(Capteur::class)->findAll();

        return $this->render('capteur/index.html.twig', [
            'capteurs' => $capteurs,
        ]);
    }


    #[Route('/parcelle/{id}/new-capteurs', name: 'app_capteur_new_list', methods: ['GET', 'POST'])]
public function newCapteurs(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
{
    $nombreCapteurs = $parcelle->getNombreCapteurs();
    $capteursExistants = $parcelle->getCapteurs()->count();
    $capteursAAjouter = $nombreCapteurs - $capteursExistants;

    if ($capteursAAjouter <= 0) {
        return new JsonResponse([
            'success' => false,
            'message' => 'Tous les capteurs sont déjà enregistrés.',
            'redirect' => $this->generateUrl('app_parcelle_show', ['id' => $parcelle->getId()])
        ]);
    }

    // Créer des formulaires pour les nouveaux capteurs
    $capteurs = [];
    for ($i = 0; $i < $capteursAAjouter; $i++) {
        $capteurs[] = new Capteur();
    }

    $form = $this->createFormBuilder()
        ->add('capteurs', CollectionType::class, [
            'entry_type' => CapteurType::class,
            'data' => $capteurs,
            'allow_add' => true,
            'by_reference' => false,
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        foreach ($form->get('capteurs')->getData() as $capteur) {
            $capteur->setParcelle($parcelle);
            $entityManager->persist($capteur);
        }

        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'redirect' => $this->generateUrl('app_parcelle_show', ['id' => $parcelle->getId()])
        ]);
    }

    return $this->render('capteur/new_capteurs.html.twig', [
        'parcelle' => $parcelle,
        'capteurForm' => $form->createView(),
        'nombreCapteurs' => $capteursAAjouter
    ]);
}

    
    #[Route('/parcelle/{id}/edit-capteurs', name: 'app_capteur_edit_list', methods: ['GET', 'POST'])]
    public function editCapteurs(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
    {
        $capteurs = $parcelle->getCapteurs(); // 🔥 Récupère tous les capteurs de la parcelle
    
        $form = $this->createFormBuilder()
            ->add('capteurs', CollectionType::class, [
                'entry_type' => CapteurType::class,
                'data' => $capteurs,
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
            ])
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($capteurs as $capteur) {
                $entityManager->persist($capteur);
            }
    
            $entityManager->flush();
            $this->addFlash('success', 'Capteurs mis à jour avec succès !');
    
            return $this->redirectToRoute('app_parcelle_show', ['id' => $parcelle->getId()]);
        }
    
        return $this->render('capteur/edit_capteurs.html.twig', [
            'parcelle' => $parcelle,
            'capteurForm' => $form->createView(),
        ]);
    }
    

    #[Route('/capteur/{id}/delete', name: 'capteur_delete', methods: ['POST'])]
    public function delete(Request $request, Capteur $capteur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $capteur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($capteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('capteur_list');
    }
}
