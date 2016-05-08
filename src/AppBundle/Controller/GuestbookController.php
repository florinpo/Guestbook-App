<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Guestbook;
use AppBundle\Form\Type\GuestbookType;
use AppBundle\Form\Type\GuestbookSearchType;

class GuestbookController extends Controller
{
  /**
   * @Route("/guestbook/list/{page}", name="guestbook_main", defaults={"page": 1}, requirements={
   *    "page": "\d+"
   * })
   */
  public function indexAction(Request $request, $page)
  {
    $repository = $this->getDoctrine()->getRepository('AppBundle:Guestbook');
    $query = $repository->getListBy();

    if ($request->isMethod('POST')) {
      if ($request->request->has('guestbook_search')) {
        $criteria = $request->request->get('guestbook_search');
        $query = $repository->getListBy($criteria);
      }
    }

    $paginator = $this->get('knp_paginator');
    $pagination = $paginator->paginate(
      $query,
      $page
    );

    return $this->render('guestbook/list.html.twig', array('pagination' => $pagination));
  }

  /**
   * @Route("/guestbook/new", name="guestbook_new")
   */
  public function newAction(Request $request)
  {
    $guestbook = new Guestbook();
    $form = $this->createForm(GuestbookType::class, $guestbook);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $imageHandler = $this->get('image.handling');
      $guestbook->processFile();
      $em->persist($guestbook);
      $em->flush();

      // manipulate image after save
      $imageHandler = $this->get('image.handling');

      $tempDir = $this->container->getParameter('uploads_tmp');
      $uploadDir = $this->container->getParameter('gb_uploads_directory');

      $tempImg = $tempDir . DIRECTORY_SEPARATOR . $guestbook->getPath();

      $extension = pathinfo($guestbook->getPath(), PATHINFO_EXTENSION);
      $newPath = "gb_" . $guestbook->getId() . "." . $extension;
      $newFullPath = $uploadDir  . DIRECTORY_SEPARATOR . $newPath;

      $imageHandler->open($tempImg)
        ->resize(100, 100)
        ->save($newFullPath);

      $guestbook->setPath($newPath);
      $em->persist($guestbook);
      $em->flush();

      unlink($tempImg);

      $this->addFlash('success', 'Your post has been saved.');
      return $this->redirectToRoute('guestbook_main');
    }

    return $this->render('guestbook/guestbook_form.html.twig', array('form' => $form->createView()));
  }

  /**
   * @Route("/guestbook/{id}", name="guestbook_show")
   */
  public function showAction($id) {
    $repository = $this->getDoctrine()->getRepository('AppBundle:Guestbook');
    $post = $repository->find($id);

    if (!$post) {
      throw $this->createNotFoundException(
        'No product found for id ' . $id
      );
    }

    return $this->render('guestbook/item_details.html.twig', array('post' => $post));
  }

  public function sidebarAction()
  {
    $limitPosts = $this->container->getParameter('latest_posts_limit');
    $repository = $this->getDoctrine()->getRepository('AppBundle:Guestbook');
    $latestPosts = $repository->getLatestPosts($limitPosts);
    $searchForm = $this->createForm(GuestbookSearchType::class);

    return $this->render('guestbook/sidebar.html.twig', array(
      'latestPosts' => $latestPosts,
      'searchForm' => $searchForm->createView()
    ));
  }
}
