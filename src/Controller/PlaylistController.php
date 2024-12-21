<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/playlist')]
final class PlaylistController extends AbstractController
{
    #[Route('/user/{id}', name: 'playlists', methods: ['GET'])]
    public function index(PlaylistRepository $playlistRepository, int $id): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_USER")'
        ));

        return $this->render('playlist/index.html.twig', [
            'playlists' => $playlistRepository->findBy(['user' => $id]),
        ]);
    }

    #[Route('/new', name: 'playlist-add', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_USER")'
        ));

        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($playlist);
            $entityManager->flush();

            return $this->redirectToRoute('playlists', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('playlist/new.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'playlist-show', methods: ['GET'])]
    public function show(Playlist $playlist): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_USER")'
        ));

        return $this->render('playlist/show.html.twig', [
            'playlist' => $playlist,
        ]);
    }

    #[Route('/{id}/edit', name: 'playlist-edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Playlist $playlist, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_USER")'
        ));

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('playlists', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('playlist/edit.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'playlist-delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_USER")'
        ));

        if ($this->isCsrfTokenValid('delete'.$playlist->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($playlist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('playlists', [], Response::HTTP_SEE_OTHER);
    }
}
