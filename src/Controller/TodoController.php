<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\Type\TodoType;
use App\Repository\TodoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("/todo/{id}/edit", name="todo_edit", requirements={"id" = "\d+"})
     * @Template("create.html.twig")
     * @param Request $request
     * @param int|null $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Request $request, $id = null)
    {
        if ($id === null) {
            $todo = new Todo();
        } else {
            $todo = $this->todoRepository->find($id);
            if ($todo === null) {
                throw new NotFoundHttpException();
            }
        }
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($todo);
            $entityManager->flush();

            $operation = $id ? '更新' : '登録';
            $this->addFlash('success', $operation . 'しました。');

            return $this->redirectToRoute('todo_edit', ['id' => $todo->getId()]);
        }

        return [
            'form' => $form->createView(),
            'todo' => $todo,
        ];
    }

    /**
     * @Route("/todo/{id}/remove", name="todo_remove", requirements={"id" = "\d"}, methods={"POST"}) # ※1
     */
    public function remove(Request $request, $id)
    {
        $token = $request->headers->get('x-csrf-token'); # ※2
        if (!$this->isCsrfTokenValid('x-csrf-token', $token)) {
            throw new BadRequestHttpException();
        }

        $todo = $this->todoRepository->find($id);
        if ($todo == null) {
            throw new NotFoundHttpException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($todo);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }
}
