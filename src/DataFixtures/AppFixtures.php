<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Quote;
use GuzzleHttp\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $users = [];
        $faker = Factory::create('FR-fr');
        $genres = ['male', 'female'];

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);


        //Création user ADMIN
        $adminUser = new User();
        $adminUser->setFirstName('Noureddine')
            ->setLastName('Berjaoui')
            ->setEmail('nour1808@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser, '000000'))
            ->setPicture('https://media.licdn.com/dms/image/C5603AQFkyKtb-_uVQQ/profile-displayphoto-shrink_200_200/0?e=1558569600&v=beta&t=i_oN9fBXKY5mG38OeLbfM20nmDolwszvQ59wcuGpZ9w')
            ->setGender('male')
            ->addUserRole($adminRole);

        $users[] = $adminUser;
        //Gestion des users
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture = "https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $hash = $this->encoder->encodePassword($user, 'password');

            if ($genre == "male") $picture = $picture . 'men/' . $pictureId;
            else $picture = $picture . 'women/'
                . $pictureId;

            $manager->persist($adminUser);

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setHash($hash)
                ->setGender($genre)
                ->setPicture($picture);
            $manager->persist($user);
            $users[] = $user;
        }


        //Gestion des Quotes  
        for ($j = 0; $j < 10; $j++) {
            $API = "http://quotesondesign.com/wp-json/wp/v2/posts/?orderby=rand";
            $client = new Client([
                'headers' => ['Content-type' => 'application/json', 'Accept' => 'application/json']
            ]);

            $response = $client->request('GET', $API);
            $data = $response->getBody();
            $data = json_decode($data);
            $data = $data[0];
            $source = (isset($data->custom_meta)) ? $source = $data->custom_meta->Source : null;

            $quote = new Quote();
            $title = $data->title->rendered;
            $content = $data->content->rendered;
            $source = $source;
            $authorQuote = $data->title;
            $userQuote = $users[mt_rand(0, count($users) - 1)];

            $quote->setContent($content)
                ->setSource($source)
                ->setUser($userQuote)
                ->setAuthor($authorQuote->rendered)
                ->setCreatedAt($faker->dateTimeBetween('-8 months'));

            $manager->persist($quote);
        }

        $manager->flush();
    }
}