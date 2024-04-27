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

            return $this->redirectToRoute('app_encounter_show', ['id' => $encounterSetResult->getEncounter()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('encounter_set_result/edit.html.twig', [
            'encounter_set_result' => $encounterSetResult,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_set_result_delete', methods: ['POST'])]
    public function delete(Request $request, EncounterSetResult $encounterSetResult, EntityManagerInterface $entityManager, EncounterSetResultRepository $encounterSetResultRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $encounterSetResult->getId(), $request->getPayload()->get('_token'))) {

            $results = $encounterSetResultRepository->findBy(['encounter' => $encounterSetResult->getEncounter()], ['number' => 'ASC']);
            $number = 0;

            foreach ($results as $result) {

                if ($encounterSetResult->getId() == $result->getId())
                    continue;

                $number++;

                if ($result->getNumber() == $number)
                    continue;

                $result->setNumber($number);
                $entityManager->persist($result);
            }

            $entityManager->remove($encounterSetResult);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_encounter_show', ['id' => $encounterSetResult->getEncounter()->getId()], Response::HTTP_SEE_OTHER);
    }
}
