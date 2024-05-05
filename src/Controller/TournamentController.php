<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Tournament;
use App\Form\PlayerListType;
use App\Form\TournamentType;
use App\Repository\PlayerRepository;
use App\Repository\TournamentRepository;
use App\Service\TournamentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
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

            return $this->redirectToRoute('app_tournament_edit', ['id' => $tournament->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/index.html.twig', [
            'tournaments' => $tournamentRepository->findBy([], ['id' => 'DESC']),
            'form_tournament' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournament_show', methods: ['GET'])]
    public function show(
        int $id,
        TournamentManager $tournamentManager
    ): Response {
        if (!$tournamentManager->init($id))
            return $this->redirectToRoute('app_tournament_index', [], Response::HTTP_SEE_OTHER);

        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournamentManager->getTournament(),
            'players_available' => $tournamentManager->getPlayersAvailable(),
            'encounters' => $tournamentManager->getEncounters(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tournament_edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        TournamentRepository $tournamentRepository,
        PlayerRepository $playerRepository
    ): Response {
        $tournament = $tournamentRepository->findOneJoinedByID($id);

        if (is_null($tournament))
            return $this->redirectToRoute('app_tournament_index', [], Response::HTTP_SEE_OTHER);

        $formTournamment = $this->createForm(TournamentType::class, $tournament);
        $formTournamment->handleRequest($request);

        if ($formTournamment->isSubmitted() && $formTournamment->isValid()) {

            $tournament->setUpdatedAt(date_create_immutable());
            $entityManager->persist($tournament);

            $entityManager->flush();

            return $this->redirectToRoute('app_tournament_edit', ['id' => $tournament->getId()], Response::HTTP_SEE_OTHER);
        }

        $formPlayerList = $this->createForm(
            PlayerListType::class,
            null,
            ['players' => $playerRepository->findAllNotEntrant($tournament)]
        );

        $formPlayerList->handleRequest($request);

        if ($formPlayerList->isSubmitted() && $formPlayerList->isValid()) {

            $player = $formPlayerList->getData()['player'];
            if (!$player instanceof Player)
                throw new UnexpectedTypeException($player, Player::class);

            $tournament->addEntrant($player);
            $entityManager->persist($tournament);

            $entityManager->flush();

            return $this->redirectToRoute('app_tournament_edit', ['id' => $tournament->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournament/edit.html.twig', [
            'tournament' => $tournament,
            'players_available' => $playerRepository->findAllAvailable($tournament),
            'form_tournament' => $formTournamment,
            'form_player_list' => $formPlayerList,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_tournament_delete', methods: ['GET'])]
    public function delete(Tournament $tournament, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($tournament);
        $entityManager->flush();

        return $this->redirectToRoute('app_tournament_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{tournament}/player/{player}/remove', name: 'app_tournament_player_remove', methods: ['GET'])]
    public function playerRemove(Tournament $tournament, Player $player, EntityManagerInterface $entityManager): Response
    {
        $tournament->removeEntrant($player);
        $entityManager->persist($tournament);
        $entityManager->flush();

        return $this->redirectToRoute('app_tournament_edit', ['id' => $tournament->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/generate', name: 'app_tournament_generate', methods: ['GET'])]
    public function playerGenerate(
        int $id,
        TournamentManager $tournamentManager
    ): Response {
        if (!$tournamentManager->init($id))
            return $this->redirectToRoute('app_tournament_index', [], Response::HTTP_SEE_OTHER);
        $tournamentManager->getRandomEncounter();
        return $this->redirectToRoute('app_tournament_show', ['id' => $id], Response::HTTP_SEE_OTHER);
    }
}
