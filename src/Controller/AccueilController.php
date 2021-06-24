<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
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
    public function resultat( HttpClientInterface $httpclient)
    {
        $pseudo = $_GET['pseudo'];
        $pseudo = strtolower($pseudo);
        
        $serveur= $_GET['serveur'];
        $serveur = strtolower($serveur);
        $local= $_GET['local'];
        $local = strtolower($local);

        $response = $httpclient->request('GET','https://worldofwarcraft.com/'.$local.'/character/eu/'.$serveur.'/'.urlencode($pseudo).'/reputation.json');
        $response2= $httpclient->request('GET',"https://worldofwarcraft.com/".$local."/character/eu/".$serveur."/".$pseudo."/model.json");
        // dd($response2->toArray()["character"]);

 
        return $this->render('accueil/resultat.html.twig',[
            'zones'=>$response->toArray()["reputations"], 
            'persos'=>$response2->toArray()['character'],
            'pseudo' => $pseudo,
            'serveur'=> $serveur, 
        ]);
    }

}
