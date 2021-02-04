<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\Type\TodoType;
use App\Repository\TodoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends Controller
{
    /**
     * @var TodoRepository
     */
    private $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    /**
     * @Route("/todo", name="todo")
     * @Template("todo.html.twig")
     */
    public function index()
    {
        $todoList = $this->todoRepository->findBy([], ['id' => 'desc']);

        return [
            'todoList' => $todoList
        ];
    }

    /**
     * @Route("/todo/create", name="todo_create")
     * @Template("create.html.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Request $request)
    {
        $form = $this->createForm(TodoType::class, new Todo());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($todo);
            $entityManager->flush();

            $this->addFlash('success', '登録しました。');

            return $this->redirectToRoute('todo_create');
        }

        return [
            'form' => $form->createView()
        ];
    }
}
