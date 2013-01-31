<?php
/*
 * This file is part of the bach project.
 */

namespace Anph\IndexationBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	public $id;
	//Utiliser ID pour avoir le nom de fichier??
	// => sauvegarder l'extension dans la propriété path, à la place du nom de fichier actuel

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	public $name;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	public $path;

	/**
	 * @Assert\File
	 */
	public $file;

	public $extension;

	public function getAbsolutePath() {
		return null === $this->path ? null
				: $this->getUploadRootDir() . '/' . $this->path;
	}

	public function getWebPath() {
		return null === $this->path ? null
				: $this->getUploadDir() . '/' . $this->path;
	}

	protected function getUploadRootDir() {
		// le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
		return __DIR__ . '/../../../../web/' . $this->getUploadDir();
	}

	protected function getUploadDir() {
		// on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
		// le document/image dans la vue.
		return 'uploads/documents';
	}

	/**
	 * @ORM\PrePersist()
	 * @ORM\PreUpdate()
	 */
	public function preUpload() {
		if (null !== $this->file) {
			 $this->path = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
			 $this->name = $this->getName();
			 $this->extension = $this->file->guessExtension();
		}
	}

	/**
	 * @ORM\PostPersist()
	 * @ORM\PostUpdate()
	 */
	public function upload() {
		if (null === $this->file) {
			return;
		}

		// s'il y a une erreur lors du déplacement du fichier, une exception
		// va automatiquement être lancée par la méthode move(). Cela va empêcher
		// proprement l'entité d'être persistée dans la base de données si
		// erreur il y a
		$this->file->move($this->getUploadRootDir(), $this->path);

		unset($this->file);
	}

	/**
	 * @ORM\PostRemove()
	 */
	public function removeUpload() {
		if ($file = $this->getAbsolutePath()) {
			unlink($file);
		}
	}

	public function getName() {
		return $this->file->getClientOriginalName();
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getFile() {
		return $this->file;
	}

	public function setFile($file) {
		$this->file = $file;
	}

	public function getPath() {
		return $this->path;
	}

	public function setPath($path) {
		$this->path = $path;
	}

	public function getExtension() {
		// return $this->extension = $file->guessExtension();
	}


}
