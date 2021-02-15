<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Note;
use App\Entity\Status;

class NotesController extends AbstractController
{
    /**
     * @Route("/note", name="note_index", methods={"GET"})
     */
    public function index(Request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $statuses =  $this->getDoctrine()
        ->getRepository(Status::class)
        ->findAll();
        
        $notes = $this->getDoctrine()
        ->getRepository(Note::class);

       
        if($r->query->get('filter') == 0 ){
            $notes= $notes->findAll();
            
        }else{
            $notes = $notes->findBy(['status_id'=> $r->query->get('filter')], ['title' => 'asc']);
        }

        return $this->render('notes/index.html.twig', [
            'notes'=>$notes,
            'statuses' => $statuses,
            'statusID'=>$r->query->get('filter') ?? 0,
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'success' => $r->getSession()->getFlashBag()->get('success', [])
        ]);
    }

    /**
     * @Route("/note/create", name="note_create", methods={"GET"})
     */
    public function create(Request $r): Response
    {

        $statuses =  $this->getDoctrine()
        ->getRepository(Status::class)
        ->findAll();

        if(empty($statuses)){
            $r->getSession()->getFlashBag()->add('errors', 'You cant create a note because there are not stutauses');
            $r->getSession()->getFlashBag()->add('errors', 'Create a new status');
            return $this->redirectToRoute('status_create');
        }
        
        $note_title = $r->getSession()->getFlashBag()->get('note_title', []);
        $note_priority = $r->getSession()->getFlashBag()->get('note_priority', []);
        $note_note = $r->getSession()->getFlashBag()->get('note_note', []);
        $note_status = $r->getSession()->getFlashBag()->get('note_status_id');
       
        
        return $this->render('notes/create.html.twig', [
            'note_title' => $note_title[0] ?? '',
            'note_priority' => $note_priority[0] ?? '',
            'note_note' => $note_note[0] ?? '',
            'note_status' => $note_status[0] ?? '',
            'statuses' => $statuses,
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'success' => $r->getSession()->getFlashBag()->get('success', []),
        ]);
    }

     /**
     * @Route("/note/create", name="note_store", methods={"POST"})
     */
    public function store(Request $r, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');

        if (!$this->isCsrfTokenValid('check_csrf_hidden', $submittedToken)) {
            $r->getSession()->getFlashBag()->add('errors', 'Bad talken CSRF');
            return $this->redirectToRoute('note_create');
        }

        $status =  $this->getDoctrine()
        ->getRepository(Status::class)
        ->find($r->request->get('note_status_id'));

        if($status == null){
            $r->getSession()->getFlashBag()->add('errors', 'Choose status from the list');
        }

        $note = new Note;

        $note->
        setTitle($r->request->get('note_title'))->
        setPriority($r->request->get('note_priority'))->
        setNote($r->request->get('note_note'))->
        setStatus($status);

        $errors = $validator->validate($note);
        if (count($errors) > 0){
            foreach($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('note_title', $r->request->get('note_title'));
            $r->getSession()->getFlashBag()->add('note_priority', $r->request->get('note_priority'));
            $r->getSession()->getFlashBag()->add('note_note', $r->request->get('note_note'));
            $r->getSession()->getFlashBag()->add('note_status_id', $r->request->get('note_status_id'));
            return $this->redirectToRoute('note_create');
        }
        if(null === $status) {
            return $this->redirectToRoute('note_create');
        }


        //creating entity manager sending data to database
        $entityManager = $this->getDoctrine()->getManager();
        //organizing data to be send
        $entityManager->persist($note);
        //wrting
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Note was successfully created');

        return $this->redirectToRoute('note_index');
    }

    /**
     * @Route("/note/edit/{id}", name="note_edit", methods= {"GET"})
     */
    public function edit(Request $r, int $id): Response
    {
        $note= $this->getDoctrine()
        ->getRepository(Note::class)
        ->find($id);

        $statuses = $this->getDoctrine()
        ->getRepository(Status::class)
        ->findAll();
        
        return $this->render('notes/edit.html.twig', [
            'note' => $note,
            'statuses' => $statuses,
            'errors' => $r->getSession()->getFlashBag()->get('errors', []),
            'success' => $r->getSession()->getFlashBag()->get('success', []),
        ]);
    }

    /**
     * @Route("/note/update/{id}", name="note_update", methods= {"POST"})
     */
    public function update(Request $r, int $id, ValidatorInterface $validator): Response
    {
        $submittedToken = $r->request->get('token');


        $note= $this->getDoctrine()
        ->getRepository(Note::class)
        ->find($id);

        if (!$this->isCsrfTokenValid('check_csrf_hidden', $submittedToken)) {
            $r->getSession()->getFlashBag()->add('errors', 'Bad talken CSRF');
            return $this->redirectToRoute('note_edit' , ['id'=>$note->getId()]);
        }
        
        $status =  $this->getDoctrine()
        ->getRepository(Status::class)
        ->find($r->request->get('note_status_id'));

        //dd($status);
        $note->
        setTitle($r->request->get('note_title'))->
        setPriority($r->request->get('note_priority'))->
        setNote($r->request->get('note_note'))->
        setStatus($status);

        $errors = $validator->validate($note);
        if (count($errors) > 0){
            foreach($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            return $this->redirectToRoute('note_edit', ['id'=>$note->getId()] );
        }

        //creating entity manager sending data to database
        $entityManager = $this->getDoctrine()->getManager();
        //organizing data to be send
        $entityManager->persist($note);
        //wrting
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Note was successfully edited');

        return $this->redirectToRoute('note_index');
    }

    /**
     * @Route("/note/delete/{id}", name="note_delete", methods= {"POST"})
     */
    public function delete(Request $r, int $id): Response
    {
        $submittedToken = $r->request->get('token');

        if (!$this->isCsrfTokenValid('check_csrf_hidden', $submittedToken)) {
            $r->getSession()->getFlashBag()->add('errors', 'Bad talken CSRF');
            return $this->redirectToRoute('note_index');
        }

        $note= $this->getDoctrine()
        ->getRepository(Note::class)
        ->find($id);

        //creating entity manager sending data to database
        $entityManager = $this->getDoctrine()->getManager();
        //organizing data to be send
        $entityManager->remove($note);
        //wrting
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', 'Note was successfully deleted');

        return $this->redirectToRoute('note_index');
    }
}
