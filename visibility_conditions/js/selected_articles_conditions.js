/**
 * @file
 * Shows summary for special articles.
 */

(function ($, Drupal) {

  'use strict';

  function checkSummary(context) {
    // Check if the condition has been selected.
    var selectedCondition = $(context).find('[data-drupal-selector="edit-visibility-selected-article-show"]:checked').length;
    // Check if the negate condition has been selected.
    var selectedNegate = $(context).find('[data-drupal-selector="edit-visibility-selected-article-negate"]:checked').length;

    // Reviews scenarios.
    if (selectedCondition) {
      if (selectedNegate) {
        // Condition and Negation were selected.
        return Drupal.t("The block will be shown in all Articles except the Selected.");
      }

      // Only the condition was selected.
      return Drupal.t("The block will be shown only in Selected Articles.");
    }

    // The condition has not been enabled and is not negated.
    return Drupal.t('The block will be shown in all Articles');
  }

  /**
   * Provide the summary information for the block settings vertical tabs.
   *
   */
  Drupal.behaviors.blockSettingsSummarySelectedArticles = {
    attach: function () {
      // Check if the function drupalSetSummary is available.
      if (jQuery.fn.drupalSetSummary !== undefined) {
        // Add the summary on tab.
        $('[data-drupal-selector="edit-visibility-selected-article"]').drupalSetSummary(checkSummary);
      }
    }
  };

}(jQuery, Drupal));


