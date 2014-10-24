<?php

namespace Core\CategoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\CategoryBundle\Entity\Category;
use Core\CategoryBundle\Form\CategoryType;
use Core\CategoryBundle\Form\CategoryParentType;
use Core\CommonBundle\Controller\TranslateController;

/**
 * Category controller.
 *
 */
class CategoryController extends TranslateController
{

    protected function saveTranslation($entity, $culture, $translation)
    {
        $em = $this->getDoctrine()->getManager();
        $slug = $translation->getSlug();
        $entity->setTitle($translation->getTitle());
        $entity->setTranslatableLocale($culture);
        $em->persist($entity);
        $em->flush();
        if ($translation->isExternal()) {
            $entity->setSlug($slug);
            $em->persist($entity);
            $em->flush();
        }
    }
    /**
     * Lists all Category entities.
     *
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $mid = $session->get('last_category_menu', 0);

        return $this->redirect($this->generateUrl('category_menu', array("menu" => $mid)));
    }

    public function menuAction($menu = 0)
    {
        $menu_index = $menu;
        $menus = $this->container->getParameter('menu');
        if (!is_numeric($menu_index)) {
            $menu_index = array_search($menu, $menus);
        }
        if ($menu_index === false || (count($menus) <= $menu_index)) {
            throw $this->createNotFoundException("Menu not found: ". $menu);
        } else {
            $session = $this->getRequest()->getSession();
            $session->set('last_category_menu', $menu);
            $em = $this->getDoctrine()->getManager();

            $minishop  = $this->container->getParameter('minishop');
            $controller =$this;
            $options = array(
                'decorate' => true,
                'rootOpen' => $this->renderView("CoreCategoryBundle:Category:Tree/rootOpen.html.twig"),
                'rootClose' => $this->renderView("CoreCategoryBundle:Category:Tree/rootClose.html.twig"),
                'childOpen' => function ($node) use ($controller) {
                    return $controller->renderView("CoreCategoryBundle:Category:Tree/childOpen.html.twig", array('node'=> $node));
                },
                'childClose' => $this->renderView("CoreCategoryBundle:Category:Tree/childClose.html.twig"),
                'nodeDecorator' => function ($node) use ($controller, $minishop) {
                    return $controller->renderView("CoreCategoryBundle:Category:Tree/nodeDecoration.html.twig", array(
                        "node" => $node,
                        "minishop" => $minishop,
                    ));
                },
            );

            $parentsQuery = $em->getRepository('CoreCategoryBundle:Category')->getTreeQuery($menu);
            $parents = $parentsQuery->getArrayResult();
            $tree = (count($parents)> 0) ? $em->getRepository('CoreCategoryBundle:Category')->buildTree($parents, $options) : "";

            return $this->render('CoreCategoryBundle:Category:index.html.twig', array(
            'tree' => $tree,
            'menus' => $menus,
            'menu' => $menu
            ));
        }
    }

    /**
     * Finds and displays a Category entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreCategoryBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreCategoryBundle:Category:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Category entity.
     *
     */
    public function newAction($menu, $parent = null)
    {
        $entity = new Category();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Category());
        $form   = $this->createForm(new CategoryType(), $entity, array('cultures' => $cultures));

        return $this->render('CoreCategoryBundle:Category:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'menu' => $menu,
            'parent' => $parent,
            'cultures' => $cultures
        ));
    }

    /**
     * Creates a new Category entity.
     *
     */
    public function createAction($menu, $parent = null)
    {
        $entity  = new Category();
        $request = $this->getRequest();
        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures, new Category());
        $form   = $this->createForm(new CategoryType(), $entity, array('cultures' => $cultures));
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($parent !== null) {
                $parententity = $em->getRepository('CoreCategoryBundle:Category')->find($parent);
                $entity->setParent($parententity);
            }
            $slug = $entity->getSlug();
            $entity->setMenu($menu);
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);
            if ($entity->isExternal()) {
                $entity->setSlug($slug);
                $em->persist($entity);
                $em->flush();
            }
            $this->saveTranslations($entity, $cultures);
            if ($entity->isHome()) {
                $em->getRepository('CoreCategoryBundle:Category')->updateHomeCategory($entity->getId());
            }

            return $this->redirect($this->generateUrl('category'));

        }

        return $this->render('CoreCategoryBundle:Category:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'menu' => $menu,
            'parent' => $parent,
            'cultures' => $cultures,
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreCategoryBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createForm(new CategoryType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreCategoryBundle:Category:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
     * Edits an existing Category entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreCategoryBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $cultures = $this->container->getParameter('core.cultures');
        $this->loadTranslations($entity, $cultures);
        $editForm = $this->createForm(new CategoryType(), $entity, array('cultures' => $cultures));
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $slug = $entity->getSlug();
            $em->persist($entity);
            $em->flush();
            if ($entity->isExternal()) {
                $entity->setSlug($slug);
                $em->persist($entity);
                $em->flush();
            }
            $this->saveTranslations($entity, $cultures);
            if ($entity->isHome()) {
                $em->getRepository('CoreCategoryBundle:Category')->updateHomeCategory($entity->getId());
            }

            return $this->redirect($this->generateUrl('category_edit', array('id' => $id)));
        }

        return $this->render('CoreCategoryBundle:Category:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'cultures'    => $cultures,
        ));
    }

    /**
     * Deletes a Category entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreCategoryBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('category'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Moves Category up
     *
     */
    public function moveUpAction($id, $position)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreCategoryBundle:Category')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $em->getRepository('CoreCategoryBundle:Category')->moveUp($entity, $position);

        return $this->redirect($this->generateUrl('category'));
    }

    /**
     * Moves Category down
     *
     */
    public function moveDownAction($id, $position)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreCategoryBundle:Category')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $em->getRepository('CoreCategoryBundle:Category')->moveDown($entity, $position);

        return $this->redirect($this->generateUrl('category'));
    }

    public function parentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreCategoryBundle:Category')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $form   = $this->createForm(new CategoryParentType(), $entity);

        return $this->render('CoreCategoryBundle:Category:parent.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    public function parentSaveAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreCategoryBundle:Category')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $form   = $this->createForm(new CategoryParentType(), $entity);
        $request = $this->getRequest();
        if ($request->getMethod() == "POST") {
            $form->bind($request);
            if ($form->isValid()) {
                $parententity = $entity->getParent();
                //if parententity is not child of entity
                if (empty($parententity) || (!($parententity->getLeft() >= $entity->getLeft() && $parententity->getLeft()<= $entity->getRight()))) {
                    if (!empty($parententity)) {
                        $entity->setMenu($parententity->getMenu());
                    }
                    $em->persist($entity);
                    $em->flush();

                    return $this->redirect($this->generateUrl('category'));
                }
            }
        }

        return $this->render('CoreCategoryBundle:Category:parent.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }
}
