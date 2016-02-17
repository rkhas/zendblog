<?php
namespace MyBlog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MyBlog\Entity;

class BlogController extends AbstractActionController{
	public function indexAction(){
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		if ($this->isAllowed('controller/MyBlog\Controller\BlogPost:edit')){
			$posts = $objectManager
				->getRepository('\MyBlog\Entity\BlogPost')
				->findBy(array(), array('created' => 'DESC'));
		}else{
			$posts = $objectManager
				->getRepository('\MyBlog\Entity\BlogPost')
				->findBy(array('state'=>1), array('created' => 'DESC'));
		}
		$posts_array = array();
		foreach($posts as $post){
			$posts_array[] = $post->getArrayCopy();
		}
		$view = new ViewModel(array(
			'posts' => $posts_array,
		));
		
		return $view;
		
	}
	
	public function viewAction(){
		//Проверим, существуют id и пост
		$id = (int) $this->params()->fromRoute('id', 0);
		if(!$id){
			$this->flashMessenger()->addErrorMessage('Id поста не найден((');
			return $this->redirect()->toRoute('blog');
		}
		
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		$post = $objectManager
			->getRepository('\MyBlog\Entity\BlogPost')
			->findOneBy(array('id' => $id));
			
		if(!$post){
			$this->flashMessenger()->addErrorMessage(sprintf('Постик не существует'));
			return $this->redirect()->toRoute('blog');
		}
		
		//рендерим шаблон
		$view = new ViewModel(array(
			'post' => $post->getArrayCopy(),
		));
		
		return $view;		
		
		
	}
	
	public function addAction(){
		$form = new \MyBlog\Form\BlogPostForm();
		$form->get('submit')->setValue('Add');
		
		$request = $this->getRequest();
		if($request->isPost){
			$form->setData($request->getPost());
			
			if($form->isValid()){
				$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
				
				$blogpost = new \MyBlog\Entity\BlogPost();
				
				$blogpost->exchangeArray($form->getData());
				
				$blogpost->setCreated(time());
				$blogpost->setUserId(0);
				
				$objectManager->persist($blogpost);
				$objectManager->flush();
				
				$message = 'Пост добавлен';
				$this->flashMessenger()->addMessage($message);
				
				//Редирект к списку постов
				return $this->redirect()->toRoute('blog');
			}else{
				$message = "Ошибка при сохранении";
				$this->flashMessenger()->addErrorMessage($message);
			}
		}
		return array('form' => $form);
		
	}
	
	public function editAction(){
		$id = (int) $this->params()->fromRoute('id', 0);
		if(!$id){
			$this->flashMessenger()->addErrorMessage('Пост не найден');
			return $this->redirect()->toRoute('blog');
		}
		
		//Создаем форму
		$form-> new \MyBlog\Form\BlogPostForm();
		$form->get('submit')->setValue('Сохранить');
		
		$request = $this->getRequest();
		if(!request->isPost()){
			$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
			
			$post = $objectManager
				->getRepository('\MyBlog\Entity\BlogPost')
				->findOneBy(array('id' => $id));
				
			if(!$post){
				$this->flashMessenger()->addErrorMessage(sprintf('Пост с id %s не существует', $id));
				return $this->redirect()->toRoute('blog');
			}
			
			//заполняем форму'
			$form->bind($post);
			return array('form' => $form, 'id' => $id, 'post' => $post);
			
		}else{
			$form->setData($request->getPost());
			
			if($form->isValid()){
				$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
				
				$data = $form->getData();
				$id = $data['id'];
				try{
					$blogpost = $objectManager->find('\MyBlog\Entity\BlogPost', $id);
				}
				catch(\Exeption $ex){
					return $this->redirect()->toRoute('blog', array(
						'action' => 'index'
					));
				}
				
				$blogpost->exchangeArray($form->getData());
				
				$objectManager->persist($blogpost);
				$objectManager->flush();
				
				$message = 'Пост успешно сохранен';
				return $this->flash->redirect()->toRoute('blog');
								
			}else{
				$message = 'Ошибка при сохранении';
				$this->flashMessenger()->addErrorMessage($message);
				return array('form' => $form, 'id' => $id);
			}
		}
		
		public function deleteAction(){
			$id = (int) $this->params()->fromRoute('id', 0);
			if (!$id) {
				$this->flashMessenger()->addErrorMessage('Blogpost id doesn\'t set');
				return $this->redirect()->toRoute('blog');
			}
			$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
			$request = $this->getRequest();
			if($request->isPost()){
				$del = $request->getPost('del', 'No');
				if ($del == 'Yes') {
					$id = (int) $request->getPost('id');
					try {
						$blogpost = $objectManager->find('MyBlog\Entity\BlogPost', $id);
						$objectManager->remove($blogpost);
						$objectManager->flush();
					}
					catch (\Exception $ex) {
						$this->flashMessenger()->addErrorMessage('Ошибка ');
						return $this->redirect()->toRoute('blog', array(
							'action' => 'index'
						));
					}
					$this->flashMessenger()->addMessage(sprintf('Blogpost %d was succesfully deleted', $id));
				}
				return $this->redirect()->toRoute('blog');
			}
			return array(
				'id'    => $id,
				'post' => $objectManager->find('MyBlog\Entity\BlogPost', $id)->getArrayCopy(),
			);
		}
		
	}
	
	
	
}












