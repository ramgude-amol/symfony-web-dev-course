<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{ 
    /**
     * @Route("/", name="read")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tasks = $entityManager->getRepository(Task::class)->findBy([], ['id'=>'DESC']);
        
        return $this->render('index.html.twig',[
            'tasks'=>$tasks
        ]);
    }

    /**
     * @Route("/create", name="create_task")
     */
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $title = $request->request->get('title');
        if(empty($title))
        {
            return $this->redirectToRoute('read');
        }

        $task = new Task;
        $task->setTitle($title);
        $entityManager->persist($task);
        $entityManager->flush($task);

        return $this->redirectToRoute('read');
    }

    /**
     * @Route("/switch/{id}", name="switch_task")
     */
    public function switchTask($id, EntityManagerInterface $entityManager): Response
    {
        $task = $entityManager->getRepository(Task::class)->find($id);
        $task->setStatus(!$task->getStatus());
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('read');
    }

    /**
     * @Route("/delete/{id}", name="delete_task")
     * 
     */
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $task = $entityManager->getRepository(Task::class)->find($id);
        $entityManager->remove($task);
        $entityManager->flush();

        return $this->redirectToRoute('read');
    }
}
