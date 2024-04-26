<?php

namespace App\Controller;

use App\Entity\Encounter;
use App\Form\EncounterType;
use App\Repository\EncounterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/encounter')]
class EncounterController extends AbstractController
{
    #[Route('/', name: 'app_encounter_index', methods: ['GET'])]
    public function index(EncounterRepository $encounterRepository): Response
    {
        return $this->render('encounter/index.html.twig', [
            'encounters' => $encounterRepository->findAllJoined(),
        ]);
    }

    #[Route('/new', name: 'app_encounter_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $encounter = new Encounter();
        $form = $this->createForm(EncounterType::class, $encounter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($encounter);
            $entityManager->flush();

            return $this->redirectToRoute('app_encounter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('encounter/new.html.twig', [
            'encounter' => $encounter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_encounter_show', methods: ['GET'])]
    public function show(int $id, EncounterRepository $encounterRepository): Response
    {
        return $this->render('encounter/show.html.twig', [
            'encounter' => $encounterRepository->findOneJoinedByID($id),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_encounter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Encounter $encounter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EncounterType::class, $encounter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_encounter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('encounter/edit.html.twig', [
            'encounter' => $encounter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_encounter_delete', methods: ['POST'])]
    public function delete(Request $request, Encounter $encounter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$encounter->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($encounter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_encounter_index', [], Response::HTTP_SEE_OTHER);
    }
}
