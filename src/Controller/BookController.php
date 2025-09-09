<?php

namespace App\Controller;
use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
 



final class BookController extends AbstractController
{
    #[Route('/editor/book', name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/editor/book/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


             $image = $form->get('image')->getData();
            if ($image) {
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName = $slugger->slug($originalName);
                $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move($this->getParameter('image_directory'), $newFileImageName);
                } catch (FileException $exception) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image');
                }

                $book->setImage($newFileImageName);
            }
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/editor/book/show/{id}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/editor/book/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Book $book, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $image = $form->get('image')->getData();

        if ($image) {
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeImageName = $slugger->slug($originalName);
            $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();

            try {
                $image->move($this->getParameter('image_directory'), $newFileImageName);
            } catch (FileException $exception) {
                $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image');
            }

            $book->setImage($newFileImageName);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
}


    #[Route('/editor/book/{id}/delete', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/book/{id}/favorite', name: 'app_add_book_favorite')]
    public function toggleFavorite(?Book $book, EntityManagerInterface $em): Response
    {
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $user = $this->getUser();

        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException();
        }

        if ($user->getFavorites()->contains($book)) {
            $user->removeFavorite($book);
        } else {
            $user->addFavorite($book);
        }

        $em->flush();
         $this->addFlash('success', 'Livre ajouté aux favoris avec succès.');


        return $this->redirectToRoute('app_home');
    }

     #[Route('/show/favorites', name: 'app_show_book_favorite')]
    public function showFavorite(BookRepository $book): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException();
        }

        $favorites = $user->getFavorites();

        if ($favorites->count() === 0) {
            $this->addFlash('info', 'Veuillez ajouter des favoris');
        }

        return $this->render('book/favorites.html.twig', [
            'favorites' => $favorites,
            'books' => $book,
        ]);
    }
    


}
