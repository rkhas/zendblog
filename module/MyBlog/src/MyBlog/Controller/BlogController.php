<?php
namespace MyBlog\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MyBlog\Entity;
class BlogController extends AbstractActionController
{
    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $posts = $objectManager
            ->getRepository('\MyBlog\Entity\BlogPost')
            ->findBy(array('state' => 1), array('created' => 'DESC'));
        $posts_array = array();
        foreach ($posts as $post) {
            $posts_array[] = $post->getArrayCopy();
        }
        $view = new ViewModel(array(
            'posts' => $posts_array,
        ));
        return $view;
    }
    public function viewAction()
    {

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Пост не найден');
            return $this->redirect()->toRoute('blog');
        }
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $post = $objectManager
            ->getRepository('\MyBlog\Entity\BlogPost')
            ->findOneBy(array('id' => $id));
        if (!$post) {
            $this->flashMessenger()->addErrorMessage(sprintf('Пост с id %s не найден', $id));
            return $this->redirect()->toRoute('blog');
        }

        $view = new ViewModel(array(
            'post' => $post->getArrayCopy(),
        ));
        return $view;
    }
    public function addAction()
    {
        $form = new \MyBlog\Form\BlogPostForm();
        $form->get('submit')->setValue('Сохранить');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $blogpost = new \MyBlog\Entity\BlogPost();
                $blogpost->exchangeArray($form->getData());
                $blogpost->setCreated(time());
                $blogpost->setUserId(0);
                $objectManager->persist($blogpost);
                $objectManager->flush();
                $message = 'Пост сохранен!';
                $this->flashMessenger()->addMessage($message);
  
                return $this->redirect()->toRoute('blog');
            }
            else {
                $message = 'Error while saving blogpost';
                $this->flashMessenger()->addErrorMessage($message);
            }
        }
        return array('form' => $form);
    }
    
	public function editAction(){
        $form = new \MyBlog\Form\BlogPostForm();
        $form->get('submit')->setValue('Сохранить');
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $id = (int) $this->params()->fromRoute('id', 0);
            if (!$id) {
                $this->flashMessenger()->addErrorMessage('Пост не существует');
                return $this->redirect()->toRoute('blog');
            }
            $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $post = $objectManager
                ->getRepository('\MyBlog\Entity\BlogPost')
                ->findOneBy(array('id' => $id));
            if (!$post) {
                $this->flashMessenger()->addErrorMessage(sprintf('Пост с id %s не существует', $id));
                return $this->redirect()->toRoute('blog');
            }
            $form->bind($post);
            return array('form' => $form);
        }
        else {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $data = $form->getData();
                $id = $data['id'];
                try {
                    $blogpost = $objectManager->find('\MyBlog\Entity\BlogPost', $id);
                }
                catch (\Exception $ex) {
                    return $this->redirect()->toRoute('blog', array(
                        'action' => 'index'
                    ));
                }
                $blogpost->exchangeArray($form->getData());
                $objectManager->persist($blogpost);
                $objectManager->flush();
                $message = 'Пост сохранен!';
                $this->flashMessenger()->addMessage($message);
                
                return $this->redirect()->toRoute('blog');
            }
            else {
                $message = 'Ошибка при сохранении';
                $this->flashMessenger()->addErrorMessage($message);
            }
        }
    }
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Пост не найден');
            return $this->redirect()->toRoute('blog');
        }
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'Нет');
            if ($del == 'Да') {
                $id = (int) $request->getPost('id');
                try {
                    $blogpost = $objectManager->find('MyBlog\Entity\BlogPost', $id);
                    $objectManager->remove($blogpost);
                    $objectManager->flush();
                }
                catch (\Exception $ex) {
                    $this->flashMessenger()->addErrorMessage('Ошибка удаления поста');
                    return $this->redirect()->toRoute('blog', array(
                        'action' => 'index'
                    ));
                }
                $this->flashMessenger()->addMessage(sprintf('Пост %d удален', $id));
            }
            return $this->redirect()->toRoute('blog');
        }
        return array(
            'id'    => $id,
            'post' => $objectManager->find('MyBlog\Entity\BlogPost', $id)->getArrayCopy(),
        );
    }
}