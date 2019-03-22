<?php

namespace App\Controller;

use GuzzleHttp\Client;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{


    /**
     * @Route("/", name="home_index")
     */
    public function index()
    {
        $API = "http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=3";
        $API2 = "http://quotesondesign.com/wp-json/posts?filter[orderby]=rand";

        $client = new Client([
            'headers' => ['Content-type' => 'application/json', 'Accept' => 'application/json']
        ]);

        $response = $client->request('GET', $API2);
        $data = $response->getBody();
        $data = json_decode($data);

        //var_dump($userManager);
        //die;

        return $this->render(
            'home/index.html.twig',
            [
                'data' => $data[0],
            ]
        );
    }
}