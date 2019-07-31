<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FavorieController extends Controller
{
    public function ajouterAction(Request $request)
    {
        $id=$request->get("id");

        $session = $request->getSession();

        if(!$session->has('favorie'))
            $session->set('favorie', array());

        //$favorie[id_produit] = 1

        $favorie = $session->get('favorie');

        $favorie[$id] = 1;

        $session->set('favorie' , $favorie);

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('ecommerce_homepage'));
    }

    public function afficherAction(Request $request)
    {
        $session = $request->getSession();

        if(!$session->has('panier'))
            $session->set('panier', array());

        if(!$session->has('favorie'))
            $session->set('favorie', array());

        $em = $this->getDoctrine()->getManager();
        $produitpanier = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('panier'))));
        $produitfavorie = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('favorie'))));

        $router = $this->container->get('router');
        return $this->render('@Ecommerce/Favorie/afficher.html.twig', [ "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);

    }

    public function supprimerAction(Request $request)
    {
        $session = $request->getSession();
        $favorie = $session->get('favorie');


        //recuperer id du produit à supprimer
        $id = $request->get('id');

        if (array_key_exists($id,$favorie)){
            unset($favorie[$id]);
            $session->set('favorie' , $favorie);
        }

        $router = $this->container->get('router');

        return new RedirectResponse($router->generate("Favorie_afficher"), 307);

    }

}
