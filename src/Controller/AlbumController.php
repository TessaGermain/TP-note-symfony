<?php

namespace App\Controller;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/album')]
final class AlbumController extends AbstractController
{
    #[Route(name: 'all-albums', methods: ['GET'])]
    public function index(AlbumRepository $albumRepository): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_USER")'
        ));
        return $this->render('album/index.html.twig', [
            'albums' => $albumRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_album_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_ARTIST")'
        ));
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($album);
            $entityManager->flush();

            return $this->redirectToRoute('all-albums', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('album/new.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'album-show', methods: ['GET'])]
    public function show(Album $album): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_USER")'
        ));

        return $this->render('album/show.html.twig', [
            'album' => $album,
        ]);
    }

    #[Route('/{id}/edit', name: 'album-edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Album $album, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_ARTIST")'
        ));

        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('all-albums', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('album/edit.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'album-delete', methods: ['POST'])]
    public function delete(Request $request, Album $album, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(new Expression(
            'is_granted("ROLE_ARTIST")'
        ));

        if ($this->isCsrfTokenValid('delete'.$album->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($album);
            $entityManager->flush();
        }

        return $this->redirectToRoute('all-albums', [], Response::HTTP_SEE_OTHER);
    }
}
