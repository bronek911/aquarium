<?php

namespace waterZooBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use waterZooBundle\Entity\Decoration;

/**
 * @Route("/decoration")
 */
class DecorationController extends Controller
{

    /**
     * @Route("/")
     * @Template("waterZooBundle:Decoration:index.html.twig")
     * @Method("GET")
     */
    public function returnAllDecorationAction()
    {

        $repository = $this->getDoctrine()->getRepository('waterZooBundle:Decoration');

        $allDecorations = $repository->findAll();

        return $this->render('@waterZoo/Decoration/index.html.twig', array(
            'decorations' => $allDecorations,
        ));
    }

    /**
     * @Route("/new")
     * @Template("waterZooBundle:Decoration:new.html.twig")
     * @Method({"GET", "POST"})
     */
    public function addDecoration(Request $request)
    {

        $newDecoration = new Decoration();


        if ($request->getMethod() === "POST") {
            $newDecoration->setName($request->get('name'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($newDecoration);
            $em->flush();

            return $this->redirectToRoute('waterzoo_decoration_show', array('id' => $newDecoration->getId()));
        }

        return $this->render('@waterZoo/Decoration/new.html.twig', array(
            'decoration' => $newDecoration,
        ));

    }

    /**
     * @Route("/{id}")
     * @Method("GET")
     */
    public function showAction(Decoration $decoration)
    {
        $deleteForm = $this->createDeleteForm($decoration);

        return $this->render('@waterZoo/Decoration/show.html.twig', array(
            'decoration' => $decoration,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a client entity.
     *
     * @Route("/{id}")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Decoration $decoration)
    {
        $form = $this->createDeleteForm($decoration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($decoration);
            $em->flush();
        }

        return $this->redirectToRoute('waterzoo_decoration_returnalldecoratios');
    }


    /**
     * Add to the tank.
     *
     * @Route("/{id}/add")
     * @Method({"GET", "POST"})
     */
    public function addToTankAction(Request $request, Decoration $decoration)
    {
        $repo = $this->getDoctrine()->getRepository('waterZooBundle:Tank');
        $tanks = $repo->findAll();

        if ($request->getMethod() === "POST") {

            $getTankId = $request->get('tankId');
            $tank = $repo->find($getTankId);

            $decoration->addToTank($tank);

            $em = $this->getDoctrine()->getManager();
            $em->persist($decoration);
            $em->flush();

            return $this->redirectToRoute('waterzoo_decoration_show', array('id' => $decoration->getId()));

        }

        return $this->render('@waterZoo/Default/addToTank.html.twig', array(
            'entity' => 'decoration',
            'item' => $decoration,
            'tanks' => $tanks,
        ));
    }

    /**
     * Removes from the tank.
     *
     * @Route("/{id}/remove")
     * @Method("GET")
     */
    public function removeFromTankAction(Request $request, Decoration $decoration)
    {
        $decoration->removeFromTank();

        $em = $this->getDoctrine()->getManager();
        $em->persist($decoration);
        $em->flush();

        return $this->redirectToRoute('waterzoo_decoration_show', array('id' => $decoration->getId()));
    }


    /**
     * Creates a form to delete a decoration entity.
     *
     * @param Decoration $decoration The decoration entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Decoration $decoration)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('waterzoo_decoration_delete', array('id' => $decoration->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
