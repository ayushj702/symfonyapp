<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Inventory;
use App\Form\MoveProductType;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\ProductVariation;

class ProductController extends AbstractController
{
    #[Route('/shop/{shopId}/inventory/{inventoryId}/product/create', name: 'product_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, int $inventoryId): Response
    {
        $inventory = $entityManager->getRepository(Inventory::class)->find($inventoryId);
        if (!$inventory) {
            throw $this->createNotFoundException('Inventory not found!');
        }

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setInventory($inventory);
            foreach ($product->getVariations() as $variation) {
                $variation->generateUniqueSKU();
                $variation->setProduct($product);
                $entityManager->persist($variation);
            }
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product added successfully.');
            return $this->redirectToRoute('inventory_dashboard', [
                'shopId' => $inventory->getShop()->getId(),
                'inventoryId' => $inventoryId,
            ]);
        }

        return $this->render('product/add.html.twig', [
            'form' => $form->createView(),
            'inventory' => $inventory,
        ]);
    }

    #[Route('/product/{id}/delete', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $inventoryId = $product->getInventory()->getId();
            $shopId = $product->getInventory()->getShop()->getId();

            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('inventory_dashboard', ['shopId' => $shopId, 'inventoryId' => $inventoryId]);
    }

    #[Route('/product/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Product $product): Response {
        // Clone the original variations to compare later
        $originalVariations = new ArrayCollection();
        foreach ($product->getVariations() as $variation) {
            $originalVariations->add(clone $variation);
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($product->getVariations() as $variation) {
                if (empty($variation->getSku())) {
                    // Call the auto-generate method if SKU is empty
                    $variation->generateUniqueSKU();
                }
                if (!$originalVariations->contains($variation)) {
                    // New variation
                    $variation->setProduct($product);
                    $entityManager->persist($variation);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Product updated successfully.');
            return $this->redirectToRoute('inventory_dashboard', ['shopId' => $product->getInventory()->getShop()->getId(), 'inventoryId' => $product->getInventory()->getId()]);
        }

        return $this->render('product/edit.html.twig', ['product' => $product, 'form' => $form->createView()]);
    }




    #[Route('/product/{productId}/move', name: 'product_move')]
    public function moveProduct(Request $request, EntityManagerInterface $entityManager, int $productId): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($productId);
        if (!$product) {
            throw $this->createNotFoundException('Product not found!');
        }

        $form = $this->createForm(MoveProductType::class, null, ['product' => $product]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $targetInventory = $data['targetInventory'];
            $quantityToMove = $data['quantity'];

            if ($quantityToMove > $product->getQuantity()) {
                $this->addFlash('error', 'Not enough stock to move.');
            } else {
                // Move product and its variations
                $product->setInventory($targetInventory);
                foreach ($product->getVariations() as $variation) {
                    $variation->setProduct($product); 
                    $entityManager->persist($variation);
                }

                $entityManager->persist($product);
                $entityManager->flush();

                $this->addFlash('success', 'Product and its variations moved successfully.');
            }

            return $this->redirectToRoute('inventory_dashboard', [
                'shopId' => $targetInventory->getShop()->getId(),
                'inventoryId' => $targetInventory->getId(),
            ]);
        }

        return $this->render('product/move.html.twig', [
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
