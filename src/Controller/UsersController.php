<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Users Controller
 *
 */
class UsersController extends AppController
{
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['add']);
    }

    public function isAuthorized($user)
    {
        if(isset($user['role']) and $user['role'] === 'user')
        {
            // El usuario tiene acceso a estas opciones
            if(in_array($this->request->action, ['home', 'view', 'logout']))
            {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }

    public function login()
    {
        if($this->request->is('post'))
        {
            $user = $this->Auth->identify();
            if($user)
            {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            else
            {
                $this->Flash->error('Datos son invalidos, por favor intente nuevamente', ['key' => 'auth']);
            }
        }

        // Evitar que una vez autienticado, vuelva a la pantalla de login
        if ($this->Auth->user())
        {
            return $this->redirect(['controller' => 'Users', 'action' => 'home']);
        }
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    // Aquí es donde ingresa el usuario lugo de loguearse al Sistema
    public function home()
    {
        $this->render();
    }

	public function index()
    {
        $users = $this->paginate($this->Users);
        $this->set('users', $users);
    }

    public function view($id)
    {
        $user = $this->Users->get($id);
        $this->set('user', $user);
    }

    public function add()
    {
    	$user = $this->Users->newEntity();

    	if ($this->request->is('post'))
    	{
    		// Muestra lo que envía el formulario en modo debug
    		// debug($this->request->data);

            // Se define por defecto rol de "usuario" y "activo"
            $user->role = 'user';
            $user->active = 1;

    		// Coloca los datos en el método patchEntity para validarlos
    		$user = $this->Users->patchEntity($user, $this->request->data);

    		// Guarda los datos en la BD
    		if($this->Users->save($user))
    		{
    			$this->Flash->success('El usuario ha sido creado correctamente.');
    			return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    		}
    		else
    		{
    			$this->Flash->error('El usuario no pudo ser creado. Por favor, intente nuevamente');
    		}
    	}

    	$this->set(compact('user'));
    }

    public function edit($id = null)
    {
        $user = $this->Users->get($id);

        if ($this->request->is(['patch', 'post', 'put']))
        {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user))
            {
                $this->Flash->success('El usuario ha sido modificado');
                return $this->redirect(['action' => 'index']);
            }
            else
            {
                $this->Flash->error('El usuario no pudo ser modificado. Por favor, intente nuevamente.');
            }
        }

        $this->set(compact('user'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);

        if ($this->Users->delete($user))
        {
            $this->Flash->success('El usuario ha sido eliminado.');
        }
        else
        {
            $this->Flash->error('El usuario no pudo ser eliminado. Por favor, intente nuevamente.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
