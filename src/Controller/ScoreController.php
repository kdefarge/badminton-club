<?php

namespace App\Controller;

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
    #[Route('/{id}/edit', name: 'app_score_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Score $score, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ScoreType::class, $score);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_encounter_edit', ['id' => $score->getEncounter()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('score/edit.html.twig', [
            'score' => $score,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_score_delete', methods: ['GET'])]
    public function delete(Score $score, EntityManagerInterface $entityManager, ScoreRepository $scoreRepository): Response
    {
        if(is_null($score))
            return $this->redirectToRoute('app_encounter_index', [], Response::HTTP_SEE_OTHER);

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

        return $this->redirectToRoute('app_encounter_edit', ['id' => $score->getEncounter()->getId()], Response::HTTP_SEE_OTHER);
    }
}
