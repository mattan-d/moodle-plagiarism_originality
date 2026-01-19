/**
 * JS functions.
 *
 * @package
 * @copyright  2023 mattandor <mattan@centricapp.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
  return {
    unsetLimit: function() {
      var attemptreopenmethod = $('#id_attemptreopenmethod'), maxattempts = $('#id_maxattempts');

      attemptreopenmethod.find('option').removeAttr('disabled');
      maxattempts.find('option').removeAttr('disabled');
    }, setLimit: function() {
      var attemptreopenmethod = $('#id_attemptreopenmethod'), maxattempts = $('#id_maxattempts');

      attemptreopenmethod.find('option').attr('disabled', true);
      attemptreopenmethod.find('[value=manual]').prop('selected', true).removeAttr('disabled');
      attemptreopenmethod.change().click();

      maxattempts.find('option').attr('disabled', true);
      maxattempts.find('[value=2]').prop('selected', true).removeAttr('disabled');
      maxattempts.change().click();
    }, submissions: function() {

      var that = this;
      var originalityDropdown = $('#id_originality_use'), originalityDropdownGhostwriter = $('#id_originality_use_ghostwriter'),
          submitBtn = $('input[id=id_submitbutton], input[id=id_submitbutton2]'), originalityStatus = originalityDropdown.val(),
          allowFilesGhostwriter = '*';//'.rtf,.docx,.doc';

      // Ghostwriter Init
      var el = $('#fitem_fgroup_id_assignsubmission_file_filetypes'), ghostinit = setInterval(function() {
        {
          if (originalityDropdownGhostwriter.val() == 1) {
            el.find('[type=text]').val(allowFilesGhostwriter).attr('readonly', true);
            el.find('[type=button]').attr('disabled', true);
          }
          if (el.find('[type=button]').length > 0) {
            clearInterval(ghostinit);
          }
        }
      }, 2000);

      originalityDropdownGhostwriter.change(function() {
        var base = $(this), status = base.val();

        if (status == 1) {
          //el.find('[type=text]').val(allowFilesGhostwriter).attr('readonly', true);
          el.find('[type=button]').attr('disabled', true);
          // Notify(true);
        } else {
          //el.find('[type=text]').removeAttr('readonly');
          el.find('#id_assignsubmission_file_filetypes').val('*');
          el.find('[type=button]').removeAttr('disabled');
        }
      });

      if (originalityStatus == 2) {
        that.setLimit();
      }

      originalityDropdown.change(function() {
        var base = $(this), status = base.val(), msg = $('#assignment_has_submissions_notifications');

        if (status == 2) {
          that.setLimit();
        } else {
          that.unsetLimit();
        }

        if (status == 1) {
          $('.originality-message').toggle(true);

          if (msg.length) {
            require(['core/notification'], function(notification) {
              notification.alert('Notification', msg.html(), 'OK');
            });
          }
        }
      });
      submitBtn.on('click', function(e) {
        var isOnlinetext = $('#id_assignsubmission_onlinetext_enabled').is(':checked'),
            isGwon = $('#id_originality_use_ghostwriter').val();

        if (isOnlinetext && isGwon == 1 && $('.originality-gw-notify').length) {
          e.preventDefault();
          require(['core/notification'], function(notification) {
            notification.alert('', $('.core-notification msg').text(), $('.core-notification btn').text());
          });
        }
      });

      if ($('#originality-checkbox').length) {
        $('#region-main input[id=id_submitbutton]').click(function(e) {
          var isChecked = $('#originality-checkbox').is(':checked');
          if (!isChecked) {
            e.preventDefault();
            require(['core/notification'], function(notification) {
              notification.alert('Originality Warning', $('.core-notification msg').text(), $('.core-notification btn').text());
            });
            return;
          }
        });

        $('#region-main #originality-checkbox').click(function() {
          var isChecked = $(this).is(':checked');
          $('.originality-checkbox-form').remove();
          $('.mform').
              prepend('<input type="hidden" value="' + isChecked + '" ' +
                  'class="originality-checkbox-form" name="originality-checkbox-form">');
        });
      }
    },
  };
});
