<?php

namespace App\Controller;

use App\Entity\Terre;
use App\Form\Terre1Type;
use App\Repository\TerreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/terre')]
final class TerreController extends AbstractController
{
    #[Route('/', name: 'app_terre_index', methods: ['GET'])]
    public function index(TerreRepository $terreRepository): Response
    {
        // Récupérer toutes les terres depuis le repository
        $terres = $terreRepository->findAll();

        // Passer les terres au template
        return $this->render('terre/index.html.twig', [
            'terres' => $terres, // Assurez-vous que cette ligne est présente
        ]);
    }

    #[Route('/new', name: 'app_terre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $terre = new Terre();
        $form = $this->createForm(Terre1Type::class, $terre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($terre);
            $entityManager->flush();

            return $this->redirectToRoute('app_terre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('terre/new.html.twig', [
            'terre' => $terre,
            'form' => $form,
        ]);
    }

    #[Route('/new-from-map', name: 'app_terre_new_from_map', methods: ['POST'])]
    public function newFromMap(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données du formulaire
        $data = json_decode($request->getContent(), true);

        // Créer une nouvelle instance de Terre
        $terre = new Terre();
        $terre->setNom($data['nom']);
        $terre->setGouvernorat($data['gouvernorat']);
        $terre->setSuperficie($data['superficie']);
        $terre->setLatitude($data['latitude']);
        $terre->setLongitude($data['longitude']);

        // Enregistrer la nouvelle terre dans la base de données
        $entityManager->persist($terre);
        $entityManager->flush();

        // Retourner une réponse JSON
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Terre ajoutée avec succès !',
            'terre' => [
                'id' => $terre->getId(),
                'nom' => $terre->getNom(),
                'gouvernorat' => $terre->getGouvernorat(),
                'latitude' => $terre->getLatitude(),
                'longitude' => $terre->getLongitude(),
                'superficie' => $terre->getSuperficie(),
            ],
        ]);
    }

    #[Route('/{id}', name: 'app_terre_show', methods: ['GET'])]
    public function show(Terre $terre): Response
    {
        return $this->render('terre/show.html.twig', [
            'terre' => $terre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_terre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Terre $terre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Terre1Type::class, $terre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_terre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('terre/edit.html.twig', [
            'terre' => $terre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_terre_delete', methods: ['POST'])]
    public function delete(Request $request, Terre $terre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$terre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($terre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_terre_index', [], Response::HTTP_SEE_OTHER);
    }
}