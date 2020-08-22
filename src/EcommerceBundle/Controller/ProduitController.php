<?php

namespace EcommerceBundle\Controller;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use EcommerceBundle\Entity\Produit;
use EcommerceBundle\ImageUpload;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProduitController extends Controller
{

    public function consulterAction(Request $request)
    {
        //recuperer id du produit à consulter
        $id = $request->get('id');

        //recuperer le produit à consulter à partir de leur id
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository("EcommerceBundle:Produit")->find($id);

        $aviss= $em->getRepository("EcommerceBundle:Avis")->findBy(["Produit" => $produit]);
        $TotalEtoile = 0;
        foreach($aviss as $avis) {
            $TotalEtoile=$TotalEtoile + $avis->getNombre();
        }
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
        return $this->render('@Ecommerce/Produit/consulter.html.twig' , [ "user" => $this->getUser(), "produit" => $produit , "aviss" => $aviss , "TotalEtoile" => $TotalEtoile ,  "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
    }

    public function ajouterAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //ajouter nouveau PRODUIT
        $produit = new Produit();
        $produit->setNom($request->get("nom"));
        $produit->setDescription($request->get("description"));

        $categorie= $em->getRepository("EcommerceBundle:Categorie")->findBy(['nom' => $request->get("categorie")]);

        if(count($categorie)!= null)
            $produit->setCategorie($categorie[0]);

        $produit->setColor($request->get("color"));
        $produit->setDimensions($request->get("dimensions"));

        $fournisseur= $em->getRepository("EcommerceBundle:Fournisseur")->findBy(['nom' => $request->get("fournisseur")]);

        if(count($fournisseur)!= null)
            $produit->setFournisseur($fournisseur[0]);

        /*
        $uploadimg = new ImageUpload(__DIR__."/../../../../web/Image/produit");
        $produit->setImage(new UploadedFile($request->get("image") , $request->get("image") , null , null , null , null , false ));

        */
        $produit->setImage("chemises.jpg");


        $produit->setMaterials($request->get("materials"));
        $produit->setPrix($request->get("prix"));
        $produit->setSize($request->get("size"));
        $produit->setStock($request->get("stock"));
        $produit->setWeight($request->get("weight"));

        $validator = $this->get('validator');
        $errors = $validator->validate($produit);

        $errorsString = null;

        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();
        $fournisseurs = $em->getRepository("EcommerceBundle:Fournisseur")->findAll();


        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->render('@Ecommerce\Produit\ajouter.html.twig' , ["user" => $this->getUser() , "categories" => $categories , "fournisseurs" => $fournisseurs , "error" => "ne doit pas etre vide !" ] );

        }

        $em->persist($produit);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Produit_list_dashboard'));

    }

    public function afficherAction(Request $request)
    {
        //afficher tous les produits

        $em = $this->getDoctrine()->getManager();
        $listproduits = $em->getRepository("EcommerceBundle:Produit")->findAll();
        $produits  = $this->get('knp_paginator')->paginate(
            $listproduits,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            8/*nbre d'éléments par page*/
        );

        return $this->render('@Ecommerce\Produit\list_dashboard.html.twig' , ["produits" => $produits , "user" => $this->getUser()]);
    }


    public function supprimerAction(Request $request)
    {

        //recuperer id du produit à supprimer
        $id = $request->get('id');

        //recuperer le produit à supprimer à partir de leur id
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository("EcommerceBundle:Produit")->find($id);

        //remove produit
        $em->remove($produit);
        $em->flush();

        $router = $this->container->get('router');

        return new RedirectResponse($router->generate("Produit_list_dashboard"), 307);

    }

    public function goajouterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();
        $fournisseurs = $em->getRepository("EcommerceBundle:Fournisseur")->findAll();

        return $this->render('@Ecommerce\Produit\ajouter.html.twig' , ["user" => $this->getUser() , "categories" => $categories , "fournisseurs" => $fournisseurs , "error" => ""  ] );

    }

    public function updateAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //recuperer le produit à modifier
        $produit = $em->getRepository("EcommerceBundle:Produit")->find($request->get("id"));

        $produit->setNom($request->get("nom"));
        $produit->setDescription($request->get("description"));

        $categorie= $em->getRepository("EcommerceBundle:Categorie")->findBy(['nom' => $request->get("categorie")]);

        $produit->setCategorie($categorie[0]);
        $produit->setColor($request->get("color"));
        $produit->setDimensions($request->get("dimensions"));

        $fournisseur= $em->getRepository("EcommerceBundle:Fournisseur")->findBy(['nom' => $request->get("fournisseur")]);

        $produit->setFournisseur($fournisseur[0]);


        $produit->setImage("chemises.jpg");


        $produit->setMaterials($request->get("materials"));
        $produit->setPrix($request->get("prix"));
        $produit->setSize($request->get("size"));
        $produit->setStock($request->get("stock"));
        $produit->setWeight($request->get("weight"));

        $validator = $this->get('validator');
        $errors = $validator->validate($produit);

        $errorsString = null;

        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();
        $fournisseurs = $em->getRepository("EcommerceBundle:Fournisseur")->findAll();


        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->render('@Ecommerce\Produit\modifier.html.twig' , ["user" => $this->getUser() ,"produit" => $produit,"categories" => $categories , "fournisseurs" => $fournisseurs , "error" => "ne doit pas etre vide !"  ] );

        }


        $em->persist($produit);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Produit_list_dashboard'));

    }

    public function goupdateAction(Request $request) {

        //recuperer l'id de Produit à modifier
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository("EcommerceBundle:Produit")->find($id);

        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();
        $fournisseurs = $em->getRepository("EcommerceBundle:Fournisseur")->findAll();

        return $this->render('@Ecommerce\Produit\modifier.html.twig' , ["user" => $this->getUser() ,"produit" => $produit,"categories" => $categories , "fournisseurs" => $fournisseurs , "error" => ""  ] );
    }

    public function afficherFrontAction( Request $request)
    {
        //afficher tous les categories

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();

        $produits = $em->getRepository("EcommerceBundle:Produit")->findAll();

        $session = $request->getSession();

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

        return $this->render('@Ecommerce/Produit/afficher_front.html.twig' , ["user" => $this->getUser() ,"produits" => $produits , "categories" => $categories , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
    }

    public function statiqueAction()
    {
        $pieChart = new PieChart();
        $em= $this->getDoctrine();
        $produits = $em->getRepository("EcommerceBundle:Produit")->findAll();
        $lignecommandes = $em->getRepository("EcommerceBundle:lignecommande")->findAll();

        $totalStock =0;
        foreach($produits as $produit) {
            $totalStock=$totalStock + $produit->getStock();
        }

        $totalvendue = 0;
        foreach($lignecommandes as $lignecommande) {
            $totalvendue=$totalvendue + $lignecommande->getQuantite();
        }

        $data= array();
        $stat=['classe', 'nbEtudiant'];
        $nb=0;
        array_push($data,$stat);

        $stat=array();
        if($totalStock != 0){
            array_push($stat,"All Stock",($totalStock *100)/($totalStock + $totalvendue));
            $nb=($totalStock *100)/($totalStock + $totalvendue);
            array_push($stat,"All Stock",($totalStock *100)/($totalStock + $totalvendue));
            $nb=($totalStock *100)/($totalStock + $totalvendue);

            $stat=["All Stock",$nb];
            array_push($data,$stat);

            $stat=array();
            array_push($stat,"Sell",($totalvendue *100)/($totalStock + $totalvendue));
            $nb=($totalvendue *100)/($totalStock + $totalvendue);
            $stat=["Sell",$nb];
            array_push($data,$stat);
            $msg = "";

        }
        else{
            array_push($stat,"All Stock",0);
            $nb=0;
            array_push($stat,"All Stock",0);
            $nb=0;

            $stat=["All Stock",$nb];
            array_push($data,$stat);

            $stat=array();
            array_push($stat,"Sell",0);
            $nb=0;
            $stat=["Sell",$nb];
            array_push($data,$stat);

            $msg = "Pas de produit disponible ! ";
        }


        $pieChart->getData()->setArrayToDataTable(
            $data
        );
        $pieChart->getOptions()->setTitle('Pourcentages des produit vendu par rapport à tous les produits');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);
        return $this->render('@Ecommerce/Default/dashboard.html.twig', array('piechart' => $pieChart , "user" => $this->getUser() , "msg" => $msg ));
    }

    public function rechercherfrontAction( Request $request)
    {
        //afficher tous les categories

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();

        $nom = $request->get('nom');

        if ($nom != "") {
            $query = $this->getDoctrine()->getEntityManager()
                ->createQuery(
                    "SELECT u FROM EcommerceBundle:Produit u WHERE u.nom LIKE :nomp"
                )->setParameter('nomp', $nom);

            $produits = $query->getResult();
        }
        else
            $produits = $em->getRepository("EcommerceBundle:Produit")->findAll();

        $session = $request->getSession();

        $session = $request->getSession();

        if(!$session->has('panier'))
            $session->set('panier', array());

        if(!$session->has('favorie'))
            $session->set('favorie', array());

        $em = $this->getDoctrine()->getManager();
        $produitpanier = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('panier'))));
        $produitfavorie = $em->getRepository("EcommerceBundle:Produit")->findArray(array_keys(($session->get('favorie'))));

        $favorie = $session->get('favorie');

        $session->set('favorie' , $favorie);

        return $this->render('@Ecommerce/Produit/home_rechercher.html.twig' , ["user" => $this->getUser() ,"produits" => $produits , "categories" => $categories , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
    }


}
