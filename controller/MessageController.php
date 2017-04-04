<?php

/**
*
*	Message Controleur
*
*
* @user Cedric
* @date 2017.03.27
**/
class MessageController
{
	private $db;
	private $var;
	
	private $mDao;	// Forum Dao
	private $mViewC;	// Forum View Controller
	
	
	
	/**
	*	Constructeur
	*
	**/
	public function __construct(CConnexion $db, $var = '')
	{
		$this->db = $db;
		$this->var = $var;
		
		// Instanciation du DAO
		$this->mDao = new MessageDao($this->db);

		// Instanciation de la Vue
		$this->mViewC = new MessageViewController();
		
	}
	
	
	
	/**
	*	Afficher la liste de forums
	*
	**/
	public function afficherListe($sujetId)
	{
		// Récupération de la liste des forums en tableau d'objet
		$messageListe = $this->mDao->getMessages($sujetId);
		
		// Appel le controller de la vue du Forum qui renvoi le code HTML de la liste des forums
		return $this->mViewC->getViewMessageListe($messageListe);

	}
	
	
	
	/**
	*	Afficher le formulaire d'ajout de message
	*		Quand on souhaite créer un nouveau message, un message vide est créé et envoyé a la vue pour remplir le formulaire
	*		Ca permet d'utiliser la même fonction de la vue pour les modifications
	*			action = ajoutMessage
	*			var = 5	// sujetId
	*
	* @param 	$sujetId 	Id du sujet dans lequel le message sera enregistré
	* @param 	$messageId 	Id du message a modifier
	**/
	public function afficherFormulaire($sujetId = 0, $messageId = 0)
	{
		if($sujetId != 0) {
			// Création du Message
			$message = new Message();
			
			// Ajout d'un message
			$message->setSujetId($sujetId);		
			
		} else {
			// Récupération du message via l'id du message transmis
			$message = $this->mDao->getMessage($messageId);
			
			// Modification d'un message
			$message->setId($messageId);			
		}
		
		// Création du formulaire 
		$form = new Form();
		
		// Envoi du Formulaire & du Message à la vue pour créer le Formulaire
		$form = $this->mViewC->getForm($form, $message);
		
		// Envoi du formulaire a la vue pour inclure le formulaire dans un template
		$dataHTML = $this->mViewC->getFormHTML($form);
		
		// Renvoi du code HTML
		return $dataHTML;
	}
	
	
	
	/**
	*	Traitement des Messages recu via le formulaire
	*
	* @Return 	True si tout est OK
	**/
	public function traiterFormulaire()
	{
		$message = new Message();
			
			// Si un ID est présent, c'est une modification
			if( Request::getInstance()->get('id') != '' ) {
				$message->setAuteur(Request::getInstance()->get('id'));
			}
			
			$message->setAuteur(Request::getInstance()->get('auteur'));
			$message->setAuteur(Request::getInstance()->get('auteur'));
			$message->setAcl(Request::getInstance()->get('acl'));
			$message->setTitre(Request::getInstance()->get('titre'));
			$message->setTexte(Request::getInstance()->get('texte'));
			$message->setSujetId(Request::getInstance()->get('sujetId'));
			$message->setAffichage(Request::getInstance()->get('affichage'));

			// Si un ID est présent, c'est une modification
			if( Request::getInstance()->get('id') != '' ) {
				Debug::getInstance()->set('Formulaire', __CLASS__,  __FILE__, __LINE__ , ' Modification Message envoyé : '.$message);
			} else {
				Debug::getInstance()->set('Formulaire', __CLASS__,  __FILE__, __LINE__ , ' Ajout Message envoyé : '.$message);
			}
		
		if( $this->mDao->addMessage($message) ) {
			return true;
		} else {
			return false;
		}
		
	}
	
	
	
	/**
	*	Supression des messages
	*
	* @Return 	Message d'erreur
	**/
	public function supprimer($messageId = 0)
	{
		// Récupération de l'auteur du message
		$auteurId = $this->mDao->getMessageAuteur($messageId);
		
		// Récupération du profil de l'auteur
		// TODO Inclure recherche de l'auteur via la classe User
		// un truc dans ce genre
		
		// Vérification des droits
		if( checkDroit(true) ) {
			// Supression
			$this->mDao->supprimer($messageId);
			
			return 'Message supprimé';
		} else {
			// TODO Gestion des messages d'information
			return 'Droit refusé';
		}
		
		
	}
	

	
	
}


