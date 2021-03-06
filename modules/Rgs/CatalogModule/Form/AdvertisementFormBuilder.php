<?php
namespace Rgs\CatalogModule\Form;
use Novice\Form\FormBuilder;
use Novice\Form\Field\InputField,
	Novice\Form\Field\RadioField,
	Novice\Form\Field\CheckboxField,
	Novice\Form\Field\SelectField,
	Novice\Form\Field\TextareaField,
	Novice\Form\Field\Prototype,
	Novice\Form\Extension\Securimage\Field\SecurimageField;
use Novice\Form\Validator\MaxLengthValidator,
	Novice\Form\Validator\MinLengthValidator,
	Novice\Form\Validator\NotNullValidator,
	Novice\Form\Validator\EmailValidator,
	Novice\Form\Validator\NonRequiredEmailValidator,
	Novice\Form\Extension\Securimage\Validator\SecurimageValidator;
use Rgs\CatalogModule\Entity\Advertisement;
class AdvertisementFormBuilder extends FormBuilder
{
	public function getName()
	{
		return 'advertisement_form';
	}
  
  public function build()
  {
	$translator = $this->container->get('translator');
	$domain = "RgsCatalogModule";
	$trans = function($string, array $array = array(), $lang = null) use ($translator, $domain){
		return $translator->trans($string, $array, $domain, $lang);
	};
    $this->form->add(new InputField(array(
        'label' => $trans('form.title'),
		'type' => 'text',
        'name' => 'name',
        'maxlength' => 64,
		'pattern' => "^[^ ].{1,}$",
	    'required' => true,
		'control_label' => true,
		//'addon' => '@', //'&euro;',
		/*'right_addon_class' => 'input-group-btn',
		'right_addon' => $dropdown2,//'<input type="checkbox" aria-label="...">',*/ //'<button class="btn btn-default" type="button">Go!</button>',
		'attributes' => array(
			'data-error' => 'something to write',
			'title' => 'Choisir un titre',
		),
		'validators' => array(
    new MaxLengthValidator('Le nom spécifié est trop long (64 caractères maximum)', 64),
    new NotNullValidator('Spécifiez le titre'),)
		)))
		->add(new TextareaField(array(
        'label' => $trans('form.description'),
        'name' => 'description',
		'control_label' => true,
		)))
		->add(new Prototype(array('name' => 'image')))

		->add(new RadioField(array(
        'label' => $trans('form.status'),
		'control_label' => true,
        'name' => 'published',
		 'attributes' => array('class' => 'icheck iradio_line-red'),
		'inline' => 1,
		'required' => true,
		'buttons' => array('Published' => Advertisement::PUBLISHED , 'Unpublished' => Advertisement::NOT_PUBLISHED),
		'validators' => array(
    new NotNullValidator('Choisir le statut'),)
		)))
		->add(new InputField(array(
		'type' => 'url',
        'label' => 'Lien externe (URL)',
		'title' => 'lien vers site le officiel ou autres informations concernant l\'advertisement',
        'name' => 'url',
        'maxlength' => 255,
		'placeholder' => 'http://',
		)))
		->add(new InputField(array(
        'label' => $trans('form.created_at'),
		'type' => 'text',
        'name' => 'created_at_to_string',
		'validation_states' => false,
		//'bootstrap' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)))
		->add(new InputField(array(
        'label' => $trans('form.updated_at'),
		'type' => 'text',
        'name' => 'updated_at_to_string',
		'validation_states' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)))
		->add(new InputField(array(
        'label' => $trans('form.slug'),
		'type' => 'text',
        'name' => 'slug',
		'validation_states' => false,
		'attributes' => array(
			'disabled' => 'disabled'),
		)));
		$options = array(
			'name' => 'image',
			'type' => 1,
			'label' => $trans('form.image'),
			//'input_attributes' => array("required" => "required"),
			'text_remove_btn' => '&times;',
			'akeys' => array(md5('one'), md5('gfk'), md5('theodore')),
		);
		$this->form->addExtension(new \Novice\Form\Extension\Filemanager\FilemanagerExtension('/plugins/filemanager/filemanager', $options));
  }

}