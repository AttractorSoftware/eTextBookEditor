<?php

namespace eTextBook\SpawnBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\SpawnBundle\Form\BookType;

/**
 * Book controller.
 *
 * @Route("/spawn/book")
 */
class BookController extends Controller
{

    /**
     * Lists all Book entities.
     *
     * @Route("/", name="spawn_book")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $current_page = $this->container->get('request')->get('page') ? $this->container->get('request')->get('page') : 1;

        $builder = $em->getRepository('eTextBookLoungeBundle:Book')
        ->createQueryBuilder('e')
        ->setMaxResults(20)
        ->orderBy('e.id', 'desc');

        $entities = $builder->getQuery()->setFirstResult(($current_page-1)*20)->getResult();
        $pages = $builder->select('COUNT(e)')
        ->getQuery()
        ->getSingleScalarResult();

        $deleteForms = array();

        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
        }

        return array(
            'entities' => $entities,
            'pages' => ceil($pages/20),
            'current_page' => $current_page,
            'deleteForms' => $deleteForms
        );
    }
    /**
     * Creates a new Book entity.
     *
     * @Route("/", name="spawn_book_create")
     * @Method("POST")
     * @Template("eTextBookSpawnBundle:Book:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Book();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('spawn_book_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Book entity.
    *
    * @param Book $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Book $entity)
    {
        $form = $this->createForm(new BookType(), $entity, array(
            'action' => $this->generateUrl('spawn_book_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Book entity.
     *
     * @Route("/new", name="spawn_book_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Book();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Book entity.
     *
     * @Route("/{id}", name="spawn_book_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('eTextBookLoungeBundle:Book')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Book entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Book entity.
     *
     * @Route("/{id}/edit", name="spawn_book_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('eTextBookLoungeBundle:Book')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Book entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Book entity.
    *
    * @param Book $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Book $entity)
    {
        $form = $this->createForm(new BookType(), $entity, array(
            'action' => $this->generateUrl('spawn_book_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Book entity.
     *
     * @Route("/{id}", name="spawn_book_update")
     * @Method("PUT")
     * @Template("eTextBookSpawnBundle:Book:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('eTextBookLoungeBundle:Book')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Book entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('spawn_book_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Book entity.
     *
     * @Route("/{id}", name="spawn_book_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('eTextBookLoungeBundle:Book')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Book entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('spawn_book'));
    }

    /**
     * Creates a form to delete a Book entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('spawn_book_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
