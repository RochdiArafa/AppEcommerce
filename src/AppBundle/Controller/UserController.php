<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{

    public function updateAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //modifer user
        $user = $this->getUser();
        $user->setNom($request->get("nom"));
        $user->setPrenom($request->get("prenom"));
        $user->setAdresse($request->get("adresse"));
        $user->setTel($request->get("tel"));
        $user->setAvatar($request->get("avatar"));
        $user->setPassword($request->get("password"));
        $user->setPlainPassword($request->get("repeatPassword"));

        $em->persist($user);
        $em->flush();

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

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('user_consulter'));
    }

    public function goupdateAction(Request $request) {

        //recuperer l'id de categorie à modifier
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();

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

        return $this->render('@Ecommerce/User/modifier.html.twig' , ["user" => $this->getUser() ,  "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);

    }

    public function consulterAction(Request $request)
    {
        //recuperer l'id de categorie à modifier
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();

        //afficher tous les categories

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();

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
        return $this->render('@Ecommerce/User/consulter.html.twig' , ["user" => $this->getUser() ,  "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
    }
}
