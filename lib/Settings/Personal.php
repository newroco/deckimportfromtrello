<?php
namespace OCA\DeckImportFromTrello\Settings;
use OCP\Settings\ISettings;

class Personal implements ISettings {

	/** @var int */
    private $userId;

    /** @var IURLGenerator */
    private $urlGenerator;

    /** @var IL10N */
    private $l;

	/**
	 * constructor of the controller
	 *
     * @param $UserId
     * @param $urlGenerator
	 */
	public function __construct($UserId,
                                IL10N $l,
                                IURLGenerator $urlGenerator) {
		$this->userId = $UserId;
        $this->urlGenerator = $urlGenerator;
        $this->l = $l;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm() {

		return new TemplateResponse('deckimportfromtrello', 'settings/personal', [
			'setting'			=> 'personal',
		], 'blank');
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection() {
		return 'deckimportfromtrello';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * E.g.: 70
	 */
	public function getPriority() {
		return 100;
	}
}
