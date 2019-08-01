<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PanierController extends Controller
{
    public function ajouterAction(Request $request)
    {
        $id=$request->get("id");
        $qte=$request->get("qte");

        $session = $request->getSession();

        if(!$session->has('panier'))
            $session->set('panier', array());

        //$panier[id_produit] = quantité

        $panier = $session->get('panier');

        $panier[$id] = $qte;

        $session->set('panier' , $panier);

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Produit_consulter' , ["id" => $id ,"user" => $this->getUser()]));
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
        return $this->render('@Ecommerce/Panier/afficher.html.twig', [ "user" => $this->getUser() , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);

    }

    public function supprimerAction(Request $request)
    {
        $session = $request->getSession();
        $panier = $session->get('panier');


        //recuperer id du produit à supprimer
        $id = $request->get('id');

        if (array_key_exists($id,$panier)){
            unset($panier[$id]);
            $session->set('panier' , $panier);
        }

        $router = $this->container->get('router');

        return new RedirectResponse($router->generate("Panier_afficher"), 307);

    }

    public function updateAction(Request $request)
    {

        $session = $request->getSession();

        if(!$session->has('panier'))
            $session->set('panier', array());

        $em = $this->getDoctrine()->getManager();
        $produitpanier = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('panier'))));

        $panier = $session->get('panier');

        foreach ($produitpanier as $p){
            $qte=$request->get("qte".$p->getId());
            $panier[$p->getId()] = $qte;

            $session->set('panier' , $panier);
        }

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate("Panier_afficher"), 307);
    }



}
