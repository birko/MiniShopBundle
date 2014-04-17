<?php

namespace Core\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Core\ShopBundle\Entity\Order;
use Core\ShopBundle\Entity\OrderFilter;
use Core\ShopBundle\Entity\Process;
use Core\ShopBundle\Entity\ProcessOrder;
use Core\ShopBundle\Form\OrderType;
use Core\ShopBundle\Form\OrderInvoiceType;
use Core\ShopBundle\Form\OrderFilterType;
use Core\ShopBundle\Form\ProcessType;

/**
 * Order controller.
 *
 */
class OrderController extends BaseOrderController
{
    /**
     * Lists all Order entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        //filter
        $session = $request->getSession();
        $filter = $session->get('adminorderfilter', new OrderFilter());
        if (empty($filter)) {
            $filter = new OrderFilter();
            $session->set('adminorderfilter', $filter);
        }
        if ($filter->getShippingState() != null) {
            $filter->setShippingState($em->merge($filter->getShippingState()));
        }
        if ($filter->getShippingStatus() != null) {
            $filter->setShippingStatus($em->merge($filter->getShippingStatus()));
        }
        if ($filter->getOrderStatus() != null) {
            $filter->setOrderStatus($em->merge($filter->getOrderStatus()));
        }

        $form   = $this->createForm(new OrderFilterType(), $filter);
        $page = $this->getRequest()->get('page', $filter->getPage());
        if ($request->getMethod() == "POST") {
            $form->bind($request);
            if ($form->isValid()) {
                $page = 1;
                $session->set('adminorderfilter', $filter);
            }
        }
        $filter->setPage($page);
        $session->set('adminorderfilter', $filter);

        $querybuilder = $em->getRepository('CoreShopBundle:Order')->createQueryBuilder('o');
        $querybuilder =  $em->getRepository('CoreShopBundle:Order')->filterOrderQuieryBuilder($querybuilder, $filter);
        $querybuilder->addOrderBy('o.createdAt', 'desc');
        $query = $querybuilder->getQuery();
        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $page/*page number*/,
            100/*limit per page*/
        );

        $processOrders = array();
        foreach ($pagination->getItems() as $order) {
            $processOrder = new ProcessOrder();
            $processOrder->setOrder($order);
            $processOrders[] = $processOrder;
        }
        $process = new Process();
        $process->setProcessOrders($processOrders);

        $export = $this->container->getParameter('admin.order.export');
        $processConfig = $this->container->getParameter('admin.order.process');
        $processForm = $this->createForm(new ProcessType(), $process, array('export' => $export, 'config' => $processConfig));

        return $this->render('CoreShopBundle:Order:index.html.twig', array(
            'entities' => $pagination,
            'form' => $form->createView(),
            'process' => $processForm->createView(),
            'processConfig' => $processConfig,
        ));
    }

    /**
     * Finds and displays a Order entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        return $this->render('CoreShopBundle:Order:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Order entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $editForm = $this->createForm(new OrderType(), $entity);

        return $this->render('CoreShopBundle:Order:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Order entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }
        $oldorderstatusid = null;
        $oldshippingstatusid = null;
        if ($entity->getOrderStatus() != null) {
             $oldorderstatusid  = $entity->getOrderStatus()->getId();
        }
        if ($entity->getShippingStatus() != null) {
             $oldshippingstatusid  = $entity->getShippingStatus()->getId();
        }
        $oldtrackingId = $entity->getTrackingId();
        $editForm   = $this->createForm(new OrderType(), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $neworderstatusid = null;
            $newshippingstatusid = null;
            if ($entity->getOrderStatus() != null) {
                 $neworderstatusid  = $entity->getOrderStatus()->getId();
            }
            if ($entity->getShippingStatus() != null) {
                 $newshippingstatusid  = $entity->getShippingStatus()->getId();
            }
            $newtrackingId = $entity->getTrackingId();
            if ($oldorderstatusid != $neworderstatusid || $oldshippingstatusid != $newshippingstatusid || $oldtrackingId != $newtrackingId) {
                $entity->setUpdatedAt(new \DateTime());
                $em->persist($entity);
                $em->flush();
                //emails
                $this->sendEmail($entity);
            }

            return $this->redirect($this->generateUrl('order'));
        }

        return $this->render('CoreShopBundle:Order:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    public function editInvoiceAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $editForm = $this->createForm(new OrderInvoiceType(), $entity);

        return $this->render('CoreShopBundle:Order:editinvoice.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Order entity.
     *
     */
    public function updateInvoiceAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreShopBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $editForm   = $this->createForm(new OrderInvoiceType(), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity->setInvoicedAt(new \DateTime());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('order'));
        }

        return $this->render('CoreShopBundle:Order:editinvoice.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    protected function sendEmail(Order $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sendEmail= $entity->getInvoiceEmail();
        $emails = $this->container->getParameter('default.emails');
        $name = ($entity->getOrderStatus()) ? $entity->getOrderStatus()->getName(): 'order-change';
        $ordernumber = $entity->getOrderNumber();
        $title = (!empty($ordernumber)) ? $ordernumber : $entity->getId();
        $trackingid = $entity->getTrackingId();
        if (!empty($trackingid)) {
            $title .= " " . $trackingid;
        }

        $t = $this->get('translator')->trans('Order No.:%title% changed status to %status% - %subject%', array(
            '%subject%' => $this->container->getParameter('site_title'),
            '%title%' => $title,
            '%status%' => $name,
         ));
        $message = \Swift_Message::newInstance()
            ->setSubject($t)
            ->setFrom($emails['default'], $this->container->getParameter('site_title'))   //settings
            ->setTo(array($sendEmail))
            ->setBody($this->renderView('CoreShopBundle:Email:orderstatus.html.twig', array(
                'order' => $entity,
                'text' => $em->getRepository('CoreUserTextBundle:UserText')->getUserText($name, false)
            )), 'text/html')
            ->setContentType("text/html");
        $this->get('swiftmailer.mailer.site_mailer')->send($message);
    }

    public function processAction()
    {
        $request = $this->getRequest();
        $process = new Process();
        $export = $this->container->getParameter('admin.order.export');
        $processForm = $this->createForm(new ProcessType(), $process, array('export' => $export));
        $processForm->bind($request);
        if ($processForm->isValid()) {
            $orders = array();
            foreach ($process->getProcessOrders() as $processOrder) {
                if ($processOrder->isInclude()) {
                    $orders[] = $processOrder->getOrderId();
                }
            }
            switch ($process->getType()) {
                case 'export':
                    if ($process->getExport()) {
                        return $this->exportAction($orders, $process->getExport());
                    }
                    break;
                case 'orderstatus':
                    if ($process->getOrderStatus()) {
                        return $this->orderStatusUpdateAction($orders, $process->getOrderStatus()->getId());
                    }
                    break;
                case 'shippingstatus':
                    if ($process->getShippingStatus()) {
                        return $this->shippingStatusUpdateAction($orders, $process->getShippingStatus()->getId());
                    }
                    break;
                case 'labels':
                default:
                    return $this->stickersAction($orders);
            }
        }

        return $this->redirect($this->generateUrl('order'));
    }

    public function stickersAction($orderIds = array())
    {
        $entities = array();
        if (!empty($orderIds)) {
            $em = $this->getDoctrine()->getManager();
            $entities = $em->getRepository('CoreShopBundle:Order')->getOrdersByIdQuery($orderIds)->getResult();
        }

        return $this->render('CoreShopBundle:Order:stickers.html.twig', array(
            'entities'      => $entities,
        ));
    }

    public function orderStatusUpdateAction($orderIds, $statusId)
    {
        if (!empty($orderIds)) {
            $em = $this->getDoctrine()->getManager();
            $count = $em->getRepository('CoreShopBundle:Order')->updateOrderStatus($orderIds, $statusId);
            if ($count > 0) {
                $entities = $em->getRepository('CoreShopBundle:Order')->getOrdersByIdQuery($orderIds)->getResult();
                foreach ($entities as $entity) {
                    $this->sendEmail($entity);
                }
            }
        }

        return $this->redirect($this->generateUrl('order'));
    }

    public function shippingStatusUpdateAction($orderIds, $statusId)
    {
        if (!empty($orderIds)) {
            $em = $this->getDoctrine()->getManager();
            $count = $em->getRepository('CoreShopBundle:Order')->updateShippingStatus($orderIds, $statusId);
            if ($count > 0) {
                $entities = $em->getRepository('CoreShopBundle:Order')->getOrdersByIdQuery($orderIds)->getResult();
                foreach ($entities as $entity) {
                    $this->sendEmail($entity);
                }
            }
        }

        return $this->redirect($this->generateUrl('order'));
    }

    public function exportAction($orderIds, $export)
    {

        if (!empty($orderIds) && !empty($export)) {
            $exportdata = $this->container->getParameter('admin.order.export');
            $definition = $exportdata[$export];
            $type = isset($definition['type']) ? $definition['type'] : 'csv';
            $em = $this->getDoctrine()->getManager();

            $entities = array();
            $querybuilder = $em->getRepository('CoreShopBundle:Order')->getOrdersByIdQueryBuilder($orderIds);
            switch ($definition['filter']) {
                case "product":
                    $expr = $querybuilder->expr()->isNotNull("oi.product");
                    $querybuilder->andWhere($expr);
                    break;
                case "order":
                    $querybuilder->resetDQLPart("join");
                    break;
            }
            switch ($type) {
                case "csv":
                default:
                    {
                        $select = array();
                        foreach ($definition['fields'] as $key => $field) {
                            foreach ($field['value'] as $key => $val) {
                                $select[]= $field['alias'].".".$val. " as " . $field['alias'].$val;
                            }
                        }
                        $entities = $querybuilder->select(implode(' ,', $select))->getQuery()->getScalarResult();
                    }
                    break;
            }

            $response = $this->render("CoreCommonBundle:Export:export." . $type . ".twig", array('entities' => $entities, 'fields' => $definition['fields']));
            $response->headers->set('Content-Type', 'text/' . $type);
            $date = new \DateTime();
            $response->headers->set('Content-Disposition', 'attachment; filename="'.$date->format("U")."-" . $export . '.csv"');

            return $response;
        }

        return $this->redirect($this->generateUrl('order'));
    }

}
