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
        $color=$request->get("color");
        $size=$request->get("size");

        $session = $request->getSession();

        if(!$session->has('panier'))
            $session->set('panier', array());

        if(!$session->has('commande'))
            $session->set('commande', array());

        //$panier[id_produit] = quantité

        $panier = $session->get('panier');
        //$panier[$id] = ["qantite" => $qte , "size" => $size , "color" => $color];

        $commande = $session->get('commande');

        $panier[$id] = $qte;

        if($size != ""){
            $commande[$id] = ["size" => $size];
        }
        if($color != ""){
            $commande[$id] = ["color" => $color];
        }

        $session->set('panier' , $panier);
        $session->set('commande' , $commande);

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

        if(!$session->has('commande'))
            $session->set('commande', array());

        $em = $this->getDoctrine()->getManager();
        $produitpanier = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('panier'))));
        $produitfavorie = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('favorie'))));

        /*
        $commandeInfos = $session->get('commande');
        $size = "";
        foreach ($commandeInfos as $commandeInfo){
            $size = $commandeInfo['size'];
        }
        */

        //get error messages
        $fullnameMsg = $request->get('fullnameMsg');
        $telMsg = $request->get('telMsg');
        $countryMsg = $request->get('countryMsg');
        $stateMsg = $request->get('stateMsg');
        $postcodeMsg = $request->get('postcodeMsg');
        $adresseMsg = $request->get('adresseMsg');

        $router = $this->container->get('router');
        return $this->render('@Ecommerce/Panier/afficher.html.twig', [ "user" => $this->getUser() , "fullnameMsg" => $fullnameMsg , "telMsg" => $telMsg  , "countryMsg" => $countryMsg, "stateMsg" => $stateMsg, "postcodeMsg" => $postcodeMsg, "adresseMsg" => $adresseMsg, "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);

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
