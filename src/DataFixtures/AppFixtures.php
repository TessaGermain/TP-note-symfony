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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Genres
        $genres = [];
        $genreTypes = ['Rock', 'Jazz', 'Pop', 'Hip-hop', 'Classique', 'Rap', 'Électronique', 'Country', 'Métal', 'Blues'];
        foreach ($genreTypes as $type) {
            $genre = new Genre();
            $genre->setName($type);
            $manager->persist($genre);
            $genres[] = $genre;
        }

        // Artists
        $artists = [];
        for ($i = 0; $i < 20; $i++) {
            $artist = new Artist();
            $artist->setName($faker->name);
            $artist->setBirthdate($faker->dateTimeBetween('-50 years', '-20 years'));
            $artist->setPhoto($faker->imageUrl(100, 100, 'people'));
            $artist->setBiography($faker->text);
            $manager->persist($artist);
            $artists[] = $artist;
        }

        // Albums
        $albums = [];
        for ($i = 0; $i < 30; $i++) {
            $album = new Album();
            $album->setTitle($faker->words($faker->numberBetween(1, 5), true));
            $album->setReleaseDate($faker->dateTimeBetween('-20 years', 'now'));
            $album->setImage($faker->imageUrl(100, 100, 'music'));
            $album->setDescription($faker->paragraph);
            $album->setLanguage($faker->randomElement(LanguageEnum::cases()));
            $album->setGenre($faker->randomElement($genres));
            $album->setArtist($faker->randomElement($artists));
            $manager->persist($album);
            $albums[] = $album;
        }

        // Songs
        $songs = [];
        for ($i = 0; $i < 100; $i++) {
            $song = new Song();
            $song->setTitle($faker->words($faker->numberBetween(1, 5), true));
            $song->setDuration($faker->numberBetween(75, 360));
            $song->setAlbum($faker->randomElement($albums));
            $contributors = [];
            $length = $faker->numberBetween(0, 5);
            if ($length != 0) {
                for ($j = 0; $j < $length; $j++) {
                    $contributors[] = $faker->name;
                }    
            }
            else {
                $contributors = null;
            }
            $song->setContributors($contributors);
            $manager->persist($song);
            $songs[] = $song;
        }

        // Users
        $users = [];

        //banned user
        $user = new User();
        $user->setEmail("banned@gmail.com");
        $user->setRoles(['ROLE_BANNED']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'banned'));
        $user->setName('Test');
        $user->setLastName('Banned');
        $manager->persist($user);
        $users[] = $user;

        // basic user
        $user = new User();
        $user->setEmail("tessa.germain2003@gmail.com");
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
        $user->setName('Test');
        $user->setLastName('User');
        $manager->persist($user);
        $users[] = $user;

        // artist
        $user = new User();
        $user->setEmail("artist@gmail.com");
        $user->setRoles(['ROLE_ARTIST']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'artist'));
        $user->setName('Test');
        $user->setLastName('Artist');
        $manager->persist($user);
        $users[] = $user;

        //admin
        $user = new User();
        $user->setEmail("admin@gmail.com");
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'admin'));
        $user->setName('Test');
        $user->setLastName('Admin');
        $manager->persist($user);
        $users[] = $user;

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
        for ($i = 0; $i < 20; $i++) {
            $playlist = new Playlist();
            $playlist->setTitle($faker->words($faker->numberBetween(1, 10), true));
            $playlist->setDescription($faker->paragraph);
            $playlist->setCreationDate($faker->dateTimeBetween('-5 years', 'now'));
            $playlist->setUser($faker->randomElement($users));
            $manager->persist($playlist);
            $playlists[] = $playlist;
        }

        // Comments
        for ($i = 0; $i < 150; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->paragraph);
            $comment->setPublicationDate($faker->dateTimeBetween('-1 year', 'now'));
            $comment->setNote($faker->numberBetween(1, 5));
            $comment->setUser($faker->randomElement($users));
            $comment->setSong($faker->randomElement($songs));
            $manager->persist($comment);
        }

        // Populate playlist
        foreach ($playlists as $playlist) {
            $numberOfSongs = $faker->numberBetween(5, 20);

            for ($i = 0; $i < $numberOfSongs; $i++) {
                $song = $faker->randomElement($songs);

                if (!$playlist->getSongs()->contains($song)) {
                    $playlist->addSong($song);
                }
            }

            $manager->persist($playlist);
        }

        $manager->flush();
    }
}
