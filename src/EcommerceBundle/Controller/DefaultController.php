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

        if(!$session->has('favorie'))
            $session->set('favorie', array());

        $em = $this->getDoctrine()->getManager();
        $produitpanier = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('panier'))));
        $produitfavorie = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('favorie'))));

        //$favorie[id_produit] = 1

        $favorie = $session->get('favorie');

        $session->set('favorie' , $favorie);

        return $this->render('@Ecommerce/Default/home.html.twig' , ["user" => $this->getUser() ,"produits" => $produits , "categories" => $categories , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
    }

    public function dashboardAction()
    {
        return $this->render('@Ecommerce/Default/dashboard.html.twig', ["user" => $this->getUser()]);
    }
}
