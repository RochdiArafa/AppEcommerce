<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function homeAction(Request $request)
    {
        //afficher tous les categories

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();

        $produits = $em->getRepository("EcommerceBundle:Produit")->findAll();

        $session = $request->getSession();

        if(!$session->has('panier'))
            $session->set('panier', array());

        $em = $this->getDoctrine()->getManager();
        $produitpanier = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('panier'))));

        return $this->render('@Ecommerce/Default/home.html.twig' , ["produits" => $produits , "categories" => $categories , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier')]);
    }

    public function dashboardAction()
    {
        return $this->render('@Ecommerce/Default/dashboard.html.twig');
    }
}
