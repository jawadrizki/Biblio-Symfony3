<?php
/**
 * Created by PhpStorm.
 * User: jawad
 * Date: 2/6/2018
 * Time: 11:23 AM
 */

namespace AppBundle\Controller\Api;


use AppBundle\Entity\Livre;
use AppBundle\Form\LivreType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class LivreController
 * @package AppBundle\Controller\Api
 * @Route("/api")
 */
class LivreController extends FOSRestController

{
    /**
     * @Rest\Get("/livres")
     *
     */
    public function getLivresAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $livres = $em->getRepository('AppBundle:Livre')->findAll();

        if(empty($livres))
            return new JsonResponse(['message' => 'n\'exist aucun livre' ],Response::HTTP_NOT_FOUND);

        return $livres;
    }

    /**
     * @Rest\Get("/livres/{id}", requirements={"id"="\d+"})
     *
     */
    public function getLivreAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $livre = $em->getRepository('AppBundle:Livre')->find($request->get('id'));
        if(empty($livre))
            return new JsonResponse(['message' => 'Cette livre n\'exist pas !' ],Response::HTTP_NOT_FOUND);

        return $livre;
    }

    /**
     * @Rest\Post("/livres")
     */

    public function postLivreAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $livre = new Livre();
        $res = json_decode($request->getContent(),true);

        $livre->setAuteur($res['livre']['auteur']);
        $categorie = $em->getRepository('AppBundle:Categorie')->find((int)$res['livre']['categorie']);
        $livre->setCategorie($categorie);
        $livre->setDate(new \DateTime($res['livre']['date']));
        $livre->setTitre($res['livre']['titre']);

        $em->persist($livre);
        $em->flush($livre);

        return $livre;

    }

    /**
     * @Rest\Put("/livres/{id}")
     */
    public function putLivreAction(Request $request){
        $livre = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Livre')
            ->find($request->get('id')); // L'identifiant en tant que paramètre n'est plus nécessaire


        if (empty($livre)) {
            return new JsonResponse(['message' => 'Livre not found'], Response::HTTP_NOT_FOUND);
        }


        $em = $this->getDoctrine()->getManager();

        $res = json_decode($request->getContent(),true);

        $livre->setAuteur($res['auteur']);
        $categorie = $em->getRepository('AppBundle:Categorie')->find((int)$res['categorie']);
        $livre->setCategorie($categorie);
        $livre->setDate(new \DateTime($res['date']));
        $livre->setTitre($res['titre']);

        $em->merge($livre);
        $em->flush();

        return new JsonResponse(['message' => 'success']);
    }

    /**
     * @Rest\Get("/livres/chercher")
     */

    public function getLivreByKeyword(Request $request){

        $em = $this->getDoctrine()->getManager();

        $keyword = $request->query->get('keyword');

        $livres = $em->getRepository('AppBundle:Livre')->findBy(['titre' => $keyword]);

        return $livres;

    }
}