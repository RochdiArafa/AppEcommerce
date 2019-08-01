<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Categorie;
use EcommerceBundle\Entity\Fournisseur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FournisseurController extends Controller
{

    public function ajouterAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //ajouter nouveau fournisseur
        $fournisseur = new Fournisseur();
        $fournisseur->setNom($request->get("nom"));
        $fournisseur->setTelephone($request->get("telephone"));
        $fournisseur->setLogo("logo");

        $em->persist($fournisseur);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Fournisseur_list_dashboard'));

    }

    public function afficherAction()
    {
        //afficher tous les fournisseur

        $em = $this->getDoctrine()->getManager();
        $fournisseurs = $em->getRepository("EcommerceBundle:Fournisseur")->findAll();


        return $this->render('@Ecommerce\Fournisseur\list.html.twig' , ["user" => $this->getUser() , "fournisseurs" => $fournisseurs]);
    }

    public function supprimerAction(Request $request)
    {

        //recuperer id du Fournisseur à supprimer
        $id = $request->get('id');

        //recuperer le Fournisseur à supprimer à partir de leur id
        $em = $this->getDoctrine()->getManager();
        $fournisseur = $em->getRepository("EcommerceBundle:Fournisseur")->find($id);

        //remove categorie
        $em->remove($fournisseur);
        $em->flush();

        $router = $this->container->get('router');

        return new RedirectResponse($router->generate("Fournisseur_list_dashboard"), 307);

    }

    public function goajouterAction(Request $request)
    {
        return $this->render('@Ecommerce\Fournisseur\ajouter.html.twig' , ["user" => $this->getUser()] );

    }

    public function updateAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $fournisseur = $em->getRepository("EcommerceBundle:Fournisseur")->find($request->get("id"));

        $fournisseur->setNom($request->get("nom"));
        $fournisseur->setTelephone($request->get("telephone"));

        $em->persist($fournisseur);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Fournisseur_list_dashboard'));

    }

    public function goupdateAction(Request $request) {

        //recuperer l'id de Fournisseur à modifier
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $fournisseur = $em->getRepository("EcommerceBundle:Fournisseur")->find($id);

        return $this->render('@Ecommerce/Fournisseur/modifier.html.twig' , [ "user" => $this->getUser() , "fournisseur" => $fournisseur]);

    }


}
