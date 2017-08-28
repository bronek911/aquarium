<?php

namespace waterZooBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use waterZooBundle\Entity\Tank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/tank")
 */
class TankController extends Controller
{


    /**
     * @Route("/")
     * @Template("waterZooBundle:Tank:tank.html.twig")
     * @Method("GET")
     */
    public function returnAllTanksAction()
    {

        $repository = $this->getDoctrine()->getRepository('waterZooBundle:Tank');

        $allTanks = $repository->findBy(array(
            'parentId' => null
        ));

        return $this->render('@waterZoo/Tank/tank.html.twig', array(
            'tanks' => $allTanks,
        ));
    }

    /**
     * @Route("/new")
     * @Template("waterZooBundle:Tank:tank.html.twig")
     * @Method({"GET", "POST"})
     */
    public function addTank(Request $request)
    {

        $newTank = new Tank();


        if ($request->getMethod() === "POST") {
            $newTank->setCapacity($request->get('capacity'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($newTank);
            $em->flush();

            return $this->redirectToRoute('waterzoo_tank_show', array('id' => $newTank->getId()));
        }

        return $this->render('@waterZoo/Tank/new.html.twig', array(
            'tank' => $newTank,
        ));

    }

    /**
     * @Route("/{id}")
     * @Method("GET")
     */
    public function showAction(Tank $tank)
    {
        $deleteForm = $this->createDeleteForm($tank);

        $entities = array();
        $em = $this->getDoctrine()->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            $entities[] = explode('\\', $m->getName())[2];
        }

        $repositories = [];

        foreach ($entities as $entity) {
            $repositories[$entity] = $this->getDoctrine()->getRepository('waterZooBundle:' . $entity);
        }

        $items = [];
        foreach ($entities as $entity) {
            $items[$entity] = $repositories[$entity]->findBy(
                array('parentId' => $tank->getId())
            );
        }

        return $this->render('@waterZoo/Tank/show.html.twig', array(
            'tank' => $tank,
            'items' => $items,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a client entity.
     *
     * @Route("/{id}")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Tank $tank)
    {
        $form = $this->createDeleteForm($tank);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tank);
            $em->flush();
        }

        return $this->redirectToRoute('waterzoo_tank_returnalltanks');
    }

    /**
     * Creates a form to delete a tank entity.
     *
     * @param Tank $tank The tank entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Tank $tank)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('waterzoo_tank_delete', array('id' => $tank->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/{id}/new")
     * @Template("waterZooBundle:Tank:addItem.html.twig")
     * @Method({"GET", "POST"})
     */
    public function addItemToTankAction(Request $request, Tank $tank)
    {
        $entities = array();
        $em = $this->getDoctrine()->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            $entities[] = explode('\\', $m->getName())[2];
        }

        $repositories = [];

        foreach ($entities as $entity) {
            $repositories[$entity] = $this->getDoctrine()->getRepository('waterZooBundle:' . $entity);
        }

        //Searching for items that belongs to tank

        $items = [];
        foreach ($entities as $entity) {
            $items[$entity] = $repositories[$entity]->findBy(
                array('parentId' => $tank->getId())
            );
        }

        //Searching for available items by null value in parentId column

        $allAvaFishes = $repositories['Fish']->findBy(array(
            'parentId' => null
        ));

        $allAvaDecorations = $repositories['Decoration']->findBy(array(
            'parentId' => null
        ));

        //TODO:

        $allTanks = $repositories['Tank']->findAll();

//        echo "<pre>";
//        print_r($allTanks);
//        echo "</pre>";
//        die;

        $allAvaTanks = $repositories['Tank']->findBy(array(
            'parentId' => null
        ));

        //Creating array of available items

        $availableItems = array(
            'fish' => array(),
            'decoration' => array(),
            'tank' => array(),
        );

        foreach ($allAvaFishes as $fish) {
            $availableItems['fish'][] = $fish;
        }
        foreach ($allAvaDecorations as $decoration) {
            $availableItems['decoration'][] = $decoration;
        }

        foreach ($allAvaTanks as $avaTank) {
            $avaTankId = $avaTank->getId();
            $i = 0;
            foreach ($allTanks as $allTank) {
                $allTankId = $allTank->getParentId();

                if ($avaTankId === $allTankId) {
                    $i++;
                    break;
                }
            }
            if ($i == 0) {
                $availableItems['tank'][] = $avaTank;
            }

        }

        //Handling data from POST

        if ($request->getMethod() === "POST") {

            $getNewItem = $request->get('addingItem');

            $newItem = explode('_', $getNewItem);
            $type = $newItem[0];
            $itemId = $newItem[1];

            $item = null;

            foreach ($entities as $entity) {
                if ($type === strtolower($entity)) {
                    $item = $repositories[$entity]->find($itemId);
                    break;
                }
            }

            $item->addToTank($tank);

            $em = $this->getDoctrine()->getManager();
            $em->persist($tank);
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('waterzoo_tank_show', array('id' => $tank->getId()));
        }

        return $this->render('@waterZoo/Tank/addItem.html.twig', array(
            'tank' => $tank,
            'items' => $items,
            'avaItems' => $availableItems,
            'tables' => $entities,
        ));

    }


}
