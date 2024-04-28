<?php

namespace App\Controller;

use App\Entity\Encounter;
use App\Entity\EncounterPlayer;
use App\Entity\Score;
use App\Form\EncounterPlayerType;
use App\Form\EncounterType;
use App\Form\ScoreType;
use App\Repository\EncounterRepository;
use App\Repository\ScoreRepository;
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
    public function new(EntityManagerInterface $entityManager): Response
    {
        $encounter = new Encounter();
        $encounter->setFinished(true);
        $encounter->setCreatedAt(date_create_immutable());
        $entityManager->persist($encounter);

        $entityManager->flush();
        
        return $this->redirectToRoute('app_encounter_show', ['id' => $encounter->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_encounter_show', methods: ['GET', 'POST'])]
    public function show(Request $request, int $id, EncounterRepository $encounterRepository, EntityManagerInterface $entityManager, ScoreRepository $scoreRepository): Response
    {
        $encounter = $encounterRepository->findOneJoinedByID($id);
        $encounterPlayer = new EncounterPlayer();
        $formEncounterPlayer = $this->createForm(EncounterPlayerType::class, $encounterPlayer);
        $formEncounterPlayer->handleRequest($request);

        if ($formEncounterPlayer->isSubmitted() && $formEncounterPlayer->isValid()) {

            $encounterPlayer->setEncounter($encounter);
            $entityManager->persist($encounterPlayer);

            $encounter->setUpdatedAt(date_create_immutable());
            $entityManager->persist($encounter);

            $entityManager->flush();

            return $this->redirectToRoute('app_encounter_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        $score = new Score();
        $formScore = $this->createForm(ScoreType::class, $score);
        $formScore->handleRequest($request);

        if ($formScore->isSubmitted() && $formScore->isValid()) {

            $score->setEncounter($encounter);
            $score->setNumber($scoreRepository->count(array('encounter' => $encounter)) + 1);
            $entityManager->persist($score);

            $encounter->setUpdatedAt(date_create_immutable());
            $entityManager->persist($encounter);

            $entityManager->flush();

            return $this->redirectToRoute('app_encounter_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('encounter/show.html.twig', [
            'encounter' => $encounter,
            'form_encounter_player' => $formEncounterPlayer,
            'form_score' => $formScore,
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

    #[Route('/delete/{id}', name: 'app_encounter_delete', methods: ['POST'])]
    public function delete(Request $request, Encounter $encounter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $encounter->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($encounter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_encounter_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/player/remove/{id}', name: 'app_player_encounter_remove', methods: ['GET'])]
    public function playerRemove(EncounterPlayer $encounterPlayer, EntityManagerInterface $entityManager): Response
    {
        $encounterId = $encounterPlayer->getEncounter()->getId();
        $entityManager->remove($encounterPlayer);
        $entityManager->flush();

        return $this->redirectToRoute('app_encounter_show', ['id' => $encounterId], Response::HTTP_SEE_OTHER);
    }
}
