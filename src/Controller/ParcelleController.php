<?php

// src/Controller/ParcelleController.php
namespace App\Controller;

use App\Entity\Parcelle;
use App\Entity\Terre;
use App\Entity\Capteur;
use App\Form\CapteurType;
use App\Form\ParcelleType;
use App\Form\ParcelleCultureType;
use App\Repository\ParcelleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/parcelle')]
class ParcelleController extends AbstractController
{
    // ✅ Afficher la liste des parcelles
    #[Route('/parcelles', name: 'app_parcelle_index', methods: ['GET'])]
    public function index(ParcelleRepository $parcelleRepository): Response
    {
        $parcelles = $parcelleRepository->findAll(); // ✅ Récupérer toutes les parcelles
    
        return $this->render('parcelle/index.html.twig', [
            'parcelles' => $parcelles, // ✅ Envoi de la variable au template
        ]);
    }
    
    // ✅ Créer plusieurs parcelles pour une terre spécifique
    #[Route('/create-multiple/{id}', name: 'app_parcelle_create_multiple', methods: ['GET', 'POST'])]
    public function createMultiple(Terre $terre, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $nombreParcelles = (int) $request->request->get('nombreParcelles');

            for ($i = 1; $i <= $nombreParcelles; $i++) {
                $parcelle = new Parcelle();
                $parcelle->setNom($request->request->get('parcelleNom' . $i));
                $parcelle->setSuperficie((float) $request->request->get('parcelleSuperficie' . $i));
                $parcelle->setTerre($terre);

                $entityManager->persist($parcelle);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Les parcelles ont été créées avec succès.');
            return $this->redirectToRoute('app_parcelle_index');
        }

        return $this->render('parcelle/create_multiple.html.twig', [
            'terre' => $terre,
        ]);
    }

// src/Controller/ParcelleController.php
#[Route('/{id}/show', name: 'app_parcelle_show', methods: ['GET', 'POST'])]
public function show(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
{
    // Formulaire de modification de la parcelle
    $parcelleForm = $this->createForm(ParcelleType::class, $parcelle);
    $parcelleForm->handleRequest($request);

    if ($parcelleForm->isSubmitted() && $parcelleForm->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Parcelle mise à jour avec succès !');
        return $this->redirectToRoute('app_parcelle_show', ['id' => $parcelle->getId()]);
    }

    return $this->render('parcelle/show.html.twig', [
        'parcelle' => $parcelle,
        'parcelleForm' => $parcelleForm->createView(),
    ]);
}

#[Route('/{id}/edit', name: 'app_parcelle_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(ParcelleType::class, $parcelle);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // 🔥 Mise à jour du nombre de capteurs
        $nombreCapteurs = $form->get('nombreCapteurs')->getData();
        $parcelle->setNombreCapteurs((int) $nombreCapteurs); // 🔥 Assurer que c'est bien un entier

        $entityManager->flush();

        // ✅ Vérification : Générer l'URL correctement
        $redirectUrl = $this->generateUrl('app_capteur_new_list', ['id' => $parcelle->getId()]);

        return new JsonResponse([
            'success' => true,
            'redirect' => $redirectUrl
        ]);
    }

    return $this->render('parcelle/edit.html.twig', [
        'parcelle' => $parcelle,
        'parcelleForm' => $form->createView(),
    ]);
}



    
    // ✅ Supprimer une parcelle
    #[Route('/delete/{id}', name: 'app_parcelle_delete', methods: ['POST'])]
    public function delete(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $parcelle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($parcelle);
            $entityManager->flush();

            $this->addFlash('success', 'La parcelle a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_parcelle_index');
    }

    // ✅ Mise à jour du statut d'une parcelle (plantée ou non) via AJAX
    #[Route('/update-status/{id}/{status}', name: 'app_parcelle_update_status', methods: ['POST'])]
    public function updateStatus(Parcelle $parcelle, string $status, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($status === "planted") {
            $parcelle->setPlantee(true);
        } else {
            $parcelle->setPlantee(false);
        }
    
        $entityManager->flush();
    
        return new JsonResponse(['status' => 'success', 'plantee' => $parcelle->isPlantee()]);
    }

    // ✅ Mise à jour de la culture et des capteurs via AJAX
    #[Route('/update-culture/{id}', name: 'app_parcelle_update_culture', methods: ['POST'])]
    public function updateCulture(Request $request, Parcelle $parcelle, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        // Mettre à jour la parcelle
        $parcelle->setTypeCulture($data['typeCulture']);
        $parcelle->setPlantee(true);
    
        // Ajouter les capteurs
        for ($i = 0; $i < $data['nombreCapteurs']; $i++) {
            $capteur = new Capteur();
            $capteur->setParcelle($parcelle);
            $entityManager->persist($capteur);
        }
    
        $entityManager->flush();
    
        return new JsonResponse(['status' => 'success', 'plantee' => $parcelle->isPlantee()]);
    }
}