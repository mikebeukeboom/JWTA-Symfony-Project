<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Item;
use AppBundle\Entity\ItemLog;
use AppBundle\Entity\User;
use AppBundle\Form\form_NewItem;
use AppBundle\Form\ItemLogType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\Ldap;

class ItemController extends Controller
{
    /**
     * @Route("/", name="item_controller")
     */
    public function indexAction()
    {
        $items = $this->getDoctrine()
            ->getRepository(Item::class)
            ->findAll();

        $logs = $this->getDoctrine()
            ->getRepository(ItemLog::class)
            ->findAll();
        return $this->render(':items:index.html.twig', array(
            'items' => $items,
            'logs' => $logs
        ));
    }

    /**
     * @Route("/user2db", name="usertodatabase")
     */
    public function ldapToDB()
    {
        $domain = 'emea.corp.jwt.com';
        $username = 'xeroxprinters@emea.corp.jwt.com';
        $password = 'JWTAaccess01';
        $dc = 'OU=EMEA-AMS-29-JWT-JWTAmsterdam,OU=EMEA-AMS-29,OU=EMEA-AMS,DC=emea,DC=corp,DC=jwt,DC=com';
        $filter = "(&(objectClass=user)(mail=*)(objectCategory=person))";


        $ldap = Ldap::create('ext_ldap', array(
            'host' => $domain,
            'encryption' => 'none',
            'version' => 3,
            'debug' => false,
            'referrals' => false,
        ));


        $ldap->bind($username, $password);
        $q = $ldap->query($dc, $filter);
        $domain = $q->execute();
        $users = $domain->toArray();

        $em = $this->getDoctrine()->getManager();
        
        foreach ($users as $user) {
            $existingUser = $em->getRepository("AppBundle:User")->findOneBy([
                "email" => $user->getAttributes()['mail'][0]
            ]);
            $entityNewEntry = new User();
            $entityNewEntry->setUid($user->getAttributes()['sAMAccountName'][0]);
            $entityNewEntry->setEmail($user->getAttributes()['mail'][0]);
            $entityNewEntry->setFullname($user->getAttributes()['name'][0]);
            if ($existingUser !== null) {

            } else {
                $em->persist($entityNewEntry);
                $em->flush();
            }
        }

        return $this->redirectToRoute('item_controller');

    }

    /**
     * Create new Rental Item
     * @Route("/new", name="item_new")
     * @Method({"GET","POST"})
     */
    public function newAction(Request $request)
    {
        $item = new Item();
        $form = $this->createForm(form_NewItem::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('item_controller', array());
        }
        return $this->render(":items:item_new.html.twig", array(
            'item' => $item,
            'form' => $form->createView()
        ));
    }

    /**
     * Show the item
     * @Route("/{id}", name="item_show")
     * @Method({"GET","POST"})
     */
    public function showAction(Request $request, Item $item)
    {
        $deleteForm = $this->createDeleteForm($item);

        $itemLogForm = $this->createForm(ItemLogType::class);
        $itemLogForm->handleRequest($request);
        if ($itemLogForm->isSubmitted() && $itemLogForm->isValid()) {


            /** @var ItemLog $itemLog */
            $itemLog = $itemLogForm->getData();
            $itemLog->setItem($item);
            $item->setIsAvailable(false);

            $this->getDoctrine()->getManager()->persist($itemLog);
            $this->getDoctrine()->getManager()->flush();
        }



        return $this->render(':items:item_show.html.twig', array(
            'item' => $item,
            'delete_form' => $deleteForm->createView(),
            'item_log_form' => $itemLogForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Item entity.
     *
     * @param Item $item The Item entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Item $item)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('item_delete', array('id' => $item->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Edit a Item
     * @Route("/{id}/edit", name="item_edit")
     * @Method({"GET","POST"})
     */
    public function editAction(Request $request, Item $item)
    {
//        $deleteForm = $this->createDeleteform($item);
        $editForm = $this->createForm(form_NewItem::class, $item);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('item_controller');
        }
        return $this->render(':items:edit.html.twig', array(
            'item' => $item,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Item entity.
     *
     * @Route("/{id}", name="item_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Item $item)
    {
        $form = $this->createDeleteForm($item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();
        }

        return $this->redirectToRoute('item_controller');
    }

    /**
     * Return an item
     *
     * @Route("/{id}/return/{itemLog}", name="item_return")
     * @Method({"GET"})
     * @param Item $item
     * @param ItemLog $itemLog
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function returnAction(Item $item, ItemLog $itemLog)
    {

        $itemLog->returnItem();
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
    }



}
