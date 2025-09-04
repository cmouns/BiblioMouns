<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BookRepository $bookRepository, CategoryRepository $categoryRepository): Response
    {
        $books = $bookRepository->findAll();
        $categories = $categoryRepository->findAll();
        
        return $this->render('home/index.html.twig', [
            'books' => $books,
            'categories' => $categories, 
        ]);
    }

    #[Route('/{category.slug}/{subcategory.slug}/{id}/{slug} ', name: 'app_home_book_filter', methods: ['GET'])]
    public function filter($id, SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository): Response //ici on recupere l'id et la repo des sous catégories
    
    {   //on recupere la sous catégorie correcpondante à l'id passé en paramètre
        // on accede aux products de cette sous catégorie
        $book = $subCategoryRepository->find($id)->getBooks(); 
        // on recupere la sous catégorie complete(objet)
        $subCategory = $subCategoryRepository->find($id);
        
        return $this->render('home/filter.html.twig', [ 
        'books'=>$book, //liste des produits lies a la sous categorie
        'subCategory'=>$subCategory,// l'objet sous categorie qui corrspond a l'id
        'categories'=>$categoryRepository->findAll(), //la liste de toutes les categories via la repo
        ]);
    }
}
