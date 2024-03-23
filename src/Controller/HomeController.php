<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // check log in
        if ($this->getUser()) {
            $user = new User;

            $shops = $user->getShops();

            // home for users
            return $this->render('home.html.twig', [
                'shops' => $shops,
            ]);
        } else {
            // index for guests
            return $this->render('index.html.twig');
        }
    }

}


?>