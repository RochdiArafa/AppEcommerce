<?php

namespace EcommerceBundle\Controller;

use EcommerceBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\RedirectResponse;

class NotificationController extends Controller
{


    public function ajouterAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

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
        return new RedirectResponse($router->generate('Produit_list_dashboard'));

    }

    public function listAction()
    {
        //afficher tous les notifications de l'utilisateur

        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository("EcommerceBundle:Notification")->findBy(["User" => $this->getUser()]);

        //return new JsonResponse(array( 'notifications'=> $notifications));
        //return new JsonResponpaise(array( 'notifications'=> $notifications));
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($notifications);
        return new JsonResponse($formatted);

    }


    public function supprimerAction(Request $request)
    {

        //recuperer id du $notification à supprimer
        $id = $request->get('id');

        //recuperer le notification à supprimer à partir de leur id
        $em = $this->getDoctrine()->getManager();
        $notification = $em->getRepository("EcommerceBundle:Notification")->find($id);

        //remove produit
        $em->remove($notification);
        $em->flush();

        $router = $this->container->get('router');

        return new RedirectResponse($router->generate("dashboard_admin"), 307);

    }
}
