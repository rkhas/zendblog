<?php
namespace MyBlog\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MyBlog\Entity;
class BlogController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
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
}