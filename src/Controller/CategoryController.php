<?php

// src/Controller/CategoryController.php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Shop;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/shop/{shopId}/category/create', name: 'category_create')]
    public function create(Request $request, int $shopId): Response
    {
        $shop = $this->entityManager->getRepository(Shop::class)->find($shopId);

        if (!$shop) {
            throw $this->createNotFoundException('Shop not found!');
        }

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setShop($shop);
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'Category created successfully.');

            return $this->redirectToRoute('shop_dashboard', ['id' => $shopId]);
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(),
            'shop' => $shop,
        ]);
    }

    #[Route('/category/{id}', name: 'category_view')]
    public function view(Category $category): Response
    {
        return $this->render('category/view.html.twig', [
            'category' => $category,
        ]);
    }
}
