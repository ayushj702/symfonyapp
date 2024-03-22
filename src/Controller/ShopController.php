<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Form\ShopType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ShopController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/shops', name: 'shop_list')]
    public function list(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            // when user is not logged in
            throw $this->createAccessDeniedException('You must be logged in to view this page.');
        }

        $shops = $user->getShops();

        return $this->render('shop/list.html.twig', ['shops' => $shops]);
    }

    #[Route('/shop/create', name: 'shop_create')]
    public function create(Request $request): Response
    {
        $shop = new Shop();
        $form = $this->createForm(ShopType::class, $shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shop->setOwner($this->getUser());
            $this->entityManager->persist($shop);
            $this->entityManager->flush();

            return $this->redirectToRoute('shop_dashboard', ['id' => $shop->getId()]);
        }

        return $this->render('shop/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/shop/{id}/dashboard', name: 'shop_dashboard')]
    public function dashboard(Shop $shop): Response
    {
        // Example logic for SKU count; you'll have your implementation.
        $skuCount = 0; // Placeholder for actual SKU count logic.
        
        // Now using getInventories() based on the updated Shop entity.
        $inventories = $shop->getInventories();

        return $this->render('shop/dashboard.html.twig', [
            'shop' => $shop,
            'skuCount' => $skuCount,
            'inventories' => $inventories,
        ]);
    }
}
