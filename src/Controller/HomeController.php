<?php

// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // check log in
        if ($this->getUser()) {
            $shops = $this->getUserShops($this->getUser());

            // home for users
            return $this->render('home.html.twig', [
                'shops' => $shops,
            ]);
        } else {
            // index for guests
            return $this->render('index.html.twig');
        }
    }

    private function getUserShops($user)
    {
        //dummy data right now for test
        return [
            ['name' => 'Shop 1', 'inventory_count' => 10, 'sku_count' => 50],
            ['name' => 'Shop 2', 'inventory_count' => 5, 'sku_count' => 25],
        ];
    }
}


?>