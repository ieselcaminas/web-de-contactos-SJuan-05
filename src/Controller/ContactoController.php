<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;


final class ContactoController extends AbstractController
{
    #[Route('/contacto', name: 'app_contacto')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ContactoController.php',
        ]);
    }

    #[Route('/contacto/{codigo}', name: 'ficha_contacto')]
    public function ficha(ManagerRegistry $doctrine, $codigo): Response
    {

        return new Response("Datos del contacto con código $codigo");
    }
}
