<?php
/**
 * Page Description:
 *   Serves as the home page for administrative functions.  It's useful when the
 *   top menu has been disabled in the System Settings.
 * Input Parameters:
 *   None
 * Security:
 *   Users will see different options available on this page.
 */
require_once 'includes/init.php';

define ( 'COLUMNS', 3 );

$accessEnabled = access_is_enabled();
$assistStr = translate ( 'Assistants' );
$prefStr = translate ( 'Preferences' );
$links = $names = [];
/* Disabled for now...will move to menu when working properly
if ( $is_admin && ! empty ( $SERVER_URL ) &&
    access_can_access_function ( ACCESS_SYSTEM_SETTINGS ) ) {
  $names[] = translate ( 'Control Panel' );
  $links[] = 'controlpanel.php';
}
*/
if ( $is_nonuser_admin ) {
  if ( ! $accessEnabled || access_can_access_function( ACCESS_PREFERENCES ) ) {
    $names[] = $prefStr;
    $links[] = 'pref.php?user=' . $user;
  }

  if ( $single_user != 'Y' ) {
    if ( ! $accessEnabled || access_can_access_function( ACCESS_ASSISTANTS ) ) {
      $names[] = $assistStr;
      $links[] = 'assistant_edit.php?user=' . $user;
    }
  }
} else {
  if ( ( $is_admin && ! $accessEnabled )
      || ( $accessEnabled
      && access_can_access_function( ACCESS_SYSTEM_SETTINGS ) ) ) {
    $names[] = translate ( 'System Settings' );
    $links[] = 'admin.php';
  }

  if ( ! $accessEnabled || access_can_access_function( ACCESS_PREFERENCES ) ) {
    $names[] = $prefStr;
    $links[] = 'pref.php';
  }

  $names[] = ( $is_admin ? translate ( 'Users' ) : translate ( 'Account' ) );
  $links[] = 'user_mgmt.php';

  if ($GROUPS_ENABLED == 'Y' && $is_admin) {
    $names[] = translate ( 'Groups' );
    $links[] = 'groups.php';
  }

  if ( $REMOTES_ENABLED == 'Y' && (( $is_admin && ! $accessEnabled )
      || ( $accessEnabled
      && access_can_access_function(ACCESS_IMPORT)))) {
    $names[] = translate ( 'Remote Calendars' );
    $links[] = 'remotecal_mgmt.php';
  }

  if ( $NONUSER_ENABLED == 'Y' && (( $is_admin && ! $accessEnabled )
      || ( $accessEnabled
      && access_can_access_function(ACCESS_USER_MANAGEMENT)))) {
    $names[] = translate ( 'Resource Calendars' );
    $links[] = 'resourcecal_mgmt.php';
  }

  if ( $accessEnabled
      && access_can_access_function( ACCESS_ACCESS_MANAGEMENT ) ) {
    $names[] = translate ( 'User Access Control' );
    $links[] = 'access.php';
  }

  if ( $single_user != 'Y' ) {
    if ( ! $accessEnabled || access_can_access_function( ACCESS_ASSISTANTS ) ) {
      $names[] = $assistStr;
      $links[] = 'assistant_edit.php';
    }
  }

  if ( $CATEGORIES_ENABLED == 'Y' ) {
    if ( ! $accessEnabled
        || access_can_access_function( ACCESS_CATEGORY_MANAGEMENT ) ) {
      $names[] = translate ( 'Categories' );
      $links[] = 'category.php';
    }
  }

  if ( ! $accessEnabled
      || access_can_access_function( ACCESS_VIEW_MANAGEMENT ) ) {
    $names[] = translate ( 'Views' );
    $links[] = 'views.php';
  }

  if ( ! $accessEnabled || access_can_access_function( ACCESS_LAYERS ) ) {
    $names[] = translate ( 'Layers' );
    $links[] = 'layers.php';
  }

  if ( $REPORTS_ENABLED == 'Y'
      && ( ! $accessEnabled || access_can_access_function( ACCESS_REPORT ) ) ) {
    $names[] = translate ( 'Reports' );
    $links[] = 'report.php';
  }

  if ( $is_admin ) {
    $names[] = translate ( 'Delete Events' );
    $links[] = 'purge.php';
  }
  /*
 This Activity Log link shows ALL activity for ALL events, so you really need
 to be an admin user for this. Enabling "Activity Log" in UAC just gives you
 access to the log for your _own_ events or other events you have access to.
 */
  if ( $is_admin
      && ( ! $accessEnabled
      || access_can_access_function( ACCESS_ACTIVITY_LOG ) ) ) {
    $names[] = translate ( 'Activity Log' );
    $links[] = 'activity_log.php';

    $names[] = translate ( 'System Log' );
    $links[] = 'activity_log.php?system=1';
  }

  if ( ( $is_admin || ! $accessEnabled )
      || ( $accessEnabled
      && access_can_access_function( ACCESS_SECURITY_AUDIT ) ) ) {
    $names[] = translate ( 'Security Audit' );
    $links[] = 'security_audit.php';
  }

  if ( $is_admin && ! empty ( $PUBLIC_ACCESS ) && $PUBLIC_ACCESS == 'Y' ) {
    $names[] = translate ( 'Public Preferences' );
    $links[] = 'pref.php?public=1';
  }

  if ( $is_admin && ! empty( $PUBLIC_ACCESS ) && $PUBLIC_ACCESS == 'Y'
      && $PUBLIC_ACCESS_CAN_ADD == 'Y'
      && $PUBLIC_ACCESS_ADD_NEEDS_APPROVAL == 'Y' ) {
    $names[] = translate ( 'Unapproved Public Events' );
    $links[] = 'list_unapproved.php?user=__public__';
  }
}

$_SESSION['webcal_tmp_login'] = 'SheIsA1Fine!';

print_header();

echo '
    <h2>' . translate( 'Administrative Tools' ) . '</h2>
    <table>';

for ( $i = 0, $cnt = count( $names ); $i < $cnt; $i++ ) {
  $empLink = empty( $links[$i] );
  echo ( $i % COLUMNS == 0 ? '
      <tr>' : '' ) . '
        <td>' . ( $empLink ? '' : '<a href="' . $links[$i] . '">' )
   . $names[$i] . ( $empLink ? '' : '</a>' ) . '</td>'
   . ( $i % COLUMNS == COLUMNS - 1 ? '
      </tr>' : '' );
}

while ( $i % COLUMNS != 0 ) {
  echo '
      <td>&nbsp;</td>';
  $i++;
}

echo '
      </tr>
    </table>
    ' . print_trailer();

?>
