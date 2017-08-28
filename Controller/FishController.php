<?php

namespace waterZooBundle\Controller;

use waterZooBundle\Entity\Fish;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/fish")
 */
class FishController extends Controller
{

    /**
     * @Route("/")
     * @Template("waterZooBundle:Fish:index.html.twig")
     * @Method("GET")
     */
    public function returnAllFishesAction()
    {


        $repository = $this->getDoctrine()->getRepository('waterZooBundle:Fish');

        $allFishes = $repository->findAll();

        return $this->render('@waterZoo/Fish/index.html.twig', array(
            'fishes' => $allFishes,
        ));
    }

    /**
     * @Route("/new")
     * @Template("waterZooBundle:Fish:new.html.twig")
     * @Method({"GET", "POST"})
     */
    public function addFish(Request $request)
    {

        $newFish = new Fish();


        if ($request->getMethod() === "POST") {
            $newFish->setName($request->get('name'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($newFish);
            $em->flush();

            return $this->redirectToRoute('waterzoo_fish_show', array('id' => $newFish->getId()));
        }

        return $this->render('@waterZoo/Fish/new.html.twig', array(
            'fish' => $newFish,
        ));

    }

    /**
     * @Route("/{id}")
     * @Method("GET")
     */
    public function showAction(Fish $fish)
    {

        $deleteForm = $this->createDeleteForm($fish);

        return $this->render('@waterZoo/Fish/show.html.twig', array(
            'fish' => $fish,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a client entity.
     *
     * @Route("/{id}")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Fish $fish)
    {
        $form = $this->createDeleteForm($fish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($fish);
            $em->flush();
        }

        return $this->redirectToRoute('waterzoo_fish_returnallfishes');
    }


    /**
     * Removes from the tank.
     *
     * @Route("/{id}/remove")
     * @Method("GET")
     */
    public function removeFromTankAction(Request $request, Fish $fish)
    {
        $fish->removeFromTank();

        $em = $this->getDoctrine()->getManager();
        $em->persist($fish);
        $em->flush();

        return $this->redirectToRoute('waterzoo_fish_show', array('id' => $fish->getId()));
    }


    /**
     * Add to the tank.
     *
     * @Route("/{id}/add")
     * @Method({"GET", "POST"})
     */
    public function addToTankAction(Request $request, Fish $fish)
    {
        $repo = $this->getDoctrine()->getRepository('waterZooBundle:Tank');
        $tanks = $repo->findAll();

        if ($request->getMethod() === "POST") {

            $getTankId = $request->get('tankId');
            $tank = $repo->find($getTankId);

            $fish->addToTank($tank);

            $em = $this->getDoctrine()->getManager();
            $em->persist($fish);
            $em->flush();

            return $this->redirectToRoute('waterzoo_fish_show', array('id' => $fish->getId()));

        }


        return $this->render('@waterZoo/Default/addToTank.html.twig', array(
            'entity' => 'fish',
            'item' => $fish,
            'tanks' => $tanks,
        ));
    }

    /**
     * Creates a form to delete a tank entity.
     *
     * @param Fish $fish The fish entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Fish $fish)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('waterzoo_fish_delete', array('id' => $fish->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}
