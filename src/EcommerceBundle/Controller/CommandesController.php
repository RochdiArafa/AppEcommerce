<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Commandes;
use EcommerceBundle\Entity\lignecommande;
use EcommerceBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CommandesController extends Controller
{
    public function consulterAction(Request $request)
    {
        //recuperer id du commandes à consulter
        $id = $request->get('id');

        //recuperer la commades à consulter à partir de leur id
        $em = $this->getDoctrine()->getManager();
        $commandes = $em->getRepository("EcommerceBundle:Commandes")->find($id);
        $lignecommandes = $em->getRepository("EcommerceBundle:lignecommande")->findBy(["Commandes" => $commandes]);
        $commandes  = $this->get('knp_paginator')->paginate(
            $lignecommandes,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            8/*nbre d'éléments par page*/
        );

        return $this->render('@Ecommerce/LigneCommandes/afficher.html.twig' , [ "user" => $this->getUser(), "lignecommandes" => $lignecommandes ]);
    }

    public function ajouterAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $fullname = $request->get('fullname');
        $tel = $request->get('tel');
        $country = $request->get('country');
        $state = $request->get('state');
        $postcode = $request->get('postcode');
        $adresse = $request->get('adresse');

        //ajouter nouveau commande
        $commandes = new Commandes();
        $commandes->setpaye(false);
        $commandes->setDatecom(new \DateTime());
        $commandes->setUser($this->getUser());
        $commandes->setShippingdetaills($fullname." ,".$tel." ,".$country." ,".$state." ".$postcode." ".$adresse);
        $em->persist($commandes);
        $em->flush();

        //ajouter les ligne de commande qui apartient a ce commande

        $session = $request->getSession();

        if(!$session->has('panier'))
            $session->set('panier', array());

        if(!$session->has('favorie'))
            $session->set('favorie', array());

        $em = $this->getDoctrine()->getManager();
        $produitpanier = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('panier'))));
        $panier = $session->get('panier');

        foreach ($produitpanier as $produit){
            $lignecommande = new lignecommande();
            $lignecommande->setCommandes($commandes);
            $lignecommande->setProduit($produit);
            $lignecommande->setQuantite($panier[$produit->getId()]);
            $em->persist($lignecommande);
            $em->flush();

            //diminuer le quantite de produit aprés vente
            $produit = $em->getRepository("EcommerceBundle:Produit")->find($produit->getId());
            $produit->setStock( $produit->getStock() - $panier[$produit->getId()]);
            $em->persist($produit);
            $em->flush();
        }

        //ajouter un nouveau notification au vendeur
        //recuperer tous les admins
        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role'
            )->setParameter('role', 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}');

        $vendeur = $query->getResult();

        //parcourir la list des vendeur , et envoyer à chacun une notification
        for($i = 0; $i < count($vendeur); ++$i) {
            //ajouter nouveau notification
            $notification = new Notification();
            $notification->setTitle("Nouveau commande");
            $notification->setDescription("un nouveau commande a été affecter");
            $notification->setDaten(new \DateTime());
            $notification->setEtat("seen");
            $notification->setUser($vendeur[$i]);

            $em->persist($notification);
            $em->flush();
        }

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('ecommerce_homepage'));

    }

    public function afficherAction(Request $request)
    {
        //afficher tous les commandes

        $em = $this->getDoctrine()->getManager();
        $listeCommandes = $em->getRepository("EcommerceBundle:Commandes")->findAll();
        $commandes  = $this->get('knp_paginator')->paginate(
            $listeCommandes,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            8/*nbre d'éléments par page*/
        );

        return $this->render('@Ecommerce\Produit\list_dashboard.html.twig' , ["$commandes" => $commandes , "user" => $this->getUser()]);
    }


    public function supprimerAction(Request $request)
    {

        //recuperer id du lignecommande à supprimer
        $id_com = $request->get('id_com');
        $id_prod = $request->get('id_prod');


        //recuperer le produit à supprimer à partir de leur id
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository("EcommerceBundle:Produit")->find($id_prod);
        $commande = $em->getRepository("EcommerceBundle:Commandes")->find($id_com);

        $lignecommande = $em->getRepository("EcommerceBundle:lignecommande")->findBy(["Produit" => $produit , "Commandes" => $commande]);

        //remove ligne commande
        $em->remove($lignecommande);
        $em->flush();

        $router = $this->container->get('router');

        return new RedirectResponse($router->generate("lignecommande_list_dashboard"), 307);

    }
}
