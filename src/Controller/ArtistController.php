<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/artist')]
final class ArtistController extends AbstractController
{
    #[Route(name: 'all-artists', methods: ['GET'])]
    public function index(ArtistRepository $artistRepository): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_USER")'
        ));

        return $this->render('artist/index.html.twig', [
            'artists' => $artistRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'artist-add', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_ADMIN")'
        ));
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($artist);
            $entityManager->flush();

            return $this->redirectToRoute('all-artists', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist/new.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'artist-show', methods: ['GET'])]
    public function show(Artist $artist): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_USER")'
        ));

        return $this->render('artist/show.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/{id}/edit', name: 'artist-edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_ADMIN")'
        ));

        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('all-artists', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artist/edit.html.twig', [
            'artist' => $artist,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'artist-delete', methods: ['POST'])]
    public function delete(Request $request, Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_ADMIN")'
        ));

        if ($this->isCsrfTokenValid('delete'.$artist->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($artist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('all-artists', [], Response::HTTP_SEE_OTHER);
    }
}
