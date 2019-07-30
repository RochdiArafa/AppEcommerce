<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProduitController extends Controller
{

    public function consulterAction()
    {
        return $this->render('@Ecommerce/Produit/consulter.html.twig');
    }

    public function ajouterAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //ajouter nouveau PRODUIT
        $produit = new Produit();
        $produit->setNom($request->get("nom"));
        $produit->setDescription($request->get("description"));

        $em->persist($produit);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Produit_list_dashboard'));

    }

    public function afficherAction()
    {
        //afficher tous les categories

        $em = $this->getDoctrine()->getManager();
        $produits = $em->getRepository("EcommerceBundle:Produit")->findAll();

        return $this->render('@Ecommerce\Produit\list_dashboard.html.twig' , ["produits" => $produits]);
    }
}
