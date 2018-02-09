<?php
/**
 * Created by PhpStorm.
 * User: jawad
 * Date: 2/6/2018
 * Time: 6:00 PM
 */

namespace AppBundle\Controller\Api;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategorieController
 * @package AppBundle\Controller\Api
 * @Route("/api")
 */
class CategorieController extends FOSRestController
{
    /**
     * @param Request $request
     * @return array|JsonResponse
     * @Rest\Get("/categories")
     */
    public function getCategoriesAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AppBundle:Categorie')->findAll();

        if(empty($categories))
            return new JsonResponse(['message' => 'n\'exist aucun categorie' ],Response::HTTP_NOT_FOUND);

        return $categories;
    }

}