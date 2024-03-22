<?php

// src/Controller/ProductVariationController.php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductVariation;
use App\Form\ProductVariationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductVariationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    #[Route('/product/{productId}/variation/create', name: 'product_variation_create')]
    public function create(Request $request, int $productId): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('Product not found!');
        }

        $variation = new ProductVariation();
        $form = $this->createForm(ProductVariation::class, $variation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $variation->setProduct($product);
            $this->entityManager->persist($variation);
            $this->entityManager->flush();

            return $this->redirectToRoute('product_view', ['id' => $productId]);
        }

        return $this->render('product_variation/create.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/variation/{id}', name: 'product_variation_view')]
    public function view(ProductVariation $variation): Response
    {
        return $this->render('product_variation/view.html.twig', [
            'variation' => $variation,
        ]);
    }


    #[Route('/product/{productId}/variation/add', name: 'product_variation_add')]
    public function addVariation(Request $request, EntityManagerInterface $entityManager, int $productId): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);
        if (!$product) {
            throw $this->createNotFoundException('Product not found!');
        }

        $variation = new ProductVariation();
        $variation->setProduct($product);
        $form = $this->createForm(ProductVariationType::class, $variation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($variation);
            $entityManager->flush();
            $this->addFlash('success', 'Variation added successfully.');
            return $this->redirectToRoute('product_edit', ['id' => $productId]);
        }

        return $this->render('product_variation/add.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/product/variation/{id}/remove', name: 'product_variation_remove')]
    public function removeVariation(ProductVariation $variation, EntityManagerInterface $entityManager): Response
    {
        $productId = $variation->getProduct()->getId();
        $entityManager->remove($variation);
        $entityManager->flush();

        $this->addFlash('success', 'Variation removed successfully.');

        return $this->redirectToRoute('product_edit', ['id' => $productId]);
    }

    
}
