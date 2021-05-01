<?php
namespace App\Service;

use App\Entity\Like;
use App\Repository\LikeRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class SidebarGenerator
{
    private $sidebar;
    private $security;
    private $twig;
    private $likeRepo;

    public function __construct(Security $security, Environment $twig, LikeRepository $likeRepo)
    {
        $this->security = $security;
        $this->likeRepo = $likeRepo;
        $this->twig = $twig;
    }

    public function generate(): string
    {
        $likes = $this->likeRepo->findAllByUser($this->security->getUser());
        $likesArray = [];
        $maxNbr = 5;
        $i = 0;
        foreach($likes as $like){
            $i++;
            if($i > $maxNbr){break;}
            $card = $like->getCard();
            $media = '';
            if($card->getType() == 'video' || $card->getType() == 'musique'){
                $media = '
                <div class="video-responsive">
                    <iframe src="https://www.youtube.com/embed/' . substr($card->getContent(), -11) . '"  allow="fullscreen;"></iframe>
                </div>
                ';
            }else{
                $media = '
                <img src="' . $card->getContent() . '" alt="'.$card->getTitle().'">
                ';
            };

            $html = $this->twig->render('sidebar/card.html.twig', [
                'id' => $card->getId(),
                'title' => $card->getTitle(),
                'content' => $card->getAnswer(),
                'type' => $card->getType(),
                'media' => $media,
            ]);

            $this->sidebar.= $html;

//            array_push($likesArray, $html);
        }
//        return new Response(json_encode($likesArray));
        return $this->sidebar;

    }
}