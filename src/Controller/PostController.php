<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package App\Controller
 * @Route("post", name="post.")
 */
class PostController extends AbstractController
{
    private PostRepository $postRepository;
    /**
     * PostController constructor.
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index()
    {
        $posts = $this->postRepository->findAll();
        $form = $this->createForm(PostType::class, new Post(), [
            'action' => $this->generateUrl('post.create'),
            'method' => 'POST'
        ]);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request) {
        $post = new Post();
        $post->setTitle($request->request->get('post')['title']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($post);
        $em->flush();

        $this->addFlash('success', 'Post created successfully!');

        return $this->redirect($this->generateUrl("post.index"));
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/show/{id?}", name="show")
     */
    public function show(int $id) {
        $post = $this->postRepository->find($id);

        return $this->render("post/show.html.twig", [
            'post' => $post
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param int $id
     * @return RedirectResponse
     */
    public function remove(int $id) {
        $post = $this->postRepository->find($id);
        $em = $this->getDoctrine()->getManager();

        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Post was removed.');

        return $this->redirect($this->generateUrl("post.index"));
    }
}
