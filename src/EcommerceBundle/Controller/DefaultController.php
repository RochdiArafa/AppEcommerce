<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function homeAction(Request $request)
    {
        //afficher tous les categories

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository("EcommerceBundle:Categorie")->findAll();

        $produits = $em->getRepository("EcommerceBundle:Produit")->findAll();

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

        return $this->render('@Ecommerce/Default/home.html.twig' , ["user" => $this->getUser() ,"produits" => $produits , "categories" => $categories , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
    }

    public function dashboardAction()
    {
        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Admin_dashboard_statique'));
    }

    public function contactAction(Request $request)
    {
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
        return $this->render('@Ecommerce/Default/contact.html.twig' , ["user" => $this->getUser() , "produitspanier" => $produitpanier ,  "panier" => $session->get('panier') , "produitsfavorie" => $produitfavorie ,  "favorie" => $session->get('favorie')]);
    }


    public function sendmailAction(Request $request)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('keeptooui@gmail.com')
            ->setTo($request->get("email"))
            ->setBody($request->get("msg"));

        $this->get('mailer')->send($message);

        // or, you can also fetch the mailer service this way
        // $this->get('mailer')->send($message);

        $router = $this->container->get('router');
        return new RedirectResponse($router->generate('Contact_home'));
    }


}
