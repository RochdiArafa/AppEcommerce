<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Avis;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


class AvisController extends Controller
{
    public function ajouterAction(Request $request)
    {
        //recuperer id du produit
        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        //ajouter nouveau avis
        $avis = new Avis();
        $avis->setNombre($request->get("rating"));
        $avis->setDescription($request->get("description"));
        $avis->setUser($this->getUser());

        $produit= $em->getRepository("EcommerceBundle:Produit")->find($id);

        $avis->setProduit($produit);
        $em->persist($avis);
        $em->flush();

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate("Produit_consulter" , ['id'=> $produit->getId()]));

    }

    public function afficherAction()
    {
        //afficher tous les categories

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();


        return $this->render('@Ecommerce\Categorie\list.html.twig' , ["categories" => $categories]);
    }
}
