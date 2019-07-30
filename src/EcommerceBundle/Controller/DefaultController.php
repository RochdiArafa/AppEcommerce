<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function homeAction()
    {
        //afficher tous les categories

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();

        $produits = $em->getRepository("EcommerceBundle:Produit")->findAll();


        return $this->render('@Ecommerce/Default/home.html.twig' , ["produits" => $produits , "categories" => $categories]);
    }

    public function dashboardAction()
    {
        return $this->render('@Ecommerce/Default/dashboard.html.twig');
    }
}
