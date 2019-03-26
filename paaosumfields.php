<?php

require_once 'paaosumfields.civix.php';
use CRM_Paaosumfields_ExtensionUtil as E;

/**
 * Helper function to manage settings; aims to ease maintenance where hardcoded
 * values are used.
 */
function _paaosumfields_get_setting($setting) {
  static $settings = array(
    'membership_ftid' => 2,
    'membership_in_training_mtid' => 4,
  );

  return CRM_Utils_Array::value($setting, $settings);
}

/**
 * Implements hook_civicrm_sumfields_definitions().
 *
 */
function paaosumfields_civicrm_sumfields_definitions(&$custom) {

  // Define a new optgroup fieldset, to contain our PAAO custom fields
  // and options.
  $custom['optgroups']['paao_membership'] = array(
    'title' => 'PAAO Membership Fields',
    'fieldset' => 'PAAO',
    'component' => 'CiviMember',
  );

  $custom['fields']['membership_payments_100'] = array(
    'label' => E::ts('Count of Membership Payments $100 or more'),
    'data_type' => 'Int',
    'html_type' => 'Text',
    'text_length' => '32',
    'trigger_sql' => '(
      SELECT
        count(*)
      FROM civicrm_contribution
      WHERE
        contact_id = NEW.contact_id
        AND contribution_status_id = 1
        AND financial_type_id = '. _paaosumfields_get_setting('membership_ftid') . '
        AND total_amount >= 100
    )',
    'trigger_table' => 'civicrm_contribution',
    'optgroup' => 'paao_membership',
  );

  $custom['fields']['has_five_membership_payments'] = array(
    'label' => E::ts('Has five recent consecutive membership payments $100 or more'),
    'data_type' => 'Boolean',
    'html_type' => 'Radio',
    'is_searchable' => 1,
    'is_searchable_range' => 0,
    'trigger_sql' => "(
      SELECT
        -- compare a sorted, comma-separated list of distinct years against
        -- two possible valid sets: all years including the current year and 4
        -- most recent previous years; and all years excluding current year and
        -- including the 5 most recent previous years.  If the list of unique
        -- years matches either of those, we know we have contributions in
        -- 5 most recent consecutive years for a period optionally including the
        -- current year.
        GROUP_CONCAT(DISTINCT(YEAR(receive_date)) ORDER BY receive_date ASC SEPARATOR ',') IN (
        CONCAT(
          YEAR(CURDATE()) - 4, ',',
          YEAR(CURDATE()) - 3, ',',
          YEAR(CURDATE()) - 2, ',',
          YEAR(CURDATE()) - 1, ',',
          YEAR(CURDATE())
        ),
        concat (
          YEAR(CURDATE()) - 5, ',',
          YEAR(CURDATE()) - 4, ',',
          YEAR(CURDATE()) - 3, ',',
          YEAR(CURDATE()) - 2, ',',
          YEAR(CURDATE()) - 1
        ))
      FROM
        civicrm_contribution
      WHERE
        contact_id = NEW.contact_id
        AND contribution_status_id = 1
        AND financial_type_id = ". _paaosumfields_get_setting('membership_ftid') . "
        AND total_amount >= 100
        AND YEAR(receive_date) >= YEAR(CURDATE()) - 5
      GROUP BY contact_id
    )",
    'trigger_table' => 'civicrm_contribution',
    'optgroup' => 'paao_membership',
  );

  $custom['fields']['membership_member_in_training'] = array(
    'label' => E::ts('Count of of "Member-In-Training" memberships'),
    'data_type' => 'Int',
    'html_type' => 'Text',
    'text_length' => '32',
    'trigger_sql' => '(
      SELECT
        count(*)
      FROM civicrm_membership
      WHERE
        contact_id = NEW.contact_id
        AND membership_type_id = '. _paaosumfields_get_setting('membership_in_training_mtid') . '
    )',
    'trigger_table' => 'civicrm_membership',
    'optgroup' => 'paao_membership',
  );
}


/**
 * Implements hook_civicrm_postProcess().
 */
function paaosumfields_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_Sumfields_Form_SumFields') {
    // Problem: SummaryFields is not saving some properties correctly
    // for Yes/No summary fields
    // Solution: here we get the ID of our Yes/No summary field and then use
    // the api to change field properties.
    $customFieldParameters = sumfields_get_setting('custom_field_parameters');
    if ($field = CRM_Utils_Array::value('has_five_membership_payments', $customFieldParameters) ){
      $result = civicrm_api3('CustomField', 'create', array(
        'id' => CRM_Utils_Array::value('id', $field),
        'is_search_range' => 0,
        'is_searchable' => 1,
        'is_view' => 1,
      ));
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function paaosumfields_civicrm_config(&$config) {
  _paaosumfields_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function paaosumfields_civicrm_xmlMenu(&$files) {
  _paaosumfields_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function paaosumfields_civicrm_install() {
  _paaosumfields_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function paaosumfields_civicrm_postInstall() {
  _paaosumfields_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function paaosumfields_civicrm_uninstall() {
  _paaosumfields_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function paaosumfields_civicrm_enable() {
  _paaosumfields_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function paaosumfields_civicrm_disable() {
  _paaosumfields_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function paaosumfields_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _paaosumfields_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function paaosumfields_civicrm_managed(&$entities) {
  _paaosumfields_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function paaosumfields_civicrm_caseTypes(&$caseTypes) {
  _paaosumfields_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function paaosumfields_civicrm_angularModules(&$angularModules) {
  _paaosumfields_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function paaosumfields_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _paaosumfields_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function paaosumfields_civicrm_entityTypes(&$entityTypes) {
  _paaosumfields_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function paaosumfields_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function paaosumfields_civicrm_navigationMenu(&$menu) {
  _paaosumfields_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _paaosumfields_civix_navigationMenu($menu);
} // */
