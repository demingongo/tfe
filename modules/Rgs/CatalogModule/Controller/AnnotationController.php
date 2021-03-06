<?php
namespace Rgs\CatalogModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Rgs\CatalogModule\Entity\Article,
	Rgs\CatalogModule\Entity\Category,
	Rgs\CatalogModule\Entity\State;
use Rgs\UserModule\Entity\User,
	Rgs\UserModule\Entity\Group;
use Novice\Form\Extension\Entity\EntityExtension;
use Symfony\Component\Routing\Annotation\Route; //pour annotation

use Novice\Annotation as NOVICE; //pour annotations de Novice (Template, Service, Assign, AttributeConverter, ...)

use Novice\Templating\Assignor\ErrorMessages;
use Rgs\CatalogModule\Validator\ArticleValidator;

use Firebase\JWT\JWT;

use Novice\Password;


class AnnotationController extends \Novice\BackController
{
	
	/**
	 * @NOVICE\Service
	 */
	private $request_stack;

	private function trans($string, array $array = array(), $domain = null, $lang = null){
		return $this->get('translator')->trans($string, $array, $domain, $lang);
	}
	
	/**
     * @Route("/{homepage}", name="rgs_catalog_index" , defaults={"homepage": "home"}, requirements={"homepage": "home|accueil|index"})
	 * @NOVICE\Template("file:[RgsCatalogModule]index.tpl")
     */
	public function executeIndex($request)
	{
		$em = $this->getDoctrine()->getManager();
		$ads = $em->getRepository('RgsCatalogModule:Advertisement')->findBy(['published' => true], [ 'updatedAt' => 'DESC']);
		return array('ads' => $ads);
	}

	/**
     * @Route("/about", name="rgs_catalog_about")
	 * @NOVICE\Template("file:[RgsCatalogModule]default.tpl")
     */
	public function executeAbout($request)
	{
		return array('title' => "nav.about");
	}

	/**
     * @Route("/contact", name="rgs_catalog_contact")
	 * @NOVICE\Template("file:[RgsCatalogModule]default.tpl")
     */
	public function executeContact($request)
	{
		return array('title' => "nav.contact");
	}
	
	/**
     * @Route("/language_{_locale}", name="rgs_catalog_language" , requirements={"_locale": "en|fr|es"})
     */
	public function executeLanguage($request)
	{	
		$referer = $request->headers->get('referer');
		
		return $this->redirect($referer);
	}
	
	/**
     * @Route("/articles/all/{_page}", 
	 *			name="rgs_catalog_articles_all", 
	 * 			defaults={"_page": 1, "article_name": "attr_conv"}, 
	 * 			requirements={"_page": "\d+"})
	 *
	 *
	 * @NOVICE\AttributeConverter("article_attr", 
	 *						class="Rgs\CatalogModule\Entity\Article",
	 * 						from=NOVICE\AttributeConverter::REQUEST, 
	 *						editor="article_name_editor")
	 *
	 * @NOVICE\Template
     */
	public function executeArticlesAll($_page, Article $article_attr)
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		
		//$this->assign("categories", $this->getDoctrine()->getManager()->getRepository("RgsCatalogModule:Category")->findAll());
		
		$formError = new ErrorMessages();
		
		//if($request->isMethod('POST')){
			$validator = new ArticleValidator();
			$validator->validateRequest($request, $article_attr, $formError);
			//$validator->validate($article_attr, $formError);
			
			// s'il n'y a pas de messages d'erreurs
			if(!$formError->hasError()){
				// traitement + redirect
			}
		//}
		$this->assign($formError);
		
		$where = array();
		
		$filtre = false;
		
		if($request->query->has('category')){
			$req_byCategory = $request->query->get('category');
			if(!empty($req_byCategory)){
				$where['a.category'] = $req_byCategory;
				$filtre = true;
			}
		}
		if($request->query->has('state')){
			$req_byState = $request->query->get('state');
			if(!empty($req_byState)){
				$where['a.state'] = $req_byState;
				$filtre = true;
			}
		}
		
		$limit = 6;
		
		$page = $_page;//$request->attributes->get('_page');
		
		$em = $this->getDoctrine()->getManager();
		$paginator = $em->getRepository('RgsCatalogModule:Article')->getFrontArticles($limit, $page, $where);

		$articles = $paginator->getQuery()->getResult();

		$this->assign("paginator", $paginator);
		
		$this->assign("articles", $articles);
		
		$this->assign("titre", "Tous les articles");
		
		$this->assign("nofilterHref", $this->generateUrl($request->attributes->get('_route')));
		
		$this->assign("filter", $filtre);
		
		//$this->setView("file:[RgsCatalogModule]articlesAll.tpl");
	}

	/**
     * @Route("/articles/{id}/{slug}", 
	 *			name="rgs_catalog_article_details",
	 * 			requirements={"id": "\d+"})
	 *
	 * @NOVICE\Template
     */
	public function executeArticleDetails($id, $slug, $request)
	{	
		$em = $this->getDoctrine()->getManager();
		$article = $em->getRepository('RgsCatalogModule:Article')->findOneBy(['id' => $id, 'published' => true]);

		if(empty($article)){
			return $this->redirect($this->generateUrl("rgs_catalog_articles_all"));
		}
		
		$this->assign("article", $article);

		$this->assign("nofilterHref", $this->generateUrl("rgs_catalog_articles_all"));
	}

	/**
	 * @NOVICE\Assign("categoryWidget", route_names={"rgs_catalog_articles_all","rgs_catalog_article_details"})
	 */
	public function getCategoryWidget(Request $request)
	{
		$byCategory = null;
		
		if($request->query->has('category') && !empty($request->query->get('category'))){
			$byCategory = $request->query->get('category');
		}

		$entityExtCat = new EntityExtension($this->getDoctrine(), array(
		'label' => $this->trans('Category'),
		'class' => 'RgsCatalogModule:Category',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($repository) {
				/*$rsm = new \Doctrine\ORM\Query\ResultSetMappingBuilder($em);
				$sql = $repository->getSQLFrontCategories($rsm);*/
				return $repository->getFrontCategoriesQB();
				//return $em->createNativeQuery($sql, $rsm);
		},
        'name' => 'category',
		'feedback' => true,
		'attributes' => array(
			//'class' => 'selectmenu selectmenu-submit',
			'style' => 'width: 90%',
			'data-placeholder' => $this->trans('All Categories'),
			//'data-theme' => 'classic',
			'data-allow-clear' => 'true',
			//'data-minimum-results-for-search' => 15,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		return $entityExtCat->createField()->setValue($byCategory)->buildWidget();
	}


	/**
	 * @NOVICE\Assign("stateWidget", route_names={"rgs_catalog_articles_all","rgs_catalog_article_details"})
	 */
	public function getStateWidget(Request $request)
	{
		$byState = null;
		
		if($request->query->has('state') && !empty($request->query->get('state'))){
			$byState = $request->query->get('state');
		}

		$entityExtState = new EntityExtension($this->getDoctrine(), array(
		'label' => $this->trans('State'),
		'class' => 'RgsCatalogModule:State',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('e')
					->where('e.published = :p')
					->orderBy('e.name', 'ASC')
					->setParameter('p', State::PUBLISHED);
		},
        'name' => 'state',
		//'feedback' => false,
		'attributes' => array(
			//'class' => 'selectmenu selectmenu-submit',
			'style' => 'width: 90%',
			'data-placeholder' => $this->trans('All States'),
			//'data-theme' => 'classic',
			'data-allow-clear' => 'true',
			'data-minimum-results-for-search' => 3,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		return $entityExtState->createField()->setValue($byState)->buildWidget();
	}
}
