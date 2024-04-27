<?php

namespace App\Controller;

use App\Entity\Encounter;
use App\Entity\EncounterSetResult;
use App\Form\EncounterSetResultType;
use App\Repository\EncounterSetResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/set-result')]
class EncounterSetResultController extends AbstractController
{
    #[Route('/{id}/new', name: 'app_set_result_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Encounter $encounter, EntityManagerInterface $entityManager, EncounterSetResultRepository $encounterSetResultRepository): Response
    {
        $encounterSetResult = new EncounterSetResult();
        $form = $this->createForm(EncounterSetResultType::class, $encounterSetResult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encounterSetResult->setEncounter($encounter);
            $encounterSetResult->setNumber($encounterSetResultRepository->count(array('encounter' => $encounter->getId())) + 1);
            $entityManager->persist($encounterSetResult);
            $entityManager->flush();

            return $this->redirectToRoute('app_encounter_show', ['id' => $encounter->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('encounter_set_result/new.html.twig', [
            'encounter' => $encounter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_set_result_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EncounterSetResult $encounterSetResult, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EncounterSetResultType::class, $encounterSetResult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_encounter_show', ['id' => $encounterSetResult->getEncounter()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('encounter/edit.html.twig', [
            'encounter' => $encounterSetResult->getEncounter(),
            'form' => $form,
        ]);
    }
}
