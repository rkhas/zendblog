<?php
// этот файл является связью между доктриной и бд
namespace MyBlog\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class BlogPost{
	/**
	* @var int
	* @ORM\Id
	* @ORM\Column(type="integer")
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;
	
	/**
	* @var string
	* @ORM\Column(type="string", length=255, nullable=false)
	*/
	protected $title;
	
	/**
	* @var text
	* @ORM\Column(type="text")
	*/
	protected $text;
	
	/**
	* @var int
	* @ORM\Column(type="integer")
	*/
	protected $userId;
	
	/**
	* @var int
	* @ORM\Column(type="integer")
	*/
	protected $created;
	
	/**
	* @var int
	* @ORM\Column(type="int", nullable=true)
	*/
	protected $state;
	
	
	/**
	* Get id.
	*
	* @return int
	*/
	public function getId(){
		return $this->id;
	}
	
	/** 
	*  Set id.
	*
	* @param int $id
	*
	* @return void
	*/
	public function setId($id){
		$this->id = (int) $id;
	}
	
	/**
	* Get title.
	*
	* @return string
	*/
	public function getTitle(){
		return $this->title;
	}
	
	/**
	* Set title.
	*
	* @param string $title
	*
	* @return void
	*/
	public function setTitle($title){
		$this->title = $title;
	}
	
	/**
	* Get text.
	*
	* @return string
	*/
	public function getText(){
		return $this->text;
	}
	
	/**
	* Set0 text.
	*
	* @param string $text
	*
	* @return void
	*/
	public function setText($text){
		$this->text = $text;
	}
	
	/**
	* Get userId.
	*
	* @return int
	*/
	public function getUserId(){
		return $this->userId;
	}
	
	/**
	* Set userId.
	*
	* @param int $userId
	*
	* @return void
	*/
	public function setUserId($userId){
		$this->userId = $userId;
	}
	
	/**
	* Get created.
	*
	* @return int
	*/
	public function getCreated(){
		return $this->created;
	}
	
	/**
	* Set created.
	*
	* @param int $created
	*
	* @return void
	*/
	public function $setCreated($created){
		$this->created = $created
	}
	
	/**
	* Get state.
	*
	* @return int
	*/
	public function getState(){
		return $this->state;
	}
	
	/**
	* Set state.
	*
	* @param int $state
	*
	* @return void
	*/
	public function setState($state){
		$this->state = $state;
	}
	
	/**
	* 
	* Helper function
	* 
	*/	
	public function exchangeArray($data)	{
		foreach($data as $key => $val){
			if(property_exist)
		}
	}
	
	
}


























