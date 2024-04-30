<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\TournamentType;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tournament')]
class TournamentController extends AbstractController
{
    #[Route('/', name: 'app_tournament_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, tournamentRepository $tournamentRepository): Response
    {
        $tournament = new Tournament();
        $form = $this->createForm(TournamentType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tournament->setCreatedAt(date_create_immutable());
            $entityManager->persist($tournament);

            $entityManager->flush();

            return $this->redirectToRoute('app_tournament_show', ['id' => $tournament->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/index.html.twig', [
            'tournaments' => $tournamentRepository->findBy([], ['id' => 'DESC']),
            'form_tournament' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournament_show', methods: ['GET', 'POST'])]
    public function show(int $id, Request $request, EntityManagerInterface $entityManager, TournamentRepository $tournamentRepository): Response
    {
        $tournament = $tournamentRepository->findOneBy(['id' => $id]);

        if (is_null($tournament))
            return $this->redirectToRoute('app_tournament_index', [], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(TournamentType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tournament->setUpdatedAt(date_create_immutable());
            $entityManager->persist($tournament);

            $entityManager->flush();

            return $this->redirectToRoute('app_tournament_show', ['id' => $tournament->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournament,
            'form_tournament' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_tournament_delete', methods: ['GET'])]
    public function delete(Tournament $tournament, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($tournament);
        $entityManager->flush();

        return $this->redirectToRoute('app_tournament_index', [], Response::HTTP_SEE_OTHER);
    }
}
