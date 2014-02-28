<?php

namespace Core\UserTextBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\UserTextBundle\Entity\UserText;
use Core\UserTextBundle\Form\UserTextType;
use Core\UserTextBundle\Form\EditUserTextType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * UserText controller.
 *
 */
class UserTextController extends TranslateController
{
    protected function saveTranslation($entity, $culture, $translation)
    {
        $em = $this->getDoctrine()->getManager();
        $entity->setText($translation->getText());
        $entity->setTranslatableLocale($culture);
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Lists all UserText entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreUserTextBundle:UserText')->createQueryBuilder("ut")
            ->addOrderBy("ut.name", "asc")
            ->addOrderBy("ut.id", "asc")
            ->getQuery()
            ->getResult();

        return $this->render('CoreUserTextBundle:UserText:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a UserText entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserTextBundle:UserText')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserText entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreUserTextBundle:UserText:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new UserText entity.
     *
     */
    public function newAction()
    {
        $entity = new UserText();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new UserText());
        $form   = $this->createForm(new UserTextType(), $entity, array('cultures' => $cultures));

        return $this->render('CoreUserTextBundle:UserText:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
     * Creates a new UserText entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new UserText();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new UserText());
        $form   = $this->createForm(new UserTextType(), $entity, array('cultures' => $cultures));
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('usertext'));
        }

        return $this->render('CoreUserTextBundle:UserText:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'cultures' => $cultures,
        ));
    }

    /**
     * Displays a form to edit an existing UserText entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserTextBundle:UserText')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserText entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createForm(new EditUserTextType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreUserTextBundle:UserText:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
     * Edits an existing UserText entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserTextBundle:UserText')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserText entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createForm(new EditUserTextType(), $entity, array('cultures' => $cultures));
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->saveTranslations($entity, $cultures);

            return $this->redirect($this->generateUrl('usertext_edit', array('id' => $id)));
        }

        return $this->render('CoreUserTextBundle:UserText:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
     * Deletes a UserText entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreUserTextBundle:UserText')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserText entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('usertext'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function displayAction($name, $create = false)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserTextBundle:UserText')->getUserText($name, $create);

        return $this->render('CoreUserTextBundle:UserText:display.html.twig', array(
            'entity'      => $entity,
        ));
    }
}
