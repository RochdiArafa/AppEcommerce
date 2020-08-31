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

        return $this->render('@Ecommerce/Commande/consulter.html.twig' , [ "user" => $this->getUser(), "lignecommandes" => $commandes ]);
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

        //manage errors message
        $fullnameMsg = "";
        $telMsg = "";
        $countryMsg = "";
        $stateMsg = "";
        $postcodeMsg = "";
        $adresseMsg = "";

        if($fullname == ""){
            $fullnameMsg = "Full Name is required";
        }
        if($tel == ""){
            $telMsg = "Mobile is required";
        }
        if($country == ""){
            $countryMsg = "Country is required";
        }
        if($state == ""){
            $stateMsg = "State is required";
        }
        if($postcode == ""){
            $postcodeMsg = "Postcode is required";
        }
        if($adresse == ""){
            $adresseMsg = "Adresse is required";
        }

        if($fullname != "" && $tel != "" && $country != "" && $state != "" && $postcode != "" && $adresse != ""){
            //ajouter nouveau commande
            $commandes = new Commandes();
            $commandes->setpaye(false);
            $commandes->setDatecom(new \DateTime());
            $commandes->setUser($this->getUser());
            $commandes->setShippingdetaills($fullname." ,".$tel." ,".$country." ,".$state." ".$postcode." ".$adresse);
            $commandes->setEtat("En cour");
            $em->persist($commandes);
            $em->flush();

            //ajouter les ligne de commande qui apartient a ce commande

            $session = $request->getSession();

            if(!$session->has('panier'))
                $session->set('panier', array());

            if(!$session->has('favorie'))
                $session->set('favorie', array());

            if(!$session->has('commande'))
                $session->set('commande', array());

            $em = $this->getDoctrine()->getManager();
            $produitpanier = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('panier'))));
            $panier = $session->get('panier');
            $commandeInfos = $session->get('commande');

            foreach ($produitpanier as $produit){
                $lignecommande = new lignecommande();
                $lignecommande->setCommandes($commandes);
                $lignecommande->setProduit($produit);
                $lignecommande->setQuantite($panier[$produit->getId()]);
                foreach ($commandeInfos as $key => $value) {
                    if($key == $produit->getId()){
                        $lignecommande->setSize($value['size']);
                        $lignecommande->setColor($value['color']);
                    }
                }
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


            //vider le panier
            $session = $request->getSession();
            $panier = $session->get('panier');

            $produitpanier = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('panier'))));

            foreach ($produitpanier as $produit)
                if (array_key_exists($produit->getId(),$panier)){
                    unset($panier[$produit->getId()]);
                    $session->set('panier' , $panier);
                }

            $router = $this->container->get('router');
            return new RedirectResponse($router->generate('ecommerce_homepage'));
        }else{
            $router = $this->container->get('router');
            return new RedirectResponse($router->generate('Panier_afficher' , [ "fullnameMsg" => $fullnameMsg , "telMsg" => $telMsg  , "countryMsg" => $countryMsg, "stateMsg" => $stateMsg, "postcodeMsg" => $postcodeMsg, "adresseMsg" => $adresseMsg]));
        }





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

        return $this->render('@Ecommerce\Commande\list.html.twig' , ["commandes" => $commandes , "user" => $this->getUser()]);
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
        $em->remove($lignecommande[0]);
        $em->flush();

        $lignecommande = $em->getRepository("EcommerceBundle:lignecommande")->findBy(["Commandes" => $commande]);

        if(count($lignecommande) == 0) {
            //remove commande
            $em->remove($commande);
            $em->flush();

            $router = $this->container->get('router');

            return new RedirectResponse($router->generate("My_Commande_list_home" ), 307);
        }
        else{
            $router = $this->container->get('router');

            return new RedirectResponse($router->generate("Commande_consulter_home" , ["id" => $id_com]), 307);
        }

    }

    public function changeretatAction(Request $request)
    {
        $id = $request->get('id');
        $etat = $request->get('etat');


        //changer l'etat de la commande
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository("EcommerceBundle:Commandes")->find($id);
        $commande->setEtat($etat);
        $em->persist($commande);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate("Commande_list_dashboard"), 307);

    }

    public function affichermyAction(Request $request)
    {
        //afficher tous les commandes

        $em = $this->getDoctrine()->getManager();
        $listeCommandes = $em->getRepository("EcommerceBundle:Commandes")->findBy(['User'=> $this->getUser()]);
        $commandes  = $this->get('knp_paginator')->paginate(
            $listeCommandes,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            8/*nbre d'éléments par page*/
        );

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
        return $this->render('@Ecommerce/Commande/mylist.html.twig' , [ "commandes" => $commandes ,"user" => $this->getUser() , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
        }

    public function monlistconsulterAction(Request $request)
    {
        //recuperer id du commandes à consulter
        $id = $request->get('id');

        //recuperer la commades à consulter à partir de leur id
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository("EcommerceBundle:Commandes")->find($id);
        $lignecommandes = $em->getRepository("EcommerceBundle:lignecommande")->findBy(["Commandes" => $commande]);
        $commandes  = $this->get('knp_paginator')->paginate(
            $lignecommandes,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            8/*nbre d'éléments par page*/
        );

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

        return $this->render('@Ecommerce/Commande/consultermylist.html.twig' , ["lignecommandes" => $commandes , "etat" => $commande->getEtat()  ,"user" => $this->getUser() , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie') ]);
    }

    public function updateligneAction(Request $request)
    {
        $id_com = $request->get('id_com');
        $id_prod = $request->get('id_prod');
        $quantite = $request->get('qte'.$id_prod);


        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository("EcommerceBundle:Produit")->find($id_prod);
        $commande = $em->getRepository("EcommerceBundle:Commandes")->find($id_com);

        $lignecommandes = $em->getRepository("EcommerceBundle:lignecommande")->findBy(["Produit" => $produit , "Commandes" => $commande]);

        $lignecommandes[0]->setQuantite($quantite);
        $em->persist($lignecommandes[0]);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate("Commande_consulter_home" , ["id" => $id_com]), 307);
    }


}
