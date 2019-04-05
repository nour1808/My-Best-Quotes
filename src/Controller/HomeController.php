<?php

namespace App\Controller;

use App\Entity\Quote;
use GuzzleHttp\Client;
use App\Form\AddQuoteType;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{


    /**
     * @Route("/", name="home_index")
     */
    public function index(Request $request, SeoPageInterface $seoPage)
    {
        //var_dump($this->getParameter('app.path.user_images'));
        $session = $request->getSession();

        $API = "http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=3";
        $API2 = "http://quotesondesign.com/wp-json/posts?filter[orderby]=rand";

        $client = new Client([
            'headers' => ['Content-type' => 'application/json', 'Accept' => 'application/json']
        ]);

        $response = $client->request('GET', $API2);
        $data = $response->getBody();
        $data =
            json_decode($data);

        $session->set('data', $data[0]);

        $seoPage
            ->setTitle("My best quotes - The best and most beautiful things in the world cannot be seen or even touched ")
            ->addMeta('name', 'description', "Best Quotes - " . $data[0]->content);

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
    public function saveQuote(Request $request, ObjectManager $manager, ValidatorInterface $validator)
    {
        $session = $request->getSession();
        $quote = new Quote();
        $data = $session->get('data');
        $source = (isset($data->source)) ? $data->source : null;
        $quote->setContent($data->content)
            ->setAuthor($data->title)
            ->setUser($this->getUser())
            ->setSource($source);

        $errors = $validator->validate($quote);

        if (count($errors) > 0) {
            $this->addFlash(
                'danger',
                "Another quote already has this content."
            );
            return $this->redirectToRoute('home_index');
        } else {

            $manager->persist($quote);
            $manager->flush();

            $this->addFlash(
                'success',
                "The addition of your quote has been successfully registered."
            );

            return $this->redirectToRoute('account_index');
        }
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

    /**
     * @Route("/quote/new", name="home_addQuote")
     * @IsGranted("ROLE_USER")
     */
    public function addQuote(Request $request, ObjectManager $manager)
    {
        $quote = new Quote();
        $form = $this->createForm(AddQuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            $quote
                ->setAuthor($user->getFullname())
                ->setUser($user);

            $manager->persist($quote);
            $manager->flush();

            $this->addFlash(
                'success',
                "The addition of your quote has been successfully registered."
            );
            return $this->redirectToRoute('account_index');
        }

        return $this->render('account/add-quote.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
