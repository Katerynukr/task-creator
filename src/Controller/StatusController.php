<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Status;

class StatusController extends AbstractController
{
    /**
     * @Route("/status", name="status_index", methods= {"GET"})
     */
    public function index(Request $r): Response
    {

        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $statuses = $this->getDoctrine()
        ->getRepository(Status::class);
        
        if($r->query->get('sort_by') == 'sort_by_title_asc'){
            $statuses = $statuses->findBy([],['title' => 'asc']);
        }elseif($r->query->get('sort_by') == 'sort_by_title_desc'){
            $statuses = $statuses->findBy([],['title' => 'desc']);
        }else{
            $statuses = $statuses->findAll();
        }
        
        return $this->render('status/index.html.twig', [
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'statuses' => $statuses,
            'sortBy' => $r->query->get('sort_by') ?? 'default',
            'success' => $r->getSession()->getFlashBag()->get('success', []),
        ]);
    }

    /**
     * @Route("/status/create", name="status_create", methods= {"GET"})
     */
    public function create(Request $r): Response
    {
        $status_title = $r->getSession()->getFlashBag()->get('status_title', []);

        return $this->render('status/create.html.twig', [
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'status_title' => $status_title[0] ?? '',
            'success' => $r->getSession()->getFlashBag()->get('success', [])
        ]);
    }

    /**
     * @Route("/status/store", name="status_store", methods= {"POST"})
    */
    public function store(Request $r, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');

        if (!$this->isCsrfTokenValid('check_csrf_hidden', $submittedToken)) {
            $r->getSession()->getFlashBag()->add('errors', 'Bad talken CSRF');
            return $this->redirectToRoute('status_create');
        }

        $status = new Status;
        $status->
        setTitle($r->request->get('status_title'));

        $errors = $validator->validate($status);

        // dd(count($errors));
        if (count($errors) > 0){
            foreach($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('status_title', $r->request->get('status_title'));
            return $this->redirectToRoute('status_create');
        }

        //creating entity manager sending data to database
        $entityManager = $this->getDoctrine()->getManager();
        //organizing data to be send
        $entityManager->persist($status);
        //wrting
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Status was successfully created');

        return $this->redirectToRoute('status_index');
    }

    /**
     * @Route("/status/edit/{id}", name="status_edit", methods= {"GET"})
    */
    public function edit(Request $r, int $id): Response
    {
        $status = $this->getDoctrine()
        ->getRepository(Status::class)
        ->find($id);
        
        return $this->render('status/edit.html.twig', [
            'status' => $status,
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'success' => $r->getSession()->getFlashBag()->get('success', [])
        ]);
    }

    /**
     * @Route("/status/update/{id}", name="status_update", methods= {"POST"})
    */
    public function update(Request $r, int $id, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');

        $status = $this->getDoctrine()
        ->getRepository(Status::class)
        ->find($id);

        if (!$this->isCsrfTokenValid('check_csrf_hidden', $submittedToken)) {
            $r->getSession()->getFlashBag()->add('errors', 'Bad talken CSRF');
            return $this->redirectToRoute('status_edit', ['id'=>$status->getId()]);
        }

        $status->
        setTitle($r->request->get('status_title'));
        

        $errors = $validator->validate($status);

        // dd(count($errors));
        if (count($errors) > 0){
            foreach($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('status_title', $r->request->get('status_title'));
            return $this->redirectToRoute('status_edit', ['id'=>$status->getId()]);
        }

        //creating entity manager sending data to database
        $entityManager = $this->getDoctrine()->getManager();
        //organizing data to be send
        $entityManager->persist($status);
        //wrting
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Status was successfully edited');

        return $this->redirectToRoute('status_index');
    }

    /**
     * @Route("/status/delete/{id}", name="status_delete", methods= {"POST"})
    */
    public function delete(Request $r, int $id): Response
    {
        $submittedToken = $r->request->get('token');

        if (!$this->isCsrfTokenValid('check_csrf_hidden', $submittedToken)) {
            $r->getSession()->getFlashBag()->add('errors', 'Bad talken CSRF');
            return $this->redirectToRoute('status_index');
        }

        $status = $this->getDoctrine()
        ->getRepository(Status::class)
        ->find($id);

        if ($status->getNotes()->count() > 0) {
            $r->getSession()->getFlashBag()->add('errors', 'You cannot deleate the status because it has notes' );
            return $this->redirectToRoute('status_index');
        }

        //creating entity manager sending data to database
        $entityManager = $this->getDoctrine()->getManager();
        //organizing data to be send
        $entityManager->remove($status);
        //wrting
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Status was successfully deleted');

        return $this->redirectToRoute('status_index');
    }
}
