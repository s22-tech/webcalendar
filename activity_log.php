<?php
/**
 * Description:
 *  Display either the "Activity Log" (for events/tasks) or the
 *  "System Log" (entries not associated with an event).
 *
 * Input Parameters:
 *  startid  - specified the id of the first log entry to display
 *  system   - if specified, then view the system log (entries with no
 *             event id associated with them) rather than the event log.
 *
 * Security:
 *  User must be an admin user
 *  AND, if user access control is enabled, they must have access to
 *  activity logs. (This is because users may see event details
 *  for other groups that they are not supposed to have access to.)
 */
require_once 'includes/init.php';

if ( ! $is_admin || ( access_is_enabled()
    && ! access_can_access_function( ACCESS_ACTIVITY_LOG ) ) )
  die_miserable_death ( print_not_auth() );

$eventsStr = translate ( 'Events' );
$nextStr = translate ( 'Next' );
$prevStr = translate ( 'Previous' );

$PAGE_SIZE = 25; // Number of entries to show at once.
$startid = getValue ( 'startid', '-?[0-9]+', true );
$sys = ( $is_admin && getGetValue ( 'system' ) != '' );

print_header();
echo generate_activity_log( '', $sys, $startid ) . '
    <div class="navigation">'
// Go BACK in time.
  . ( ! empty ( $nextpage ) ? '
      <a class="prev" href="activity_log.php?startid=' . $nextpage
    . ( $sys ? '&amp;system=1' : '' ) . '">' . $prevStr . '&nbsp;' . $PAGE_SIZE
    . '&nbsp;' . $eventsStr . '</a>' : '' );

if ( ! empty ( $startid ) ) {
  $previd = $startid + $PAGE_SIZE;
  $res = dbi_execute ( 'SELECT MAX( cal_log_id ) FROM webcal_entry_log' );
  if ( $res ) {
    if ( $row = dbi_fetch_row ( $res ) )
      // Go FORWARD in time.
      echo '
      <a class="next" href="activity_log.php' . ( $row[0] <= $previd
          ? ( $sys ? '?system=1' : '' )
          : '?startid=' . $previd . ( $sys ? '&amp;system=1' : '' ) ) . '">'
        . $nextStr . '&nbsp;' . $PAGE_SIZE . '&nbsp;' . $eventsStr . '</a><br>';

    dbi_free_result ( $res );
  }
}
echo '
    </div>' . print_trailer();

?>
