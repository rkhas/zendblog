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

		$view = new ViewModel(array(
			'posts' => $posts,
		));

		return $view;
    }
    public function addAction()
    {
        $form = new \MyBlog\Form\BlogPostForm();
        $form->get('submit')->setValue('Добавить');
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
                $message = 'Пост успешно сохранен!';
                $this->flashMessenger()->addMessage($message);
                // Redirect to list of blogposts
                return $this->redirect()->toRoute('blog');
            }
            else {
                $message = 'Ошибка при сохранении поста';
                $this->flashMessenger()->addErrorMessage($message);
            }
        }
        return array('form' => $form);
    }
	
	public function viewAction()
    {
        // Check if id and blogpost exists.
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Blogpost id doesn\'t set');
            return $this->redirect()->toRoute('blog');
        }
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $post = $objectManager
            ->getRepository('\MyBlog\Entity\BlogPost')
            ->findOneBy(array('id' => $id));
        if (!$post) {
            $this->flashMessenger()->addErrorMessage(sprintf('Blogpost with id %s doesn\'t exists', $id));
            return $this->redirect()->toRoute('blog');
        }
        // Render template.
        $view = new ViewModel(array(
            'post' => $post->getArrayCopy(),
        ));
        return $view;
    }
	
}