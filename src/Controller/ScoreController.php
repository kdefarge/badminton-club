<?php

namespace App\Controller;

use App\Entity\Encounter;
use App\Entity\Score;
use App\Form\ScoreType;
use App\Repository\ScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/score')]
class ScoreController extends AbstractController
{
    #[Route('/{id}/new', name: 'app_score_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Encounter $encounter, EntityManagerInterface $entityManager, ScoreRepository $scoreRepository): Response
    {
        $score = new Score();
        $form = $this->createForm(ScoreType::class, $score);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $score->setEncounter($encounter);
            $score->setNumber($scoreRepository->count(array('encounter' => $encounter->getId())) + 1);
            $entityManager->persist($score);
            $entityManager->flush();

            return $this->redirectToRoute('app_encounter_show', ['id' => $encounter->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('score/new.html.twig', [
            'encounter' => $encounter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_score_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Score $score, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ScoreType::class, $score);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_encounter_show', ['id' => $score->getEncounter()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('score/edit.html.twig', [
            'score' => $score,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_score_delete', methods: ['POST'])]
    public function delete(Request $request, Score $score, EntityManagerInterface $entityManager, ScoreRepository $scoreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $score->getId(), $request->getPayload()->get('_token'))) {

            $results = $scoreRepository->findBy(['encounter' => $score->getEncounter()], ['number' => 'ASC']);
            $number = 0;

            foreach ($results as $result) {

                if ($score->getId() == $result->getId())
                    continue;

                $number++;

                if ($result->getNumber() == $number)
                    continue;

                $result->setNumber($number);
                $entityManager->persist($result);
            }

            $entityManager->remove($score);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_encounter_show', ['id' => $score->getEncounter()->getId()], Response::HTTP_SEE_OTHER);
    }
}
