<?php namespace Nova\Core\Forms\Data\Interfaces;

use NovaForm, NovaFormSection;

interface SectionRepositoryInterface extends BaseFormRepositoryInterface {

	public function getBoundSections(NovaForm $form);
	public function getUnboundSections(NovaForm $form, array $relations = [], $allSections = false);
	public function reassignSectionContent(NovaFormSection $oldSection, $newSectionId);
	public function removeSectionContent(NovaFormSection $section);

}
