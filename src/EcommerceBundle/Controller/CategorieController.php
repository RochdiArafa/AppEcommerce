<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CategorieController extends Controller
{

    public function ajouterAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //ajouter nouveau categorie
        $categorie = new Categorie();
        $categorie->setNom($request->get("nom"));
        $categorie->setDescription($request->get("description"));

        $em->persist($categorie);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Categorie_list_dashboard'));

    }

    public function afficherAction()
    {
        //afficher tous les categories

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();


        return $this->render('@Ecommerce\Categorie\list.html.twig' , ["categories" => $categories]);
    }

    public function supprimerAction(Request $request)
    {

        //recuperer id du categorie à supprimer
        $id = $request->get('id');

        //recuperer le categorie à supprimer à partir de leur id
        $em = $this->getDoctrine()->getManager();
        $categorie = $em->getRepository("EcommerceBundle:Categorie")->find($id);

        //remove categorie
        $em->remove($categorie);
        $em->flush();

        $router = $this->container->get('router');

        return new RedirectResponse($router->generate("Categorie_list_dashboard"), 307);

    }

    public function goajouterAction(Request $request)
    {
        return $this->render('@Ecommerce\Categorie\ajouter.html.twig' );

    }

    public function updateAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //ajouter nouveau categorie
        $categorie = $em->getRepository("EcommerceBundle:Categorie")->find($request->get("id"));
        $categorie->setNom($request->get("nom"));
        $categorie->setDescription($request->get("description"));

        $em->persist($categorie);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Categorie_list_dashboard'));

    }

    public function goupdateAction(Request $request) {

        //recuperer l'id de categorie à modifier
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $categorie = $em->getRepository("EcommerceBundle:Categorie")->find($id);

        return $this->render('@Ecommerce/Categorie/modifier.html.twig' , ["categorie" => $categorie]);

    }



}
