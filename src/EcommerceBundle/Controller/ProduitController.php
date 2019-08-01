<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        return $this->render('@Ecommerce/Produit/consulter.html.twig' , ["produit" => $produit , "aviss" => $aviss , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
    }

    public function ajouterAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //ajouter nouveau PRODUIT
        $produit = new Produit();
        $produit->setNom($request->get("nom"));
        $produit->setDescription($request->get("description"));

        $categorie= $em->getRepository("EcommerceBundle:Categorie")->findBy(['nom' => $request->get("categorie")]);

        $produit->setCategorie($categorie[0]);
        $produit->setColor($request->get("color"));
        $produit->setDimensions($request->get("dimensions"));

        $fournisseur= $em->getRepository("EcommerceBundle:Fournisseur")->findBy(['nom' => $request->get("fournisseur")]);

        $produit->setFournisseur($fournisseur[0]);
        $produit->setImage("image");
        $produit->setMaterials($request->get("materials"));
        $produit->setPrix($request->get("prix"));
        $produit->setSize($request->get("size"));
        $produit->setStock($request->get("stock"));
        $produit->setWeight($request->get("weight"));


        $em->persist($produit);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Produit_list_dashboard'));

    }

    public function afficherAction()
    {
        //afficher tous les produits

        $em = $this->getDoctrine()->getManager();
        $produits = $em->getRepository("EcommerceBundle:Produit")->findAll();


        return $this->render('@Ecommerce\Produit\list_dashboard.html.twig' , ["produits" => $produits ]);
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

        return $this->render('@Ecommerce\Produit\ajouter.html.twig' , ["categories" => $categories , "fournisseurs" => $fournisseurs  ] );

    }

    public function updateAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //ajouter nouveau produit
        $produit = $em->getRepository("EcommerceBundle:Produit")->find($request->get("id"));

        $produit->setNom($request->get("nom"));
        $produit->setDescription($request->get("description"));

        $categorie= $em->getRepository("EcommerceBundle:Categorie")->findBy(['nom' => $request->get("categorie")]);

        $produit->setCategorie($categorie[0]);
        $produit->setColor($request->get("color"));
        $produit->setDimensions($request->get("dimensions"));

        $fournisseur= $em->getRepository("EcommerceBundle:Fournisseur")->findBy(['nom' => $request->get("fournisseur")]);

        $produit->setFournisseur($fournisseur[0]);
        $produit->setImage("image");
        $produit->setMaterials($request->get("materials"));
        $produit->setPrix($request->get("prix"));
        $produit->setSize($request->get("size"));
        $produit->setStock($request->get("stock"));
        $produit->setWeight($request->get("weight"));


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

        return $this->render('@Ecommerce\Produit\modifier.html.twig' , ["produit" => $produit,"categories" => $categories , "fournisseurs" => $fournisseurs  ] );
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

        return $this->render('@Ecommerce/Produit/afficher_front.html.twig' , ["produits" => $produits , "categories" => $categories , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
    }
}
