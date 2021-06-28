<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostType;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * @Route("/resultat", name="resultat")
     */
    public function resultat( HttpClientInterface $httpclient, PostsRepository $post)
    {
        // var_dump($_GET['pseudo']);
        // die();
        $pseudo = $_GET['pseudo'];
        // $pseudo = strtolower($pseudo);
        
        $serveur= $_GET['serveur'];
        // $serveur = strtolower($serveur);
        $local= $_GET['local'];
        // $local = strtolower($local);
       
        
        $response = $httpclient->request('GET','https://worldofwarcraft.com/'.$local.'/character/eu/'.$serveur.'/'.urlencode($pseudo).'/reputation.json');
        $response2= $httpclient->request('GET',"https://worldofwarcraft.com/".$local."/character/eu/".$serveur."/".$pseudo."/model.json");
    //    dd($response->toArray()["reputations"]);
    // dd($response2->toArray()["character"]["bust"]['url']);
       $adresse= $response2->toArray()["character"]["bust"]['url'];
       $debut = "https://render-eu.worldofwarcraft.com/character/".$serveur."/";
       $image= str_replace($debut, "", $adresse);
       $extension="-inset.jpg";
       $nomImage = str_replace($extension, "", $image);
       $slash="/";
       $url=str_replace($slash, "-", $nomImage);
       

    //    var_dump($url);
    //    die();
       
 
        return $this->render('accueil/resultat.html.twig',[
            'zones'=>$response->toArray()["reputations"], 
            'persos'=>$response2->toArray()['character'],
            'pseudo' => $pseudo,
            'serveur'=> $serveur, 
            'local' => $local,
            'posts' => $post->findAll(),
            'url'=> $url,
          
        ]);
    }

    /**
     * @Route("/{pseudo}/{serveur}/{local}/{url}/new", name="nouveau-post")
     */
    public function nouveauPost($pseudo, $serveur, $local, Request $request, $url):Response
    {
        $pseudo = $pseudo;
        $url = $url;
        $nouvelleUrl = str_replace("-", "/", $url); 
        $serveur = $serveur;
        $local = $local;
        $post = new Posts;
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        // $pseudoconnu = $post1->getPseudo();
     
        // $serveurconnu = $post1->getRoyaume();

        if($form->isSubmitted() && $form->isValid() ){
            //&& ($pseudo != $pseudoconnu && $serveur != $serveurconnu)
            $post->setUsers($this->getUser());
            $post->setPseudo($pseudo);
            $post->setRoyaume($serveur);
            $post->setAvatar($nouvelleUrl);
            $post->setLocalite($local);
            $em=$this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('accueil');
        }
       
        

       
        return $this->render('post/index.html.twig',[
            'form'=> $form->createView(),
           "pseudo" => $pseudo,
           "serveur" => $serveur,
           "local" => $local,
           "url"=> $nouvelleUrl,
        //    'pseudoconnu'=>$pseudoconnu,
        //    'serveurconnu'=>$serveurconnu
        ]);
    }
    /**
     * @Route("personnages/{pseudo}/{serveur}/{local}/{url}/modifier/{id}", name="modifier-post")
     */
    public function modifPost($id, $pseudo, $serveur, $local, Request $request, $url, Posts $classpost):Response
    {
        $id = $id;
        $pseudo = $pseudo;
        $url = $url;
        $nouvelleUrl = str_replace("-", "/", $url); 
        $serveur = $serveur;
        $local = $local;
        $form = $this->createForm(PostType::class, $classpost);
        $form->handleRequest($request);
        // $pseudoconnu = $post1->getPseudo();
     
        // $serveurconnu = $post1->getRoyaume();

        if($form->isSubmitted() && $form->isValid() ){
            //&& ($pseudo != $pseudoconnu && $serveur != $serveurconnu)

            $em=$this->getDoctrine()->getManager();
            $em->persist($classpost);
            $em->flush();
            return $this->redirectToRoute('accueil');
        }
      
        

       
        return $this->render('post/index.html.twig',[
            'post'=> $classpost,
            'form'=> $form->createView(),
           "pseudo" => $pseudo,
           "serveur" => $serveur,
           "local" => $local,
           "url"=> $nouvelleUrl,
        //    'pseudoconnu'=>$pseudoconnu,
        //    'serveurconnu'=>$serveurconnu
        ]);
    }
    

    /**
     * @Route("personnages/personnage/bidule/{id}", name="post")
     */
    public function post($id, PostsRepository $postsRepository, HttpClientInterface $httpclient, Posts $post1):Response
    {
        $idUtilisateur = $post1->getUsers();
        $post = $postsRepository->findOneBy(['id'=> $id]);
        // $pseudo = $postsRepository->findOneBy(['pseudo'=> $pseudo]);
        $pseudo = $post1->getPseudo();
        $local = $post1->getLocalite();
        $serveur = $post1->getRoyaume();
        $url = $post1->getAvatar();
        $nouvelleUrl = str_replace("/", "-", $url); 

        
       

        $response = $httpclient->request('GET','https://worldofwarcraft.com/'.$local.'/character/eu/'.$serveur.'/'.$pseudo.'/reputation.json');
        $response2= $httpclient->request('GET',"https://worldofwarcraft.com/".$local."/character/eu/".$serveur."/".$pseudo."/model.json");

        if(!$post){
            throw new NotFoundHttpException("Pas de données sur le personnages");
        }

      

        // return $this->redirectToRoute('admin');
        

       
        return $this->render('post/vue.html.twig',[
            'post'=>$post,
            'zones'=>$response->toArray()["reputations"], 
            'persos'=>$response2->toArray()['character'],
            'pseudo' => $pseudo,
            'serveur' => $serveur,
            'local' => $local,
            'url' => $nouvelleUrl,
            'utilisateur' =>$idUtilisateur,
           

        
           
            
        ]);
    }
   /**
    * @Route("/supprimer/post{id}", name="supprimer_post")
    */
    public function supprimerArticle(Posts $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        $this->addFlash('message', 'Le personnage à bien été supprimé');
        return $this->redirectToRoute('accueil');
        
    }


    /**
     * @Route("/personnages", name="personnages")
     */
    public function personnages()
    {
      
       
        return $this->render('user/index.html.twig',[
           
            
        ]);
    }
}
