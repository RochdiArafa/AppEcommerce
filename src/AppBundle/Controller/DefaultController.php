<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $authChecker = $this->container->get('security.authorization_checker');
        $router = $this->container->get('router');

        //verfier si l'utilisateur connecter est un admin pour acceder à admin dashboard
        if ($authChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            return $this->redirectToRoute('dashboard_admin');
        }


        //verfier si l'utilisateur connecter est un membre  pour acceder à son interface
        if ($authChecker->isGranted('ROLE_USER')) {
            return new RedirectResponse($router->generate('ecommerce_homepage'), 307);
        }

        //visiteur
        return new RedirectResponse($router->generate('ecommerce_homepage'), 307);
    }
}
