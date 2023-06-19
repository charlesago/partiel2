<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Entity\User;
use App\Repository\QuoteRepository;
use App\Service\CallApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CallApiService $callApiService): Response
    {
            $quote= $callApiService->getKaamelottData();
            $data[]= $quote;
        return $this->render('home/index.html.twig',[
            'quotes'=>$data
        ]);

    }



    #[Route('/saveCitation', name: 'app_home_saveQuote')]
    public function saveCitation(Request $request, EntityManagerInterface $manager, QuoteRepository $repository): Response
    {
        if ($repository->findOneByContent($request->getContent())){
            $quoteFind = $repository->findOneByContent($request->get('content'));
            $quoteFind->addUser($this->getUser());
            $manager->persist($quoteFind);
            $manager->flush();

        }else {
            $quote = new Quote();
            $quote->setAuthor($request->get('auteur'));
            $quote->setContent($request->get('content'));
            $quote->addUser($this->getUser());
            $manager->persist($quote);
            $manager->flush();
        }

        return $this->render('user/profile.html.twig', [
            'quotes'=>$quote
        ]);
    }

    #[Route('/admin/{id}', name: 'app_sentence_delete')]
    public function delete( Quote $quote, Request $request, QuoteRepository $quoteRepository): Response
    {

        if ($this->isCsrfTokenValid('delete'.$quote->getid(), $request->request->get('_token'))) {
            $quoteRepository->remove($quote, true);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/quotes', name: 'app_api_quotes', methods: ['GET'])]
    public function apiQuotes(): Response
    {

        $user = $this->getUser();
        $userQuotes = $user->getUsername();
        return $this->json($userQuotes, 200, [], ['groups'=>'quote:read']);
    }
}
