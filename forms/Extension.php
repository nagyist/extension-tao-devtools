<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDevTools\forms;

/**
 * Create a form to add extensions
 *
 * @access public
 * @author Joel Bout <joel@taotesting.com>
 * @package taoDevTools
 */
class Extension extends \tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Initialize the form
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initForm()
    {
        (isset($this->options['name'])) ? $name = $this->options['name'] : $name = '';
        if (empty($name)) {
            $name = 'form_' . (count(self::$forms) + 1);
        }
        unset($this->options['name']);
            
        $this->form = \tao_helpers_form_FormFactory::getForm($name, $this->options);
        
        //create action in toolbar
        $createElt = \tao_helpers_form_FormFactory::getElement('create', 'Free');
        $createElt->setValue('<button class="btn-info" type="submit" id="addButton">' . \tao_helpers_Icon::iconAdd() . __('Create') . '</button>');
        $this->form->setActions([], 'top');
        $this->form->setActions([$createElt], 'bottom');
    }

    /**
     * Initialize the form elements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {
        $idElt = \tao_helpers_form_FormFactory::getElement('name', 'Textbox');
        $idElt->setDescription(__('Identifier'));
        $idElt->addValidator(\tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        $idElt->addValidator(\tao_helpers_form_FormFactory::getValidator('AlphaNum'));
        $this->form->addElement($idElt);
        
        $authorElt = \tao_helpers_form_FormFactory::getElement('author', 'Textbox');
        $authorElt->setDescription(__('Author'));
        $authValid = \tao_helpers_form_FormFactory::getValidator('NotEmpty');
        $authorElt->addValidator($authValid);
        $authorElt->setValue('Open Assessment Technologies SA');
        $this->form->addElement($authorElt);
        
        $nsElt = \tao_helpers_form_FormFactory::getElement('authorNs', 'Textbox');
        $nsElt->setDescription('Author namespace');
        $nsElt->addValidator(\tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        $nsElt->addValidator(\tao_helpers_form_FormFactory::getValidator('AlphaNum'));
        $nsElt->setValue('oat');
        $this->form->addElement($nsElt);
        
        $licenseElt = \tao_helpers_form_FormFactory::getElement('license', 'Textbox');
        $licenseElt->setDescription(__('License'));
        $licenseElt->setValue('GPL-2.0');
        $this->form->addElement($licenseElt);
        
        $idElt = \tao_helpers_form_FormFactory::getElement('label', 'Textbox');
        $idElt->setDescription(__('Label'));
        $idElt->addValidator(\tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        $this->form->addElement($idElt);

        $descElt = \tao_helpers_form_FormFactory::getElement('description', 'Textarea');
        $descElt->setDescription(__('Description'));
        $this->form->addElement($descElt);
        
        $extIds = [];
        foreach (\common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $ext) {
            $extIds[$ext->getId()] = $ext->getId();
        }
        $depElt = \tao_helpers_form_FormFactory::getElement('dependencies', 'Checkbox');
        $depElt->setDescription(__('Depends on'));
        $depElt->setOptions($extIds);
        $depElt->setValue('tao');
        $this->form->addElement($depElt);
        
        $chainingElt = \tao_helpers_form_FormFactory::getElement('samples', 'Checkbox');
        $chainingElt->setDescription(__('Samples'));
        $chainingElt->setOptions([
            'structure' =>  __('sample structure')
            ,'theme' => __('custom theme')
            ,'model' => __('sample model (todo)')
            ,'rdf' => __('sample rdf install (todo)')
            ,'install' => __('sample post install script (todo)')
            ,'uninstall' => __('sample uninstall script (todo)')
            ,'entry' => __('sample entry point (todo)')
            ,'itemmodel' => __('sample item model (todo)')
            ,'testmodel' => __('sample test model todo)')
            ,'deliverymodel' => __('sample delivery model (todo)')
        ]);
        $chainingElt->setValue('structure');
        $this->form->addElement($chainingElt);
    }
}
