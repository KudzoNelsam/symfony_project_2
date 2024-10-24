<?php

namespace App\Controller;

use App\Dto\ClientSearchDto;
use App\Entity\Client;
use App\Form\ClientSearchType;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{

    #[Route('/clients', name: 'client.index')]
    public function index(Request $request, EntityManagerInterface $em, ClientRepository $clientRepository) : Response
    {
        $clientSearchDto = new ClientSearchDto();
        $formSearch = $this->createForm(ClientSearchType::class, $clientSearchDto);
        $formSearch->handleRequest($request);

        $page = $request->query->getInt('page', 1);
        $limit = 8;
        $clients = $clientRepository->paginateClients($page, $limit);

        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if($formSearch->isSubmitted() && $formSearch->isValid()){
            $clients = $clientRepository->findClientBy($clientSearchDto, $page, $limit);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $hasCompte = $form->get('hasCompte')->getData();
            if($hasCompte){
                $compte = $client->getCompte();
                $em->persist($compte);
            }
            $em->persist($client);
            $em->flush();
            return $this->redirectToRoute('client.index');
        }

        $totalClients = $clients->count();
        // Calculer le nombre total de pages
        $totalPages = ceil($totalClients / $limit);


        return $this->render('client/index.html.twig', [
            'form' => $form->createView(),
            'clients' => $clients,
            "page" => $page,
            'nombrePage' => $totalPages,
            'formSearch' => $formSearch->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'client.show')]
    public function show(int $id, ClientRepository $clientRepository) : Response
    {
        $client = $clientRepository->find($id);
        return $this->render('client/show.html.twig', [
           "client" => $client,
        ]);
    }

}