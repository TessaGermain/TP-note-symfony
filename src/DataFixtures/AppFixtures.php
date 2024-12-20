<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Comment;
use App\Entity\Genre;
use App\Entity\Playlist;
use App\Entity\Song;
use App\Entity\User;
use App\Enum\LanguageEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Genres
        $genres = [];
        $genreTypes = ['Rock', 'Jazz', 'Pop', 'Hip-hop', 'Classique', 'Rap'];
        foreach ($genreTypes as $type) {
            $genre = new Genre();
            $genre->setName($type);
            $manager->persist($genre);
            $genres[] = $genre;
        }

        // Artists
        $artists = [];
        for ($i = 0; $i < 10; $i++) {
            $artist = new Artist();
            $artist->setName($faker->name);
            $artist->setBirthdate($faker->dateTimeBetween('-50 years', '-20 years'));
            $artist->setPhoto($faker->imageUrl(200, 200, 'people'));
            $artist->setBiography($faker->text);
            $manager->persist($artist);
            $artists[] = $artist;
        }

        // Albums
        $albums = [];
        for ($i = 0; $i < 10; $i++) {
            $album = new Album();
            $album->setTitle($faker->sentence(3));
            $album->setReleaseDate($faker->dateTimeBetween('-10 years', 'now'));
            $album->setImage($faker->imageUrl(300, 300, 'music'));
            $album->setDescription($faker->paragraph);
            $album->setLanguage($faker->randomElement(LanguageEnum::cases()));
            $album->setGenre($faker->randomElement($genres));
            $album->setArtist($faker->randomElement($artists));
            $manager->persist($album);
            $albums[] = $album;
        }

        // Songs
        $songs = [];
        for ($i = 0; $i < 50; $i++) {
            $song = new Song();
            $song->setTitle($faker->sentence(3));
            $song->setDuration($faker->numberBetween(120, 360));
            $song->setLanguage($faker->randomElement(LanguageEnum::cases()));
            $song->setGenre($faker->randomElement($genres));
            $song->setAlbum($faker->randomElement($albums));
            $song->setArtist($faker->randomElement($artists));
            $manager->persist($song);
            $songs[] = $song;
        }

        // Users
        //TODO: Refaire correctement les users après implémentation du login
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword('$2y$10$12345678901234567890123456789012');
            $user->setName($faker->firstName);
            $user->setLastName($faker->lastName);
            $manager->persist($user);
            $users[] = $user;
        }

        // Playlists
        $playlists = [];
        for ($i = 0; $i < 10; $i++) {
            $playlist = new Playlist();
            $playlist->setTitle($faker->sentence(3));
            $playlist->setDescription($faker->paragraph);
            $playlist->setCreationDate($faker->dateTimeBetween('-5 years', 'now'));
            $playlist->setUser($faker->randomElement($users));
            $manager->persist($playlist);
            $playlists[] = $playlist;
        }

        // Comments
        for ($i = 0; $i < 50; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->paragraph);
            $comment->setPublicationDate($faker->dateTimeBetween('-1 year', 'now'));
            $comment->setNote($faker->numberBetween(1, 5));
            $comment->setUser($faker->randomElement($users));
            $comment->setSong($faker->randomElement($songs));
            $manager->persist($comment);
        }

        $manager->flush();
    }
}
