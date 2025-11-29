<?php
namespace MapasCulturais\Controllers;

use MapasCulturais\App;

class EventFavorite extends EntityController {

    function GET_create() {
        App::i()->pass();
    }

    function GET_edit() {
        App::i()->pass();
    }

    function GET_single() {
        App::i()->pass();
    }

    function GET_index() {
        $this->requireAuthentication();
        
        $app = App::i();
        $user = $app->user;
        
        $favorites = $user->profile->getFavoriteEvents();
        
        $this->apiResponse($favorites);
    }

    /**
     * Adiciona um evento aos favoritos
     * POST /eventfavorite/toggle
     * Espera: { "eventId": 123 }
     */
    function POST_toggle() {
        $this->requireAuthentication();
        
        $app = App::i();
        $user = $app->user;
        
        $data = $this->postData;
        
        if (!isset($data['eventId'])) {
            $this->errorJson('eventId é obrigatório', 400);
            return;
        }
        
        $event = $app->repo('Event')->find($data['eventId']);
        
        if (!$event) {
            $this->errorJson('Evento não encontrado', 404);
            return;
        }
        
        // Verifica se já existe o favorito
        $favorite = $app->repo('EventFavorite')->findOneBy([
            'agent' => $user->profile,
            'event' => $event
        ]);
        
        if ($favorite) {
            // Remove o favorito
            $favorite->delete(true);
            $action = 'removed';
        } else {
            // Adiciona o favorito
            $favorite = new \MapasCulturais\Entities\EventFavorite;
            $favorite->agent = $user->profile;
            $favorite->event = $event;
            $favorite->save(true);
            $action = 'added';
        }
        
        $this->json([
            'success' => true,
            'action' => $action,
            'isFavorited' => $action === 'added',
            'favoritesCount' => $event->getFavoritesCount()
        ]);
    }

    /**
     * Remove um evento dos favoritos
     * DELETE /eventfavorite/remove
     * Espera: { "eventId": 123 }
     */
    function DELETE_remove() {
        $this->requireAuthentication();
        
        $app = App::i();
        $user = $app->user;
        
        $data = $this->postData;
        
        if (!isset($data['eventId'])) {
            $this->errorJson('eventId é obrigatório', 400);
            return;
        }
        
        $event = $app->repo('Event')->find($data['eventId']);
        
        if (!$event) {
            $this->errorJson('Evento não encontrado', 404);
            return;
        }
        
        $favorite = $app->repo('EventFavorite')->findOneBy([
            'agent' => $user->profile,
            'event' => $event
        ]);
        
        if ($favorite) {
            $favorite->delete(true);
        }
        
        $this->json([
            'success' => true,
            'favoritesCount' => $event->getFavoritesCount()
        ]);
    }
}
