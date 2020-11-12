<?php

namespace App\Controller;

use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart.index")
     */
    public function index(CartService $cartService)
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total'  => $cartService->getTotal()
        ]);
    }
    /**
     * @Route("/panier/add/{id}", name="cart.add")
     */
    public function add($id, CartService $cartService)
    {
        $cartService->add($id);
        return $this->redirectToRoute("cart.index");
    }

    /**
     * @Route("/panier/remove/{id}", name="cart.remove")
     */
    public function remove($id, CartService $cartService)
    {
        $cartService->remove($id);
        return $this->redirectToRoute('cart.index');
    }

}
