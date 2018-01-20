<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Livre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;

/**
 * Livre controller.
 *
 * @Route("livre")
 */
class LivreController extends Controller
{
    /**
     * Lists all livre entities.
     *
     * @Route("/", name="livre_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $livres = $em->getRepository('AppBundle:Livre')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $livres, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('size', 5)/*limit per page*/
        );


        return $this->render('livre/index.html.twig', array(
            'livres' => $pagination,
            'pagination' => $pagination
        ));
    }


    public function searchBarAction(){
        $form = $this->createFormBuilder(null)
            ->add('keyword',TextType::class)
            ->add('submit',SubmitType::class)
            ->getForm();
        return $this->render('livre/search.html.twig',
                ['form' => $form->createView()]
            );
    }
    /**
     * @Route("/search", name="handelSearch")
     */
    public function handelSearchAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $keyword = '%' . $request->request->get('form')['keyword'] . '%';

        $query = $em->createQuery(
            'SELECT l from AppBundle:Livre l WHERE l.titre LIKE :keyword'
        )->setParameter('keyword','%'.$keyword.'%' );
        $livres = $query->getResult();


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $livres, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('size', 5)/*limit per page*/
        );


        return $this->render('livre/index.html.twig', array(
            'livres' => $pagination,
            'pagination' => $pagination
        ));
    }


    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $keyword = $request->query->get('keyword');

        $livres = $em->getRepository('AppBundle:Livre')->findBy(['titre' => $keyword]);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $livres, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('size', 5)/*limit per page*/
        );


        return $this->render('livre/search.html.twig', array(
            'livres' => $pagination,
            'pagination' => $pagination
        ));
    }

    /**
     * Creates a new livre entity.
     *
     * @Route("/new", name="livre_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $livre = new Livre();
        $form = $this->createForm('AppBundle\Form\LivreType', $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($livre);
            $em->flush($livre);

            return $this->redirectToRoute('livre_show', array('id' => $livre->getId()));
        }

        return $this->render('livre/new.html.twig', array(
            'livre' => $livre,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a livre entity.
     *
     * @Route("/{id}", name="livre_show")
     * @Method("GET")
     */
    public function showAction(Livre $livre)
    {
        $deleteForm = $this->createDeleteForm($livre);

        return $this->render('livre/show.html.twig', array(
            'livre' => $livre,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing livre entity.
     *
     * @Route("/{id}/edit", name="livre_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Livre $livre)
    {
        $deleteForm = $this->createDeleteForm($livre);
        $editForm = $this->createForm('AppBundle\Form\LivreType', $livre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('livre_edit', array('id' => $livre->getId()));
        }

        return $this->render('livre/edit.html.twig', array(
            'livre' => $livre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a livre entity.
     *
     * @Route("/{id}", name="livre_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Livre $livre)
    {
        $form = $this->createDeleteForm($livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($livre);
            $em->flush($livre);
        }

        return $this->redirectToRoute('livre_index');
    }

    /**
     * Creates a form to delete a livre entity.
     *
     * @param Livre $livre The livre entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Livre $livre)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('livre_delete', array('id' => $livre->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
