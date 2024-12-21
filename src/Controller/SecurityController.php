<?php

namespace App\Controller;

use App\Repository\SongRepository;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path:'/', name: 'home')]
    public function home(SongRepository $songRepository, AlbumRepository $albumRepository, ArtistRepository $artistRepository): Response
    {
        $songs = $songRepository->findBy([], ['title' => 'ASC'], 5);
        $albums = $albumRepository->findBy([], ['title' => 'ASC'], 5);
        $artists = $artistRepository->findBy([], ['name' => 'ASC'], 5);
        return $this->render('index.html.twig', [
            'songs' => $songs,
            'albums' => $albums,
            'artists' => $artists,
        ]);
    }


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
