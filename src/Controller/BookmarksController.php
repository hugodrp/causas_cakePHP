<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Bookmarks Controller
 *
 *
 * @method \App\Model\Entity\Bookmark[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BookmarksController extends AppController
{
    public function isAuthorized($user)
    {
        if(isset($user['role']) and $user['role'] === 'user')
        {
            if(in_array($this->request->action, ['add', 'index']))
            {
                return true;
            }

            if (in_array($this->request->action, ['edit', 'delete']))
            {
                // Recupera el identificador del enlace favorito
                $id = $this->request->params['pass'][0];
                $bookmark = $this->Bookmarks->get($id);

                // Controla que el enlace del usuario dueño
                // del favorito sea igual al del usuario autenticado
                if ($bookmark->user_id == $user['id'])
                {
                    return true;
                }
            }
        }

        return parent::isAuthorized($user);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            // Recupera los enlaces correspondientes al usuario logueado
            'conditions' => ['user_id' => $this->Auth->user('id')],
            'order' => ['id' => 'desc']
        ];
        $this->set('bookmarks', $this->paginate($this->Bookmarks));
    }

    /**
     * View method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => []
        ]);

        $this->set('bookmark', $bookmark);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $bookmark = $this->Bookmarks->newEntity();
        if ($this->request->is('post')) 
        {
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->getData());

            // Guarda el enlace con usuario que lo ha creado
            $bookmark->user_id = $this->Auth->user('id');
            if ($this->Bookmarks->save($bookmark)) 
            {
                $this->Flash->success('El enlace ha sido creado.');
                return $this->redirect(['action' => 'index']);
            }
            else 
            {
                $this->Flash->error('El enlace no pudo ser creado. Por favor, intente nuevamente.');
            }
            
        }
        $this->set(compact('bookmark'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $bookmark = $this->Bookmarks->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) 
        {
            // Prepara el objeto para editar el enlace
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->getData());

            // Usuario identificado
            $bookmark->user_id = $this->Auth->user('id');
            if ($this->Bookmarks->save($bookmark)) 
            {
                $this->Flash->success('El enlace ha sido actualizado.');;
                return $this->redirect(['action' => 'index']);
            }            
            else
            {
                $this->Flash->error('El enlace no pudo ser actualizado. Por favor, intente nuevamente');
            }
        }
        $this->set(compact('bookmark'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Bookmark id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $bookmark = $this->Bookmarks->get($id);
        if ($this->Bookmarks->delete($bookmark)) 
        {
            $this->Flash->success('El enlace ha sido eliminado');
        } 
        else 
        {
            $this->Flash->error('El enlace no pudo ser eliminado. Por favor, intente nuevamente.');
        }

        return $this->redirect(['action' => 'index']);
    }
}
