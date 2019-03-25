<?php

namespace App\Controller;

use App\Entity\Quote;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{


    /**
     * @Route("/", name="home_index")
     */
    public function index(Request $request)
    {
        $session = $request->getSession();

        $API = "http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=3";
        $API2 = "http://quotesondesign.com/wp-json/posts?filter[orderby]=rand";

        $client = new Client([
            'headers' => ['Content-type' => 'application/json', 'Accept' => 'application/json']
        ]);

        $response = $client->request('GET', $API2);
        $data = $response->getBody();
        $data = json_decode($data);

        $session->set('data', $data[0]);

        return $this->render(
            'home/index.html.twig',
            [
                'data' => $data[0],
            ]
        );
    }

      /**
     * @Route("/saveQuote", name="home_saveQuote")
     * @IsGranted("ROLE_USER")
     */
    public function saveQuote( Request $request, ObjectManager $manager)
    {
        $session = $request->getSession();
        $quote = new Quote();
        $data = $session->get('data');
        $source = (isset($data->source)) ? $data->source : null;

        $quote->setContent($data->content)
            ->setAuthor($data->title)
            ->setUser($this->getUser())
            ->setSource($source)
            ;

            $manager->persist($quote);
            $manager->flush();

            $this->addFlash(
                'success',
                "The addition of your quote has been successfully registered."
            );

        return $this->redirectToRoute('account_index');
    }

    /**
     * @Route("/removeQuote/{id}", name="home_removeQuote")
     * @Security("is_granted('ROLE_USER') and user === quote.getUser()", message= "This ad does not belong to you, you can not remove it")
     */
    public function removeQuote(Quote $quote, ObjectManager $manager)
    {
        $manager->remove($quote);
        $manager->flush();

        $this->addFlash(
            'success',
            "Quote: <strong>{$quote->getAuthor()}</strong> was removed well !"
        );

        return $this->redirectToRoute("account_index");
    }
    
}