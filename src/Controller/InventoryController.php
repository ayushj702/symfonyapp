<?php


namespace App\Controller;

use App\Entity\Shop;
use App\Entity\Inventory;
use App\Form\InventoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\MoveInventoryType;


class InventoryController extends AbstractController
{
    #[Route('/shop/{shopId}/inventory/add', name: 'inventory_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, int $shopId): Response
    {
        $shop = $entityManager->getRepository(Shop::class)->find($shopId);

        if (!$shop) {
            throw $this->createNotFoundException('Shop not found!');
        }

        $inventory = new Inventory();
        $form = $this->createForm(InventoryType::class, $inventory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inventory->setShop($shop);
            $entityManager->persist($inventory);
            $this->addFlash('success', 'Inventory added successfully.');
            $entityManager->flush();

            return $this->redirectToRoute('inventory_dashboard', [
                'shopId' => $shopId,
                'inventoryId' => $inventory->getId()]
            );
        }

        return $this->render('inventory/add.html.twig', [
            'form' => $form->createView(),
            'shop' => $shop,
        ]);
    }

    #[Route('/shop/{shopId}/inventory/{inventoryId}/dashboard', name: 'inventory_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager, int $shopId, int $inventoryId): Response
    {
        $shop = $entityManager->getRepository(Shop::class)->find($shopId);
        $inventory = $entityManager->getRepository(Inventory::class)->find($inventoryId);

        if (!$shop) {
            throw $this->createNotFoundException('Shop not found!');
        }

        // if (!$inventory || $inventory->getShop()->getId() !== $shop->getId()) {
        //     throw $this->createNotFoundException('Inventory not found or does not belong to the given shop.');
        // }

        return $this->render('inventory/dashboard.html.twig', [
            'shop' => $shop,
            'inventory' => $inventory,
        ]);
    }

    #[Route('/inventory/{id}/delete', name: 'inventory_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Inventory $inventory): Response
    {
        if ($inventory->getShop()->getOwner() !== $this->getUser()) {
            // Ensures that only the shop owner can delete the inventory.
            throw $this->createAccessDeniedException('You are not allowed to delete this inventory.');
        }

        $entityManager->remove($inventory);
        $entityManager->flush();

        return $this->redirectToRoute('inventory_dashboard', ['shopId' => $inventory->getShop()->getId()]);
    }

    #[Route('/shop/{shopId}/inventory/move', name: 'inventory_move')]
    public function move(Request $request, EntityManagerInterface $entityManager, int $shopId): Response
    {
        $shop = $entityManager->getRepository(Shop::class)->find($shopId);
        if (!$shop) {
            throw $this->createNotFoundException('Shop not found!');
        }

        $form = $this->createForm(MoveInventoryType::class, null, ['shop' => $shop]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $inventory = $entityManager->getRepository(Inventory::class)->find($data['inventoryItem']);
            //dd($inventory->data);
            $targetShop = $entityManager->getRepository(Shop::class)->find($data['targetShop']);

            if ($inventory && $targetShop) {
                $inventory->setShop($targetShop);
                $entityManager->persist($inventory);
                $entityManager->flush();

                $this->addFlash('success', 'Inventory moved successfully.');
                return $this->redirectToRoute('inventory_dashboard', [
                    'shopId' => $shopId,
                    'inventoryId' => $inventory->getId(),
                ]);
            }

            $this->addFlash('error', 'Invalid operation.');
        }

        return $this->render('inventory/move.html.twig', [
            'form' => $form->createView(),
            'shop' => $shop,
        ]);
    }


    
}
